<?php
header('Content-Type: application/json');


$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'ecommerce';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}


$data = json_decode(file_get_contents('php://input'));
$merchantRequestId = $conn->real_escape_string($data->merchantRequestId);
$checkoutRequestId = $conn->real_escape_string($data->checkoutRequestId);


$query = "SELECT status FROM mpesa_payments 
          WHERE merchant_request_id = ? 
          AND checkout_request_id = ?
          ORDER BY payment_date DESC LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $merchantRequestId, $checkoutRequestId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['status' => $row['status']]);
} else {
    echo json_encode(['status' => 'pending']);
}

$stmt->close();
$conn->close();