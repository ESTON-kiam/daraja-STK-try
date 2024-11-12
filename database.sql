CREATE TABLE mpesa_payments (
  id int(11) NOT NULL AUTO_INCREMENT,
  transaction_id varchar(255) NOT NULL,
  phone_number varchar(20) NOT NULL,
  amount decimal(10,2) NOT NULL,
  payment_date datetime NOT NULL,
  merchant_request_id varchar(255) NOT NULL,
  checkout_request_id varchar(255) NOT NULL,
  result_code varchar(20) NOT NULL,
  result_desc text NOT NULL,
  status varchar(20) NOT NULL,
  created_at datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 