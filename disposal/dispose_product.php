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



$qrId = 437539832;
$userId = 3;

try {
    // Check if product exists
    $stmt = $pdo->prepare("SELECT id FROM order_tracking WHERE qr_data = :qr_id");
    $stmt->bindParam(':qr_id', $qrId);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    $productId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

    // Dispose of the product
    $stmt = $pdo->prepare("INSERT INTO disposals (product_id, user_id, disposed_at) VALUES (:product_id, :user_id, NOW())");
    $stmt->bindParam(':product_id', $productId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    // Delete the product record
    $stmt = $pdo->prepare("DELETE FROM order_tracking WHERE id = :id");
    $stmt->bindParam(':id', $productId);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Product disposed of successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error disposing of product: ' . $e->getMessage()]);
}
