<?php
// Включаем отображение ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Проверяем файл подключения
if (file_exists('../db_connect.php')) {
    require "../db_connect.php";
} else {
    // Если файла нет, создаём подключение
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'restaurant_db';

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Ошибка подключения к базе данных: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
}

// Убираем session_start() если сессия уже активна
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Проверка прав администратора
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "<script>
        alert('Доступ запрещён! Требуются права администратора.');
        window.location.href='../index.php';
    </script>";
    exit();
}

/* ===== ДОБАВЛЕНИЕ ПОЛЬЗОВАТЕЛЯ ===== */
if (isset($_POST['add_user'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $conn->query("INSERT INTO users (name, email, password, role)
                  VALUES ('$name', '$email', '$password', '$role')");

    echo "<script>alert('Пользователь добавлен'); location.href='admin.php';</script>";
}

/* ===== РЕДАКТИРОВАНИЕ ПОЛЬЗОВАТЕЛЯ ===== */
if (isset($_POST['edit_user'])) {
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $_POST['role'];

    $conn->query("UPDATE users SET name='$name', email='$email', role='$role' WHERE id=$id");

    echo "<script>alert('Данные пользователя обновлены'); location.href='admin.php';</script>";
}

// Обработка добавления блюда
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_dish'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);

    $sql = "INSERT INTO menu (name, description, price, category) 
            VALUES ('$name', '$description', $price, '$category')";

    if ($conn->query($sql)) {
        echo "<script>alert('Блюдо успешно добавлено!'); window.location.reload();</script>";
    } else {
        echo "<script>alert('Ошибка при добавлении блюда: " . $conn->error . "');</script>";
    }
}

// Обработка удаления
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($_GET['action'] == 'delete_user') {
        $conn->query("DELETE FROM users WHERE id = $id");
        echo "<script>alert('Пользователь удалён!'); window.location.href='admin.php';</script>";
    }

    if ($_GET['action'] == 'delete_dish') {
        $conn->query("DELETE FROM menu WHERE id = $id");
        echo "<script>alert('Блюдо удалено!'); window.location.href='admin.php';</script>";
    }

    if ($_GET['action'] == 'delete_booking') {
        $conn->query("DELETE FROM bookings WHERE id = $id");
        echo "<script>alert('Бронирование удалено!'); window.location.href='admin.php';</script>";
    }

    if ($_GET['action'] == 'change_role') {
        $new_role = $_GET['role'];
        $conn->query("UPDATE users SET role = '$new_role' WHERE id = $id");
        echo "<script>alert('Роль пользователя изменена!'); window.location.href='admin.php';</script>";
    }
}

// Получение данных
$users = $conn->query("SELECT id, name, email, role FROM users ORDER BY id DESC");
$menu = $conn->query("SELECT id, name, description, price FROM menu ORDER BY id DESC");
$bookings = $conn->query("
    SELECT b.id, u.name as user_name, u.email, b.date, b.time, b.guests, b.comment 
    FROM bookings b 
    JOIN users u ON u.id = b.user_id 
    ORDER BY b.date DESC, b.time DESC
");

// Статистика
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_dishes = $conn->query("SELECT COUNT(*) as count FROM menu")->fetch_assoc()['count'];
$today = date('Y-m-d');
$today_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE date = '$today'")->fetch_assoc()['count'];
$today_guests = $conn->query("SELECT COALESCE(SUM(guests), 0) as sum FROM bookings WHERE date = '$today'")->fetch_assoc()['sum'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Epicure | Админ-панель</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Добавляем окно для добавления пользователя */
        #addUserModal .modal {
            max-width: 500px;
        }

        #editUserModal .modal {
            max-width: 500px;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 5px;
        }

        /* Сообщения */
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 10px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
            max-width: 400px;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .close-alert {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0 5px;
        }

        :root {
            --primary-color: #c19a6b;
            --primary-dark: #a67c52;
            --secondary-color: #1a1a1a;
            --text-color: #333333;
            --text-light: #666666;
            --light-bg: #f8f5f0;
            --white: #ffffff;
            --accent: #8b4513;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --transition: all 0.3s ease;
            --shadow: 0 4px 15px rgba(0,0,0,0.1);
            --shadow-hover: 0 8px 25px rgba(0,0,0,0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--light-bg);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Admin Header */
        .admin-header {
            background: linear-gradient(rgba(26, 26, 26, 0.95), rgba(26, 26, 26, 0.95)),
            url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            padding: 40px 20px;
            text-align: center;
            color: var(--white);
            border-bottom: 3px solid var(--primary-color);
        }

        .admin-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            font-weight: 500;
            color: var(--primary-color);
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .admin-subtitle {
            font-size: 1.1rem;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 300;
            margin-bottom: 25px;
        }

        .admin-user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 25px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .admin-avatar {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: var(--white);
        }

        .admin-actions {
            display: flex;
            gap: 10px;
            margin-left: 20px;
        }

        /* Navigation */
        .admin-nav {
            position: sticky;
            top: 0;
            background: var(--white);
            z-index: 100;
            box-shadow: var(--shadow);
            padding: 15px 0;
        }

        .admin-nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .admin-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .admin-tab {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 2px solid var(--light-bg);
            padding: 12px 25px;
            font-size: 1rem;
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
            border-radius: 50px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
        }

        .admin-tab i {
            font-size: 1.2rem;
        }

        .admin-tab:hover {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background: rgba(193, 154, 107, 0.05);
        }

        .admin-tab.active {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background: rgba(193, 154, 107, 0.1);
        }

        /* Container */
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-align: center;
            border-top: 4px solid var(--primary-color);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .stat-icon i {
            font-size: 24px;
            color: var(--white);
        }

        .stat-value {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.8rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 5px;
            line-height: 1;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Sections */
        .admin-section {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .admin-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            font-weight: 500;
            color: var(--secondary-color);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 25px;
            border: none;
            border-radius: 50px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            color: var(--white);
        }

        .btn-warning {
            background: var(--warning);
            color: var(--white);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--white);
        }

        /* Tables */
        .table-container {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        thead {
            background: linear-gradient(135deg, var(--primary-color), var(--accent));
            color: var(--white);
        }

        th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 500;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        tbody tr {
            border-bottom: 1px solid var(--light-bg);
            transition: var(--transition);
        }

        tbody tr:hover {
            background: rgba(193, 154, 107, 0.05);
        }

        td {
            padding: 16px 20px;
            color: var(--text-color);
            vertical-align: middle;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .badge-admin {
            background: rgba(193, 154, 107, 0.15);
            color: var(--primary-color);
            border: 1px solid rgba(193, 154, 107, 0.3);
        }

        .badge-user {
            background: rgba(52, 152, 219, 0.15);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.3);
        }

        .badge-status {
            background: rgba(46, 204, 113, 0.15);
            color: var(--success);
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 25px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .action-edit {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.2);
        }

        .action-delete {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger);
            border: 1px solid rgba(231, 76, 60, 0.2);
        }

        .action-change {
            background: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
            border: 1px solid rgba(155, 89, 182, 0.2);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: var(--white);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-hover);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .modal-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            color: var(--secondary-color);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
        }

        .close-modal:hover {
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 18px;
            border: 2px solid var(--light-bg);
            border-radius: 10px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(193, 154, 107, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        /* Footer */
        .admin-footer {
            background: var(--secondary-color);
            color: var(--white);
            padding: 40px 20px;
            text-align: center;
            margin-top: 60px;
        }

        .admin-footer .admin-logo {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .admin-footer p {
            opacity: 0.8;
            margin-bottom: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .footer-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 25px;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .footer-info i {
            color: var(--primary-color);
            margin-right: 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-logo {
                font-size: 2.2rem;
            }

            .admin-user-info {
                flex-direction: column;
                gap: 15px;
            }

            .admin-actions {
                margin-left: 0;
            }

            .admin-tab {
                padding: 10px 18px;
                font-size: 0.9rem;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-value {
                font-size: 2.2rem;
            }

            table {
                font-size: 0.9rem;
            }

            th, td {
                padding: 12px 15px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .admin-tabs {
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 5px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<!-- Admin Header -->
<header class="admin-header">
    <h1 class="admin-logo">Epicure Admin</h1>
    <p class="admin-subtitle">Управление рестораном</p>

    <div class="admin-user-info">
        <div class="admin-avatar">
            <i class="fas fa-crown"></i>
        </div>
        <div>
            <div style="font-weight: 500; margin-bottom: 5px;"><?= htmlspecialchars($_SESSION['user']['name']) ?></div>
            <div style="font-size: 0.9rem; opacity: 0.8;">Администратор</div>
        </div>
        <div class="admin-actions">
            <a href="../index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> На сайт
            </a>
            <a href="../logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Выйти
            </a>
        </div>
    </div>
</header>

<!-- Admin Navigation -->
<nav class="admin-nav">
    <div class="admin-nav-container">
        <div class="admin-tabs">
            <button class="admin-tab active" onclick="showSection('dashboard', this)">
                <i class="fas fa-tachometer-alt"></i> Панель управления
            </button>
            <button class="admin-tab" onclick="showSection('users', this)">
                <i class="fas fa-users"></i> Пользователи
            </button>
            <button class="admin-tab" onclick="showSection('menu', this)">
                <i class="fas fa-utensils"></i> Меню
            </button>
            <button class="admin-tab" onclick="showSection('bookings', this)">
                <i class="fas fa-calendar-check"></i> Бронирования
            </button>
        </div>
    </div>
</nav>

<!-- Admin Container -->
<main class="admin-container">

    <!-- Dashboard Section -->
    <section id="dashboard" class="admin-section active">
        <div class="section-header">
            <h2 class="section-title">Обзор системы</h2>
            <button class="btn btn-primary" onclick="showAddDishModal()">
                <i class="fas fa-plus"></i> Добавить блюдо
            </button>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?= $total_users ?></div>
                <div class="stat-label">Пользователей</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="stat-value"><?= $total_dishes ?></div>
                <div class="stat-label">Блюд в меню</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-value"><?= $today_bookings ?></div>
                <div class="stat-label">Броней сегодня</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-value"><?= $today_guests ?></div>
                <div class="stat-label">Гостей сегодня</div>
            </div>
        </div>

        <div style="background: var(--white); border-radius: 16px; padding: 30px; box-shadow: var(--shadow);">
            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.6rem; margin-bottom: 20px; color: var(--secondary-color);">
                Быстрые действия
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <button class="btn btn-primary" onclick="showAddUserModal()">
                    <i class="fas fa-user-plus"></i> Добавить пользователя
                </button>
                <button class="btn btn-success" onclick="showAddDishModal()">
                    <i class="fas fa-utensils"></i> Добавить блюдо
                </button>
                <button class="btn btn-warning" onclick="showAddBookingModal()">
                    <i class="fas fa-calendar-plus"></i> Добавить бронирование
                </button>
                <a href="?export=reports" class="btn btn-primary">
                    <i class="fas fa-file-export"></i> Экспорт отчётов
                </a>
            </div>
        </div>
    </section>

    <!-- Users Section -->
    <section id="users" class="admin-section">
        <div class="section-header">
            <h2 class="section-title">Управление пользователями</h2>
            <button class="btn btn-success" onclick="showAddUserModal()">
                <i class="fas fa-user-plus"></i> Добавить пользователя
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($users && $users->num_rows > 0): ?>
                    <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $user['id'] ?></td>
                            <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge <?= $user['role'] == 'admin' ? 'badge-admin' : 'badge-user' ?>">
                                    <?= $user['role'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                        <button class="action-btn action-edit" onclick="showEditUserModal(
                                                '<?= $user['id'] ?>',
                                                '<?= htmlspecialchars($user['name'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>',
                                                '<?= $user['role'] ?>'
                                                )">
                                            <i class="fas fa-edit"></i> Редактировать
                                        </button>
                                        <?php if ($user['role'] == 'admin'): ?>
                                            <a href="?action=change_role&id=<?= $user['id'] ?>&role=user"
                                               class="action-btn action-change"
                                               onclick="return confirm('Сделать пользователем?')">
                                                <i class="fas fa-user"></i> Сделать пользователем
                                            </a>
                                        <?php else: ?>
                                            <a href="?action=change_role&id=<?= $user['id'] ?>&role=admin"
                                               class="action-btn action-change"
                                               onclick="return confirm('Сделать администратором?')">
                                                <i class="fas fa-user-shield"></i> Сделать админом
                                            </a>
                                        <?php endif; ?>
                                        <a href="?action=delete_user&id=<?= $user['id'] ?>"
                                           class="action-btn action-delete"
                                           onclick="return confirm('Удалить пользователя?')">
                                            <i class="fas fa-trash"></i> Удалить
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--text-light); font-size: 0.9rem;">Это вы</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-light);">
                            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3; display: block;"></i>
                            Пользователи не найдены
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="admin-section">
        <div class="section-header">
            <h2 class="section-title">Управление меню</h2>
            <button class="btn btn-success" onclick="showAddDishModal()">
                <i class="fas fa-plus"></i> Добавить блюдо
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Цена</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($menu && $menu->num_rows > 0): ?>
                    <?php while($dish = $menu->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $dish['id'] ?></td>
                            <td>
                                <strong style="color: var(--secondary-color);"><?= htmlspecialchars($dish['name']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars(mb_strimwidth($dish['description'], 0, 70, '...')) ?></td>
                            <td>
                                <strong style="color: var(--primary-color); font-size: 1.1rem;"><?= number_format($dish['price'], 0, ',', ' ') ?> ₽</strong>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_dish.php?id=<?= $dish['id'] ?>" class="action-btn action-edit">
                                        <i class="fas fa-edit"></i> Изменить
                                    </a>
                                    <a href="?action=delete_dish&id=<?= $dish['id'] ?>"
                                       class="action-btn action-delete"
                                       onclick="return confirm('Удалить это блюдо?')">
                                        <i class="fas fa-trash"></i> Удалить
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-light);">
                            <i class="fas fa-utensils" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3; display: block;"></i>
                            Меню пустое
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Bookings Section -->
    <section id="bookings" class="admin-section">
        <div class="section-header">
            <h2 class="section-title">Управление бронированиями</h2>
            <button class="btn btn-success" onclick="showAddBookingModal()">
                <i class="fas fa-calendar-plus"></i> Добавить бронирование
            </button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Клиент</th>
                    <th>Дата</th>
                    <th>Время</th>
                    <th>Гостей</th>
                    <th>Комментарий</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($bookings && $bookings->num_rows > 0): ?>
                    <?php while($booking = $bookings->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $booking['id'] ?></td>
                            <td>
                                <div><strong><?= htmlspecialchars($booking['user_name']) ?></strong></div>
                                <div style="font-size: 0.85rem; color: var(--text-light);"><?= $booking['email'] ?></div>
                            </td>
                            <td><strong><?= date('d.m.Y', strtotime($booking['date'])) ?></strong></td>
                            <td><?= $booking['time'] ?></td>
                            <td>
                                <span class="badge badge-status"><?= $booking['guests'] ?> чел.</span>
                            </td>
                            <td><?= htmlspecialchars(mb_strimwidth($booking['comment'] ?? '', 0, 50, '...')) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?action=delete_booking&id=<?= $booking['id'] ?>"
                                       class="action-btn action-delete"
                                       onclick="return confirm('Удалить бронирование?')">
                                        <i class="fas fa-times"></i> Отменить
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-light);">
                            <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3; display: block;"></i>
                            Бронирования не найдены
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

<!-- Footer -->
<footer class="admin-footer">
    <div class="admin-logo">Epicure</div>
    <p>Административная панель управления рестораном</p>

    <div class="footer-info">
        <div><i class="fas fa-user-shield"></i> Администратор: <?= htmlspecialchars($_SESSION['user']['name']) ?></div>
        <div><i class="fas fa-calendar-alt"></i> <?= date('d.m.Y H:i') ?></div>
    </div>
</footer>

<!-- Add Dish Modal -->
<div id="addDishModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Добавить новое блюдо</h3>
            <button class="close-modal" onclick="hideAddDishModal()">&times;</button>
        </div>
        <form method="POST" onsubmit="return validateDishForm()">
            <div class="form-group">
                <label for="dishName">Название блюда:</label>
                <input type="text" id="dishName" name="name" class="form-control" required
                       placeholder="Введите название блюда">
            </div>

            <div class="form-group">
                <label for="dishDescription">Описание:</label>
                <textarea id="dishDescription" name="description" class="form-control" required
                          placeholder="Опишите блюдо" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="dishPrice">Цена (₽):</label>
                <input type="number" id="dishPrice" name="price" class="form-control" required
                       step="0.01" min="0" placeholder="0.00">
            </div>

            <div class="form-group">
                <label for="dishCategory">Категория:</label>
                <select id="dishCategory" name="category" class="form-control" required>
                    <option value="">Выберите категорию</option>
                    <option value="Основные блюда">Основные блюда</option>
                    <option value="Салаты">Салаты</option>
                    <option value="Роллы">Роллы</option>
                    <option value="Десерты">Десерты</option>
                    <option value="Напитки">Напитки</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" name="add_dish" class="btn btn-success" style="flex: 1;">
                    <i class="fas fa-plus"></i> Добавить блюдо
                </button>
                <button type="button" class="btn btn-danger" onclick="hideAddDishModal()" style="flex: 1;">
                    <i class="fas fa-times"></i> Отмена
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Добавить нового пользователя</h3>
            <button class="close-modal" onclick="hideAddUserModal()">&times;</button>
        </div>
        <form method="POST" onsubmit="return validateUserForm()">
            <div class="form-group">
                <label for="userName">Имя:</label>
                <input type="text" id="userName" name="name" class="form-control" required
                       placeholder="Введите имя пользователя">
            </div>

            <div class="form-group">
                <label for="userEmail">Email:</label>
                <input type="email" id="userEmail" name="email" class="form-control" required
                       placeholder="Введите email">
            </div>

            <div class="form-group">
                <label for="userPassword">Пароль:</label>
                <div class="password-toggle">
                    <input type="password" id="userPassword" name="password" class="form-control" required
                           placeholder="Введите пароль" minlength="6">
                    <button type="button" class="toggle-btn" onclick="togglePassword('userPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="userRole">Роль:</label>
                <select id="userRole" name="role" class="form-control" required>
                    <option value="">Выберите роль</option>
                    <option value="user">Пользователь</option>
                    <option value="admin">Администратор</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" name="add_user" class="btn btn-success" style="flex: 1;">
                    <i class="fas fa-user-plus"></i> Добавить пользователя
                </button>
                <button type="button" class="btn btn-danger" onclick="hideAddUserModal()" style="flex: 1;">
                    <i class="fas fa-times"></i> Отмена
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Редактировать пользователя</h3>
            <button class="close-modal" onclick="hideEditUserModal()">&times;</button>
        </div>
        <form method="POST" onsubmit="return validateEditUserForm()">
            <input type="hidden" id="editUserId" name="id">

            <div class="form-group">
                <label for="editUserName">Имя:</label>
                <input type="text" id="editUserName" name="name" class="form-control" required
                       placeholder="Введите имя пользователя">
            </div>

            <div class="form-group">
                <label for="editUserEmail">Email:</label>
                <input type="email" id="editUserEmail" name="email" class="form-control" required
                       placeholder="Введите email">
            </div>

            <div class="form-group">
                <label for="editUserPassword">Новый пароль (оставьте пустым, если не хотите менять):</label>
                <div class="password-toggle">
                    <input type="password" id="editUserPassword" name="password" class="form-control"
                           placeholder="Введите новый пароль" minlength="6">
                    <button type="button" class="toggle-btn" onclick="togglePassword('editUserPassword')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="editUserRole">Роль:</label>
                <select id="editUserRole" name="role" class="form-control" required>
                    <option value="user">Пользователь</option>
                    <option value="admin">Администратор</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" name="edit_user" class="btn btn-success" style="flex: 1;">
                    <i class="fas fa-save"></i> Сохранить изменения
                </button>
                <button type="button" class="btn btn-danger" onclick="hideEditUserModal()" style="flex: 1;">
                    <i class="fas fa-times"></i> Отмена
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tab navigation
    function showSection(sectionId, button) {
        // Hide all sections
        document.querySelectorAll('.admin-section').forEach(section => {
            section.classList.remove('active');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.admin-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show selected section
        document.getElementById(sectionId).classList.add('active');

        // Add active class to clicked tab
        if (button) {
            button.classList.add('active');
        }

        // Scroll to top smoothly
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Modal functions for dishes
    function showAddDishModal() {
        document.getElementById('addDishModal').classList.add('active');
    }

    function hideAddDishModal() {
        document.getElementById('addDishModal').classList.remove('active');
    }

    // Modal functions for users
    function showAddUserModal() {
        document.getElementById('addUserModal').classList.add('active');
    }

    function hideAddUserModal() {
        document.getElementById('addUserModal').classList.remove('active');
    }

    function showEditUserModal(id, name, email, role) {
        document.getElementById('editUserId').value = id;
        document.getElementById('editUserName').value = name;
        document.getElementById('editUserEmail').value = email;
        document.getElementById('editUserRole').value = role;
        document.getElementById('editUserPassword').value = '';
        document.getElementById('editUserModal').classList.add('active');
    }

    function hideEditUserModal() {
        document.getElementById('editUserModal').classList.remove('active');
    }

    function showAddBookingModal() {
        alert('Функция добавления бронирования будет реализована позже');
    }

    // Form validation
    function validateDishForm() {
        const name = document.getElementById('dishName').value.trim();
        const price = parseFloat(document.getElementById('dishPrice').value);
        const category = document.getElementById('dishCategory').value;

        if (name.length < 2) {
            alert('Название блюда должно содержать хотя бы 2 символа');
            return false;
        }

        if (isNaN(price) || price <= 0) {
            alert('Введите корректную цену (больше 0)');
            return false;
        }

        if (!category) {
            alert('Выберите категорию блюда');
            return false;
        }

        return true;
    }

    function validateUserForm() {
        const name = document.getElementById('userName').value.trim();
        const email = document.getElementById('userEmail').value.trim();
        const password = document.getElementById('userPassword').value;
        const role = document.getElementById('userRole').value;

        if (name.length < 2) {
            alert('Имя должно содержать хотя бы 2 символа');
            return false;
        }

        if (!validateEmail(email)) {
            alert('Введите корректный email адрес');
            return false;
        }

        if (password.length < 6) {
            alert('Пароль должен содержать минимум 6 символов');
            return false;
        }

        if (!role) {
            alert('Выберите роль пользователя');
            return false;
        }

        return true;
    }

    function validateEditUserForm() {
        const name = document.getElementById('editUserName').value.trim();
        const email = document.getElementById('editUserEmail').value.trim();
        const password = document.getElementById('editUserPassword').value;
        const role = document.getElementById('editUserRole').value;

        if (name.length < 2) {
            alert('Имя должно содержать хотя бы 2 символа');
            return false;
        }

        if (!validateEmail(email)) {
            alert('Введите корректный email адрес');
            return false;
        }

        if (password && password.length < 6) {
            alert('Пароль должен содержать минимум 6 символов');
            return false;
        }

        if (!role) {
            alert('Выберите роль пользователя');
            return false;
        }

        return true;
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Toggle password visibility
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const toggleBtn = input.nextElementSibling;
        const icon = toggleBtn.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Close modal on outside click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'addDishModal') hideAddDishModal();
                if (this.id === 'addUserModal') hideAddUserModal();
                if (this.id === 'editUserModal') hideEditUserModal();
            }
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Escape - close modal
        if (e.key === 'Escape') {
            hideAddDishModal();
            hideAddUserModal();
            hideEditUserModal();
        }

        // Ctrl + D - dashboard
        if (e.ctrlKey && e.key === 'd') {
            e.preventDefault();
            showSection('dashboard');
        }

        // Ctrl + U - users
        if (e.ctrlKey && e.key === 'u') {
            e.preventDefault();
            showSection('users');
        }

        // Ctrl + M - menu
        if (e.ctrlKey && e.key === 'm') {
            e.preventDefault();
            showSection('menu');
        }

        // Ctrl + B - bookings
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            showSection('bookings');
        }

        // Ctrl + N - new dish
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            showAddDishModal();
        }

        // Ctrl + Shift + N - new user
        if (e.ctrlKey && e.shiftKey && e.key === 'N') {
            e.preventDefault();
            showAddUserModal();
        }
    });

    // Auto-refresh bookings every 2 minutes
    setInterval(function() {
        if (document.getElementById('bookings').classList.contains('active')) {
            location.reload();
        }
    }, 120000);

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Initialize tooltips
    document.querySelectorAll('[title]').forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('title');
            tooltip.style.position = 'absolute';
            tooltip.style.background = 'var(--secondary-color)';
            tooltip.style.color = 'var(--white)';
            tooltip.style.padding = '8px 12px';
            tooltip.style.borderRadius = '6px';
            tooltip.style.fontSize = '0.85rem';
            tooltip.style.zIndex = '1000';
            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
            tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';

            this._tooltip = tooltip;
        });

        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                this._tooltip.remove();
                this._tooltip = null;
            }
        });
    });

    console.log('Админ-панель Epicure загружена');
</script>

</body>
</html>