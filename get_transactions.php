<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'ecommerce';

function writeLog($message) {
    $logFile = "transactions_debug.log";
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

writeLog("Starting transaction fetch");

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    writeLog("Database connection failed: " . $conn->connect_error);
    echo json_encode(['error' => 'Connection failed']);
    exit();
}

writeLog("Database connected successfully");

$query = "SELECT transaction_id, phone_number, amount, payment_date, status 
          FROM mpesa_payments 
          ORDER BY payment_date DESC 
          LIMIT 10";

writeLog("Executing query: $query");

$result = $conn->query($query);

if ($result === false) {
    writeLog("Query failed: " . $conn->error);
    echo json_encode(['error' => 'Query failed']);
    exit();
}

$transactions = [];

if ($result->num_rows > 0) {
    writeLog("Found " . $result->num_rows . " transactions");
    while($row = $result->fetch_assoc()) {
        $transactions[] = [
            'transaction_id' => $row['transaction_id'],
            'phone_number' => $row['phone_number'],
            'amount' => $row['amount'],
            'payment_date' => $row['payment_date'],
            'status' => $row['status']
        ];
    }
} else {
    writeLog("No transactions found");
}

$conn->close();
writeLog("Database connection closed");

header('Content-Type: application/json');
writeLog("Sending response: " . json_encode($transactions));
echo json_encode($transactions);
?>
