<?php
require "db_connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $res->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = $user;
        header("Location: index.php");
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>

    <!-- CSS  -->
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f1eb;
            color: #3b2f2f;
        }

        header {
            text-align: center;
            padding: 30px 20px;
        }

        header h1 {
            margin: 0;
        }

        .container {
            max-width: 400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 30px;
        }

        h2 {
            text-align: center;
            margin-top: 0;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #d8cbbb;
            font-size: 14px;
        }

        button {
            width: 100%;
            background-color: #8b6b4a;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 15px;
        }

        button:hover {
            background-color: #75583c;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }

        .back {
            text-align: center;
            margin-top: 15px;
        }

        .back a {
            color: #8b6b4a;
            text-decoration: none;
        }
    </style>
</head>
<body>

<header>
    <h1>Ресторан</h1>
</header>

<div class="container">
    <div class="card">
        <h2>Вход</h2>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>

        <div class="back">
            <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
            <p><a href="index.php">← На главную</a></p>
        </div>
    </div>
</div>

</body>
</html>
