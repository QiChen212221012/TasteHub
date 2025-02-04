<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TasteHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .main-section {
            display: flex;
            min-height: 100vh;
            background-color: #d59135; /* Main background colour */
            color: white;
            flex-wrap: wrap;
        }
        .left-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }
        .left-section h1 {
            font-size: 60px;
            font-weight: bold;
            font-family: 'Georgia', serif;
            margin-bottom: 20px;
        }
        .left-section p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .join-button {
            display: inline-block;
            padding: 15px 30px;
            background-color: white;
            color: #d59135;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .join-button:hover {
            background-color: #f9c56a;
        }
        .footer-links {
            margin-top: 20px;
            font-size: 14px;
        }
        .footer-links a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
        .right-section {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 10px;
            padding: 20px;
            align-items: center;
            justify-content: center;
        }
        .right-section img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
        }
        .right-section img:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="main-section">
        <!-- Left Section -->
        <div class="left-section">
            <h1>TasteHub</h1>
            <p>Discover, share, and experience amazing dining spots!</p>
            <a href="/login" class="join-button">Join now</a>
            <div class="footer-links">
                <a href="#">About Us</a> | 
                <a href="#">Privacy Policy</a> | 
                <a href="#">Terms of Use</a>
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <img src="{{ asset('images/Food1.jpg') }}" alt="Food 1">
            <img src="{{ asset('images/Food2.jpg') }}" alt="Food 2">
            <img src="{{ asset('images/Food3.jpg') }}" alt="Food 3">
            <img src="{{ asset('images/Food4.jpg') }}" alt="Food 4">
        </div>
    </div>
</body>
</html>
