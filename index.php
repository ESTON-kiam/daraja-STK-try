<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nitumie Bob</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f5f5;
    }
    .message-container {
      margin-bottom: 20px;
    }
    .success-message {
      color: green;
      font-weight: bold;
      text-align: center;
    }
    .error-message {
      color: red;
      font-weight: bold;
      text-align: center;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .btn-primary {
      background-color: #6c63ff;
      border-color: #6c63ff;
    }
    .btn-primary:hover {
      background-color: #5c54d9;
      border-color: #5c54d9;
    }
    .form-label {
      font-weight: 600;
    }
    .form-control {
      border-radius: 10px;
      border-color: #e0e0e0;
    }
    .form-control:focus {
      border-color: #6c63ff;
      box-shadow: 0 0 0 0.25rem rgba(108, 99, 255, 0.25);
    }
    .mpesa-button {
      background-color: #00b33c;
      color: #fff;
      border-radius: 10px;
      padding: 0.5rem 1rem;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }
    .mpesa-button:hover {
      background-color: #009933;
      color: #fff;
    }
  </style>
</head>
<body oncontextmenu="return false">
  <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card p-4">
      <div class="row">
        <div class="col-12 text-center mb-4">
          <h4 class="fw-bold">Make a Payment</h4>
        </div>
      </div>

     
      <div class="row">
        <div class="col-12 message-container">
          <div id="message" class=""></div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <form class="row g-3" action="./stk_initiate.php" method="POST" id="payment-form">
            <div class="col-12">
              <label for="inputAddress" class="form-label">Amount</label>
              <input type="number" class="form-control" name="amount" placeholder="Enter Amount" required>
            </div>
            <div class="col-12">
              <label for="inputAddress2" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" name="phone" placeholder="Enter Phone Number" required>
            </div>
            <div class="col-12 text-center">
              <button type="submit" class="btn btn-primary" name="submit" value="submit">Make Payment</button>
            </div>
          </form>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-12 text-center">
          <a href="#" class="mpesa-button">Pay with M-PESA</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('payment-form').addEventListener('submit', function(event) {
      event.preventDefault();

      fetch('./stk_initiate.php', {
        method: 'POST',
        body: new FormData(event.target)
      })
      .then(response => response.json())
      .then(data => {
        const messageContainer = document.getElementById('message');
        messageContainer.classList.remove('success-message', 'error-message');

        if (data.ResponseCode === '0') {
          messageContainer.classList.add('success-message');
          messageContainer.textContent = `Payment Successful!\nCheckout Request ID: ${data.CheckoutRequestID}\nCustomer Message: ${data.CustomerMessage}`;
        } else {
          messageContainer.classList.add('error-message');
          messageContainer.textContent = `Payment Failed!\nError: ${data.ResponseDescription || 'Unknown error'}`;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        const messageContainer = document.getElementById('message');
        messageContainer.classList.add('error-message');
        messageContainer.textContent = 'An error occurred while processing the payment. Please try again later.';
      });
    });
  </script>
</body>
</html>
