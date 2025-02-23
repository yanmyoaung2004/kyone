<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Error Occur</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f2f2;
            color: #333;
            text-align: center;
            padding: 20px;
        }

        .error-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .error-container h1 {
            font-size: 72px;
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .error-container p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .error-container a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .error-container a:hover {
            background-color: #2980b9;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <h1>Error Occured</h1>
        <p>Oops! Error Occur</p>
        <a href="/">Go Back Home</a>
    </div>
</body>

</html>
