CREATE TABLE mpesa_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id VARCHAR(50) UNIQUE, 
    phone_number VARCHAR(15),          
    amount DECIMAL(10, 2),            
    payment_date DATETIME,             
    merchant_request_id VARCHAR(50),   
    checkout_request_id VARCHAR(50),   
    result_code INT,                  
    result_desc VARCHAR(255),          
    status ENUM('successful', 'failed', 'pending'), 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
