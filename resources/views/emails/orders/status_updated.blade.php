<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
        }

        .header {
            background-color: #007bff;
            color: #fff;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 20px;
        }

        .content p {
            font-size: 16px;
            line-height: 1.5;
        }

        .order-details {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }

        .order-details th,
        .order-details td {
            padding: 10px;
            text-align: left;
        }

        .order-details th {
            background-color: #f2f2f2;
        }

        .order-details td {
            border-bottom: 1px solid #ddd;
        }

        .footer {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h2>Order Status Update</h2>
        </div>

        <div class="content">
            <p>Hello,</p>
            <p>We would like to inform you that the status of your order has been updated. Below are the details of your
                order:</p>

            <table class="order-details">
                <tr>
                    <th>Order ID</th>
                    <td>{{ $orderId }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ $orderStatus }}</td>
                </tr>
                <tr>
                    <th>Estimated Time of Arrival (ETA)</th>
                    <td>{{ $orderETA }}</td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td>${{ number_format($order->orderTotal, 2) }}</td>
                </tr>
            </table>

            <p>If you have any questions or need further assistance, feel free to contact us.</p>
        </div>

        <div class="footer">
            <p>Thanks, <br> {{ config('app.name') }}</p>
        </div>
    </div>

</body>

</html>
