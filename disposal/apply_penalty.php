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

if (!isset($input['qr_id']) || empty($input['qr_id']) || 
    !isset($input['account_number']) || empty($input['account_number']) || 
    !isset($input['password']) || empty($input['password']) || 
    !isset($input['penalty_amount']) || empty($input['penalty_amount'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

$qrId = $input['qr_id'];
$accountNumber = $input['account_number'];
$password = $input['password'];
$penaltyAmount = $input['penalty_amount'];

try {
    // Verify account credentials and check balance
    $stmt = $pdo->prepare("SELECT * FROM bank WHERE account_number = :account_number AND password = :password");
    $stmt->bindParam(':account_number', $accountNumber);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        echo json_encode(['success' => false, 'message' => 'Invalid account credentials.']);
        exit;
    }

    if ($account['balance'] < $penaltyAmount) {
        echo json_encode(['success' => false, 'message' => 'Insufficient balance.']);
        exit;
    }

    // Deduct the penalty amount from the account
    $updateStm = $pdo->prepare("UPDATE bank SET balance = balance - :amount WHERE account_number = :account_number");
    $updateStm->bindParam(':amount', $penaltyAmount);
    $updateStm->bindParam(':account_number', $accountNumber);
    $updateStm->execute();

    // Record the penalty
    $userId = $account['id']; // Assuming 'id' is the user ID in the `bank` table
    $penaltyStmt = $pdo->prepare("INSERT INTO penalties (qr_id, user_id, amount, created_at) VALUES (:qr_id, :user_id, :amount, NOW())");
    $penaltyStmt->bindParam(':qr_id', $qrId);
    $penaltyStmt->bindParam(':user_id', $userId);
    $penaltyStmt->bindParam(':amount', $penaltyAmount);
    $penaltyStmt->execute();

    // Insert a disposal record
    $disposalStmt = $pdo->prepare("INSERT INTO disposals (product_id, user_id, disposed_at) VALUES (:product_id, :user_id, NOW())");
    $disposalStmt->bindParam(':product_id', $qrId); // Assuming `qr_id` is linked to `product_id`
    $disposalStmt->bindParam(':user_id', $userId);
    $disposalStmt->execute();

    // Delete the product record from `order_tracking`
    $deleteStmt = $pdo->prepare("DELETE FROM order_tracking WHERE id = :id");
    $deleteStmt->bindParam(':id', $qrId);
    $deleteStmt->execute();

    echo json_encode(['success' => true, 'message' => 'Penalty applied and product disposed successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error applying penalty: ' . $e->getMessage()]);
}
?>
