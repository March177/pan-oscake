<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        #receipt {
            width: 300px;
            margin: 0 auto;
            font-family: 'Arial', sans-serif;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .header {
            text-align: center;
        }

        .logo {
            width: 80px;
            margin-bottom: 10px;
        }

        .date {
            font-size: 12px;
            color: #888;
        }

        .token-section {
            text-align: center;
            margin: 20px 0;
        }

        .token-section h2 {
            font-size: 18px;
            font-weight: bold;
        }

        .token-number {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            border: 2px dashed #000;
            padding: 10px;
            display: inline-block;
        }

        .info-section {
            margin: 10px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .label {
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }

        .value {
            font-size: 12px;
            color: #000;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .thanks {
            font-size: 10px;
            color: #555;
            margin-bottom: 10px;
        }

        .footer-logo {
            width: 60px;
            margin-top: 10px;
        }
    </style>

    <head>

    <body>
        <div id="receipt">
            <div class="header">
                <img src="logo.png" alt="Company Logo" class="logo">
                <p class="date">Wed, May 27, 2020 • 9:27:53 AM</p>
            </div>

            <div class="token-section">
                <h2>Token</h2>
                <div class="token-number">0237-7746-8981-9028-5626</div>
            </div>

            <div class="info-section">
                <div class="info-row">
                    <span class="label">Token Type</span>
                    <span class="value">Credit</span>
                </div>
                <div class="info-row">
                    <span class="label">Customer Name</span>
                    <span class="value">Victor Shoaga</span>
                </div>
                <div class="info-row">
                    <span class="label">Customer Type</span>
                    <span class="value">R3</span>
                </div>
                <div class="info-row">
                    <span class="label">Address</span>
                    <span class="value">7953 Oakland St.<br>Honolulu, HI 96815</span>
                </div>
                <div class="info-row">
                    <span class="label">Meter Number</span>
                    <span class="value">04172997324</span>
                </div>
                <div class="info-row">
                    <span class="label">Amount</span>
                    <span class="value">950 NGN</span>
                </div>
                <div class="info-row">
                    <span class="label">Tax</span>
                    <span class="value">50 NGN</span>
                </div>
                <div class="info-row">
                    <span class="label">Total</span>
                    <span class="value">1000 NGN</span>
                </div>
                <div class="info-row">
                    <span class="label">Operator</span>
                    <span class="value">Ade</span>
                </div>
            </div>

            <div class="footer">
                <p class="thanks">Thanks for fueling our passion. Drop by again, if your wallet isn't still sulking. You're always welcome here!</p>
                <img src="acme.png" alt="ACME Logo" class="footer-logo">
            </div>
        </div>

    </body>

</html>