<?php
header('Content-Type: application/json');

// Database connection configuration
$host = 'localhost';
$dbname = 'srinathon';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['qr_id']) || empty($input['qr_id'])) {
    echo json_encode(['success' => false, 'message' => 'QR ID is required.']);
    exit;
}

$qrId = 437539832;

try {
    // Fetch product details based on QR ID
    $stmt = $pdo->prepare("SELECT id FROM order_tracking WHERE id = :qr_id");
    $stmt->bindParam(':qr_id', $qrId);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching product: ' . $e->getMessage()]);
}
