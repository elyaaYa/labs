<?php
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fio = htmlspecialchars($_POST['fio']);
    $login = htmlspecialchars($_POST['login']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $birthday = $_POST['birthday'];

   
    $message = "Пользователь $login ($fio) успешно зарегистрирован!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
</head>
<body>
    <?php if ($message) echo "<p style='color:green'>$message</p>"; ?>

    <form method="POST">
        <p>ФИО: <input type="text" name="fio" required></p>
        <p>Логин: <input type="text" name="login" required></p>
        <p>Пароль: <input type="password" name="password" required></p>
        <p>Дата рождения: <input type="date" name="birthday" required></p>
        <button type="submit">Зарегистрироваться</button>
    </form>
</body>
</html>