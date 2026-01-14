<?php
require "db_connect.php";
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION["user"];
$edit = isset($_GET["edit"]);
$message = "";

/* === ЗАГРУЗКА УСЛУГ (БРОНИРОВАНИЙ) === */
$services = [];

$stmtServices = $conn->prepare(
        "SELECT date, time, guests, comment 
     FROM bookings 
     WHERE user_id = ?
     ORDER BY date DESC, time DESC"
);
$stmtServices->bind_param("i", $user["id"]);
$stmtServices->execute();
$resServices = $stmtServices->get_result();

while ($row = $resServices->fetch_assoc()) {
    $services[] = $row;
}

/* === ОБРАБОТКА РЕДАКТИРОВАНИЯ ПРОФИЛЯ === */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($name && $email) {

        if ($password !== "") {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                    "UPDATE users SET name=?, email=?, password=? WHERE id=?"
            );
            $stmt->bind_param("sssi", $name, $email, $hash, $user["id"]);
        } else {
            $stmt = $conn->prepare(
                    "UPDATE users SET name=?, email=? WHERE id=?"
            );
            $stmt->bind_param("ssi", $name, $email, $user["id"]);
        }

        $stmt->execute();

        $_SESSION["user"]["name"] = $name;
        $_SESSION["user"]["email"] = $email;

        $user = $_SESSION["user"];
        $message = "Данные успешно обновлены";
        $edit = false;
    } else {
        $message = "Заполните все обязательные поля";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль пользователя</title>

    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f1eb;
            color: #3b2f2f;
        }

        .profile-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-card {
            background: #fffaf3;
            padding: 40px;
            border-radius: 24px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .profile-card h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-item {
            margin-bottom: 16px;
        }

        .profile-item span {
            display: block;
            font-weight: bold;
            color: #6f5538;
            margin-bottom: 4px;
        }

        .profile-card input {
            width: 100%;
            padding: 12px;
            margin-bottom: 14px;
            border-radius: 14px;
            border: 1px solid #d6c4ae;
            font-size: 14px;
        }

        .profile-actions {
            margin-top: 25px;
            display: flex;
            gap: 12px;
        }

        .profile-actions a,
        .profile-actions button {
            flex: 1;
            text-align: center;
            background: #8b6b4a;
            color: white;
            padding: 12px 0;
            border-radius: 20px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .profile-actions a:hover,
        .profile-actions button:hover {
            background: #6f5538;
            transform: translateY(-2px);
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: #6f5538;
        }

        /* === УСЛУГИ === */
        .services-title {
            margin: 30px 0 15px;
            font-size: 18px;
            color: #6f5538;
        }

        .service-box {
            background: #f5f1eb;
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 12px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-card">

        <h2>Личный кабинет</h2>

        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <?php if (!$edit): ?>

            <!-- ПРОСМОТР -->
            <div class="profile-item">
                <span>Имя</span>
                <?= htmlspecialchars($user["name"]) ?>
            </div>

            <div class="profile-item">
                <span>Email</span>
                <?= htmlspecialchars($user["email"]) ?>
            </div>

            <div class="profile-item">
                <span>Роль</span>
                <?= $user["role"] === "admin" ? "Администратор" : "Пользователь" ?>
            </div>

            <!-- МОИ УСЛУГИ -->
            <div class="services-title">Мои услуги</div>

            <?php if (empty($services)): ?>
                <p>Вы пока не воспользовались услугами ресторана.</p>
            <?php else: ?>
                <?php foreach ($services as $s): ?>
                    <div class="service-box">
                        <strong>Услуга:</strong> Бронирование столика<br>
                        <strong>Дата:</strong> <?= htmlspecialchars($s["date"]) ?><br>
                        <strong>Время:</strong> <?= htmlspecialchars($s["time"]) ?><br>
                        <strong>Гостей:</strong> <?= htmlspecialchars($s["guests"]) ?><br>
                        <?php if ($s["comment"]): ?>
                            <strong>Комментарий:</strong>
                            <?= htmlspecialchars($s["comment"]) ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="profile-actions">
                <a href="profile.php?edit=1">Редактировать</a>
                <a href="logout.php">Выйти</a>
            </div>

        <?php else: ?>

            <!-- РЕДАКТИРОВАНИЕ -->
            <form method="post">
                <input type="text" name="name"
                       value="<?= htmlspecialchars($user["name"]) ?>" required>

                <input type="email" name="email"
                       value="<?= htmlspecialchars($user["email"]) ?>" required>

                <input type="password" name="password"
                       placeholder="Новый пароль (необязательно)">

                <div class="profile-actions">
                    <button>Сохранить</button>
                    <a href="profile.php">Отмена</a>
                </div>
            </form>

        <?php endif; ?>

    </div>
</div>

</body>
</html>
