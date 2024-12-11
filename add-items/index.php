<?php
session_start();
if (empty($_SESSION['id']) || empty($_SESSION['mobile']) || empty($_SESSION['email'])) {
    header('location: ../login');
    exit();
}
require_once '../db/connect.php';

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

$error_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $productName = htmlspecialchars(trim($_POST['productName']));
    $productDescription = htmlspecialchars(trim($_POST['productDescription']));
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
    $expiryDate = $_POST['expiryDate'];

    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['productImage']['tmp_name'];
        $fileName = $_FILES['productImage']['name'];
        $fileSize = $_FILES['productImage']['size'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedFileTypes = ['jpg', 'jpeg', 'png'];
        if (!in_array($fileExtension, $allowedFileTypes)) {
            die("Only JPG, JPEG, and PNG files are allowed.");
        }
        if ($fileSize > 2 * 1024 * 1024) {
            die("File size exceeds the 2MB limit.");
        }
        $uploadDirectory = '../uploads/product_images/';
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }
        $newFileName = uniqid() . '.' . $fileExtension;
        $destination = $uploadDirectory . $newFileName;
        if (!move_uploaded_file($fileTmpPath, $destination)) {
            die("Error uploading the file. Please try again.");
        }
    } else {
        die("Please upload a valid product image.");
    }

    if (!$conn) {
        die("Database connection error.");
    }

    try {
        $stmt = $conn->prepare(
            "INSERT INTO products (company_id, name, description, price, quantity, image_path, expiry_date) 
             VALUES (:company_id, :name, :description, :price, :quantity, :image_path, :expiry_date)"
        );
        if ($stmt->execute([
            ':company_id' => $_SESSION['id'],
            ':name' => $productName,
            ':description' => $productDescription,
            ':price' => $price,
            ':quantity' => $quantity,
            ':image_path' => $destination,
            ':expiry_date' => $expiryDate,
        ])) {
            header("Location: ./success");
            exit;
        } else {
            die("Error saving product. Please try again later.");
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        die("Error saving product. Please try again later.");
    }
}
?>

<?php
$page_title = "Add Product";
include '../header/index.php';
?>
<header>
    <style>
        .add-product-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
        }
        .add-product-card h3 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</header>
<body>
    <div class="add-product-card d-block mx-auto my-5">
        <h3>Add Product</h3>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="productImage" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="productImage" name="productImage" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="productName" required>
            </div>
            <div class="mb-3">
                <label for="productDescription" class="form-label">Product Description</label>
                <textarea class="form-control" id="productDescription" name="productDescription" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="mb-3">
                <label for="expiryDate" class="form-label">Quantity</label>
                <input type="date" class="form-control" id="expiryDate" name="expiryDate" required>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <button type="submit" class="btn btn-primary w-100">Add Product</button>
        </form>
    </div>

<?php
include '../footer/index.php';
?>
