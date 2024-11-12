<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment - Nitumie Bob</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 mb-4">
                    <div class="row">
                        <div class="col-12 text-center mb-4">
                            <h4 class="fw-bold">Make a Payment</h4>
                        </div>
                    </div>

                    <div class="alert alert-success" role="alert" id="success-message" style="display: none;"></div>
                    <div class="alert alert-danger" role="alert" id="error-message" style="display: none;"></div>
                    <div class="loading" id="loading" style="display: none;">
                        Processing payment...
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <form class="row g-3" id="payment-form">
                                <div class="col-12">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter Amount" required>
                                </div>
                                <div class="col-12">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" id="phone" placeholder="Enter Phone Number (e.g., 0712345678)" required>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary" id="submit-btn">Make Payment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="transactions.html" class="btn btn-outline-primary">View Transactions</a>
        </div>
    </div>

    <script>
        document.getElementById('payment-form').addEventListener('submit', function(event) {
            event.preventDefault();
            
            document.getElementById('success-message').style.display = 'none';
            document.getElementById('error-message').style.display = 'none';
            document.getElementById('loading').style.display = 'block';
            document.getElementById('submit-btn').disabled = true;

            const phone = document.getElementById('phone').value;
            const amount = document.getElementById('amount').value;

            if (!phone || !amount) {
                showError('Please fill in all fields');
                document.getElementById('loading').style.display = 'none';
                document.getElementById('submit-btn').disabled = false;
                return;
            }

            const data = {
                phone: phone,
                amount: amount,
            };

            fetch('stk_initiate.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('submit-btn').disabled = false;

                if (data.ResponseCode === '0') {
                    showSuccess(data.CustomerMessage);
                    document.getElementById('payment-form').reset();
                } else {
                    showError(data.ResponseDescription || 'An error occurred processing the payment');
                }
            })
            .catch(error => {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('submit-btn').disabled = false;
                showError('There was an error processing your payment. Please try again.');
                console.error('Payment Error:', error);
            });
        });

        function showSuccess(message) {
            const successDiv = document.getElementById('success-message');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function showError(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
</body>
</html>