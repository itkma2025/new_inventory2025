<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #495057;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .error-container {
            text-align: center;
        }
        .error-container h1 {
            font-size: 3em;
            color: #e44d26;
            margin-bottom: 10px;
        }
        .error-container p {
            font-size: 1.2em;
            margin-top: 0;
        }
        .animation-container {
            margin-top: 20px;
        }
        .doraemon-svg {
            width: 100px;
            height: 100px;
            animation: rotate 2s linear infinite;
        }
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Access Denied</h1>
        <p>Sorry, but you don't have permission to access this resource.</p>
        <div class="animation-container">
        <svg class="doraemon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
    <!-- Contoh path SVG sederhana -->
    <path fill="#e44d26" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
</svg>
        </div>
    </div>
</body>
</html>
