<!DOCTYPE html>
<html>
<head>
    <title>Device Request Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .details {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .details ul {
            list-style: none;
            padding: 0;
        }
        .details li {
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .details li:last-child {
            border-bottom: none;
        }
        .details strong {
            color: #555;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Device Request Confirmation</h2>

        <p>Dear <strong>{{ $deviceRequest->name }}</strong>,</p>
        <p>Your device request has been successfully submitted. Below are the details:</p>

        <div class="details">
            <ul>
                <li><strong>Name:</strong> {{ $deviceRequest->name }}</li>
                <li><strong>Email:</strong> {{ $deviceRequest->email }}</li>
                <li><strong>Phone Number:</strong> {{ $deviceRequest->phone_number }}</li>
                <li><strong>Device:</strong> {{ $deviceRequest->device->name }}</li>
                <li><strong>Version:</strong> {{ $deviceRequest->deviceVersion->version }}</li>
                <li><strong>Primary Color:</strong> {{ $deviceRequest->primaryColor->color_name }}</li>
                <li><strong>Secondary Color:</strong> {{ $deviceRequest->secondaryColor->color_name }}</li>
                <li><strong>Shipping Address:</strong> {{ $deviceRequest->shippingAddress->address }}</li>
                <li><strong>Shipping Attention:</strong> {{ $deviceRequest->shipping_attention ?? 'N/A' }}</li>
                <li><strong>Caller ID Requested:</strong> {{ $deviceRequest->caller_id_requested ?? 'N/A' }}</li>
            </ul>
        </div>

        <p>Thank you for using our service!</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} TruView. All Rights Reserved.</p>
        </div>
    </div>

</body>
</html>
