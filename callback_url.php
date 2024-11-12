<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'ecommerce';

function writeLog($message) {
    $logFile = "mpesa_debug.log";
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

writeLog("Callback script started");

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    writeLog("Database connection failed: " . $conn->connect_error);
    exit();
}

writeLog("Database connected successfully");

$callbackJSONData = file_get_contents('php://input');
writeLog("Received callback data: " . $callbackJSONData);

$data = json_decode($callbackJSONData);
if ($data && isset($data->Body->stkCallback)) {
    writeLog("Successfully decoded JSON data");
    $stkCallback = $data->Body->stkCallback;
    $ResultCode = $stkCallback->ResultCode;
    $ResultDesc = $stkCallback->ResultDesc;
    $MerchantRequestID = $stkCallback->MerchantRequestID;
    $CheckoutRequestID = $stkCallback->CheckoutRequestID;

    writeLog("Result Code: $ResultCode");
    writeLog("Result Desc: $ResultDesc");

    if ($ResultCode == 0) {
        writeLog("Payment successful, processing callback metadata");
        $CallbackMetadata = $stkCallback->CallbackMetadata;
        $Amount = '';
        $TransactionId = '';
        $PhoneNumber = '';
        $TransactionDate = '';

        foreach ($CallbackMetadata->Item as $item) {
            writeLog("Processing metadata item: " . $item->Name);
            switch ($item->Name) {
                case 'Amount':
                    $Amount = $item->Value;
                    break;
                case 'MpesaReceiptNumber':
                    $TransactionId = $item->Value;
                    break;
                case 'PhoneNumber':
                    $PhoneNumber = $item->Value;
                    break;
                case 'TransactionDate':
                    $TransactionDate = $item->Value;
                    break;
            }
        }

        writeLog("Amount: $Amount, TransactionId: $TransactionId, Phone: $PhoneNumber, Date: $TransactionDate");

        $formattedDate = DateTime::createFromFormat('YmdHis', $TransactionDate);
        $mysqlDateTime = $formattedDate ? $formattedDate->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');

        $query = "INSERT INTO mpesa_payments (transaction_id, phone_number, amount, payment_date, merchant_request_id, checkout_request_id, result_code, result_desc) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        writeLog("Preparing SQL query: $query");

        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            writeLog("Prepare failed: " . $conn->error);
        } else {
            $stmt->bind_param("ssdsssss",
                $TransactionId,
                $PhoneNumber,
                $Amount,
                $mysqlDateTime,
                $MerchantRequestID,
                $CheckoutRequestID,
                $ResultCode,
                $ResultDesc
            );

            if (!$stmt->execute()) {
                writeLog("Error storing payment: " . $stmt->error);
            } else {
                writeLog("Payment stored successfully");
            }

            $stmt->close();
        }
    } else {
        writeLog("Payment not successful. Result Code: $ResultCode");
    }
} else {
    writeLog("Failed to decode JSON data or STK Callback data not found in response");
}

$conn->close();
writeLog("Database connection closed");

$response = array(
    'ResultCode' => 0,
    'ResultDesc' => 'Confirmation received successfully'
);

header('Content-Type: application/json');
echo json_encode($response);
writeLog("Response sent to M-Pesa");