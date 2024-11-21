<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motell Booking</title>
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;

        }

        .container {
            background-color: #fff;
            padding: 4.5em;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            height: 300px;
            width: 90%;
            text-align: center;
        }

        h3 {
            margin-bottom: 1.5em;
            font-size: 1.5em;
            color: #101010;
        }

        ul {
            list-style-type: none;
        }

        li {
            margin: 0.5em 0;
        }

        a {
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.2s ease;
        }

        a:hover {
            color: #004080;
        }

        .logout {
            display: inline-block;
            margin-top: 2em;
            padding: 0.5em 1em;
            color: #0066cc;
        }

    </style>
</head>
<body>
<div class="container">
    <h3>Book Motell her</h3>
    <ul>
        <li><a target="_blank" href="../src/forms/form_nyBruker.php">Ny bruker</a></li>
        <li><a target="_blank" href="../src/forms/form_exisBruker.php">Eksisterende bruker</a></li>
        <li><a target="_blank" href="../public/logout.php" class="logout">Logg ut</a></li>
    </ul>
</div>
</body>
</html>
