<?php

require "db_connect.php";
session_start();


if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION["user"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST["date"];
    $time = $_POST["time"];
    $guests = intval($_POST["guests"]);
    $comment = trim($_POST["comment"]);

    if ($date && $time && $guests > 0) {
        $stmt = $conn->prepare(
            "INSERT INTO bookings (user_id, date, time, guests, comment)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "issis",
            $user["id"],
            $date,
            $time,
            $guests,
            $comment
        );
        $stmt->execute();

        $message = "Бронирование успешно создано!";
    } else {
        $message = "Пожалуйста, заполните все обязательные поля";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Бронирование столика</title>

<style>
body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background-color: #f5f1eb;
    color: #3b2f2f;
}

/* Контейнер */
.booking-container {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Карточка */
.booking-card {
    background: #fffaf3;
    padding: 40px 45px;
    border-radius: 26px;
    width: 100%;
    max-width: 460px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.1);
}

.booking-card h2 {
    margin-top: 0;
    margin-bottom: 10px;
    text-align: center;
    font-size: 30px;
}

.booking-card p {
    text-align: center;
    margin-bottom: 25px;
    opacity: 0.85;
}

/* Поля */
.booking-card label {
    font-size: 14px;
    display: block;
    margin-bottom: 6px;
    color: #6f5538;
}

.booking-card input,
.booking-card textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border-radius: 14px;
    border: 1px solid #d6c4ae;
    font-size: 14px;
}

.booking-card textarea {
    resize: vertical;
    min-height: 70px;
}

/* Кнопка */
.booking-card button {
    width: 100%;
    background: #8b6b4a;
    border: none;
    padding: 14px;
    border-radius: 22px;
    color: white;
    font-size: 15px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

.booking-card button:hover {
    background: #6f5538;
    transform: translateY(-2px);
}

/* Сообщение */
.message {
    text-align: center;
    margin-bottom: 15px;
    color: #6f5538;
    font-weight: bold;
}

/* Навигация */
.booking-links {
    margin-top: 20px;
    text-align: center;
}

.booking-links a {
    color: #6f5538;
    text-decoration: none;
    font-size: 14px;
    margin: 0 10px;
}
</style>
</head>
<body>

<div class="booking-container">
<div class="booking-card">

<h2>Бронирование столика</h2>
<p>Выберите удобное время и количество гостей</p>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post">

    <label>Дата</label>
    <input type="date" name="date" required>

    <label>Время</label>
    <input type="time" name="time" required>

    <label>Количество гостей</label>
    <input type="number" name="guests" min="1" max="20" required>

    <label>Комментарий (по желанию)</label>
    <textarea name="comment" placeholder="Пожелания, особые условия..."></textarea>

    <button>Забронировать</button>

</form>

<div class="booking-links">
    <a href="profile.php">Мой профиль</a> |
    <a href="index.php">На главную</a>
</div>

</div>
</div>

</body>
</html>
