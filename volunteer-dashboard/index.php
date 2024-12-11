<?php
session_start();
require 'vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Shared\Converter;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if (empty($_SESSION['id']) || empty($_SESSION['mobile']) || empty($_SESSION['email'])) {
    header('location: ../login');
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=srinathon', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
if (isset($_POST['buy_product'])) {
    $productId = $_POST['product_id'];
    $accountNumber = $_POST['account_number'];
    $password = $_POST['password'];
    $userId = $_SESSION['id'];
    $price = $_POST['price']; // Assign price to a variable for consistency

    try {
        // Validate account credentials and check balance
        $stmt = $pdo->prepare("SELECT * FROM bank WHERE account_number = ? AND password = ?");
        $stmt->execute([$accountNumber, $password]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($account && $account['balance'] >= $price) {
            // Generate order ID
            $orderId = 437539832;

            // Create QR code folder if not exists
            $qrCodeFolder = './qrcodes';
            if (!file_exists($qrCodeFolder)) {
                mkdir($qrCodeFolder, 0777, true);
            }

            // Prepare initial QR code data
            $qrPath = "$qrCodeFolder/qrcode_$orderId.png";

            // Generate QR code data with all information
            $qrCodeData = $orderId;

            $qrCode = new QrCode($qrCodeData);
            $writer = new PngWriter();
            $qrImage = $writer->write($qrCode)->getString();
            file_put_contents($qrPath, $qrImage);

            // Save order and QR data in the database
            $stmt = $pdo->prepare("INSERT INTO order_tracking (id, user_id, product_id, qr_data, qr_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$orderId, $userId, $productId, $qrCodeData, $qrPath]);

            // Update product quantity
            $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - 1 WHERE id = ?");
            $stmt->execute([$productId]);

            // Deduct balance from the bank account
            $stmt = $pdo->prepare("UPDATE bank SET balance = balance - ? WHERE account_number = ?");
            $stmt->execute([$price, $accountNumber]);

            echo "<script>alert('Order placed successfully!');</script>";
        } else {
            echo "<script>alert('Insufficient balance or incorrect credentials.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error placing order: " . addslashes($e->getMessage()) . "');</script>";
    }
}


if (isset($_POST['delete_order'])) {
    $orderId = $_POST['order_id'];

    try {
        $stmt = $pdo->prepare("SELECT qr_path, product_id FROM order_tracking WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            if (file_exists($order['qr_path'])) {
                unlink($order['qr_path']);
            }

            $stmt = $pdo->prepare("DELETE FROM order_tracking WHERE id = ?");
            $stmt->execute([$orderId]);

            $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + 1 WHERE id = ?");
            $stmt->execute([$order['product_id']]);

            echo "<script>alert('Order deleted successfully!');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting order: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoCart Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 bg-dark text-white min-vh-100 p-3">
            <h2 class="text-center mb-4">EcoCart</h2>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a href="#" class="nav-link text-white"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
                <li class="nav-item mb-2"><a href="#profile" class="nav-link text-white"><i class="fa-solid fa-user me-2"></i> Profile</a></li>
                <li class="nav-item"><a href="./logout.php" class="nav-link text-white"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
            </ul>
        </nav>
        <main class="col-md-10 p-4">
            <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
            <div id="profile" class="card my-4">
                <div class="card-header">
                    <h4>Your Profile</h4>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Mobile:</strong> <?php echo htmlspecialchars($user['mobile']); ?></p>
                </div>
            </div>
            <div class="card my-4">
                <div class="card-header">
                    <h4>My Orders</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Order ID</th>
                                <th>QR Code</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $stmt = $pdo->prepare("SELECT ot.id AS order_id, p.name AS product_name, ot.qr_path FROM order_tracking ot JOIN products p ON ot.product_id = p.id WHERE ot.user_id = ?");
                            $stmt->execute([$_SESSION['id']]);
                            while ($order = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><img src="<?php echo htmlspecialchars($order['qr_path']); ?>" alt="QR Code" width="100"></td>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                            <button type="submit" name="delete_order" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card my-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Available Products</h4>
                    <form class="d-flex" method="get">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
                <div class="card-body row">
                    <?php
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    $query = "SELECT * FROM products WHERE quantity > 0";

                    if (!empty($search)) {
                        $query .= " AND (name LIKE :search OR description LIKE :search)";
                        $stmt = $pdo->prepare($query);
                        $stmt->execute(['search' => '%' . $search . '%']);
                    } else {
                        $stmt = $pdo->query($query);
                    }

                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($products) > 0) {
                        foreach ($products as $product) {
                            ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-lg border-0">
                                    <div class="card-body text-center">
                                        <img src="<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid rounded mb-3">
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                        <p><strong>Price:</strong> <?php echo htmlspecialchars($product['price']); ?></p>
                                        <p><strong>Stock:</strong> <?php echo htmlspecialchars($product['quantity']); ?></p>
                                        <form method="post">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                            <div class="mb-2">
                                                <input type="text" name="account_number" placeholder="Account Number" class="form-control">
                                            </div>
                                            <div class="mb-2">
                                                <input type="password" name="password" placeholder="Password" class="form-control">
                                            </div>
                                            <button type="submit" name="buy_product" class="btn btn-primary w-100">Buy Now</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p class='text-center text-muted'>No products found.</p>";
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>
</div>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
