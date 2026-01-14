<?php
require "db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($name && $email && $password) {

        // проверка, существует ли email
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Пользователь с таким email уже существует";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // регистрация пользователя с ролью user
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')"
            );
            $stmt->bind_param("sss", $name, $email, $hash);
            $stmt->execute();

            header("Location: login.php");
            exit;
        }
    } else {
        $message = "Заполните все поля";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>

    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f1eb;
            color: #3b2f2f;
        }

        .form-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-card {
            background: #fffaf3;
            padding: 40px 45px;
            border-radius: 24px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .form-card h2 {
            margin-top: 0;
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
        }

        .form-card input {
            width: 100%;
            padding: 12px;
            margin-bottom: 14px;
            border-radius: 14px;
            border: 1px solid #d6c4ae;
            font-size: 14px;
        }

        .form-card button {
            width: 100%;
            background: #8b6b4a;
            border: none;
            padding: 12px;
            border-radius: 20px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .form-card button:hover {
            background: #6f5538;
            transform: translateY(-2px);
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: #6f5538;
        }

        .login-link {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }

        .login-link a {
            color: #6f5538;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="form-container">
    <div class="form-card">

        <h2>Регистрация</h2>

        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="name" placeholder="Имя" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Пароль" required>

            <button>Создать аккаунт</button>
        </form>

        <div class="login-link">
            Уже есть аккаунт? <a href="login.php">Войти</a>
        </div>

    </div>
</div>

</body>
</html>
