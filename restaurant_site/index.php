<?php
require "db_connect.php";
session_start()
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Epicure | Главная</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #c19a6b;
            --primary-dark: #a67c52;
            --secondary-color: #1a1a1a;
            --text-color: #333333;
            --text-light: #666666;
            --light-bg: #f8f5f0;
            --white: #ffffff;
            --accent: #8b4513;
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
            background: #f9f5f0;
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(26, 26, 26, 0.9), rgba(26, 26, 26, 0.9)),
            url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            padding: 100px 20px 80px;
            text-align: center;
            color: var(--white);
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-content {
            max-width: 800px;
        }

        .restaurant-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4.5rem;
            font-weight: 400;
            color: var(--primary-color);
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .hero-subtitle {
            font-size: 1.4rem;
            letter-spacing: 3px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 300;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .hero-description {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            max-width: 600px;
            margin: 0 auto 40px;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 35px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--white);
            border: 2px solid var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background: transparent;
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--white);
            transform: translateY(-3px);
        }

        /* Navigation */
        .main-nav {
            background: var(--white);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            padding: 8px 0;
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links a.active {
            color: var(--primary-color);
        }

        .nav-links a.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-color);
        }

        /* User Info */
        .user-info {
            text-align: center;
            margin-top: 20px;
            color: var(--white);
        }

        .user-info strong {
            color: var(--primary-color);
        }

        /* Content Section */
        .content-section {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.8rem;
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 30px;
            text-align: center;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: var(--text-light);
            text-align: center;
            max-width: 800px;
            margin: 0 auto 50px;
            line-height: 1.8;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-card {
            background: var(--white);
            border-radius: 16px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .feature-icon i {
            font-size: 28px;
            color: var(--white);
        }

        .feature-card h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: var(--secondary-color);
        }

        .feature-card p {
            color: var(--text-light);
            line-height: 1.6;
        }

        /* About Section */
        .about-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 50px;
            align-items: center;
            margin-top: 40px;
        }

        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text-light);
        }

        .about-text p {
            margin-bottom: 20px;
        }

        .about-image {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .about-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.6s ease;
        }

        .about-image:hover img {
            transform: scale(1.05);
        }

        /* Footer */
        .main-footer {
            background: var(--secondary-color);
            color: var(--white);
            padding: 60px 20px 30px;
            margin-top: 80px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }

        .footer-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .footer-description {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .footer-contact {
            color: rgba(255, 255, 255, 0.8);
        }

        .footer-contact p {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-contact i {
            color: var(--primary-color);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            margin-top: 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .restaurant-logo {
                font-size: 3rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .nav-links {
                flex-wrap: wrap;
                gap: 15px;
            }

            .section-title {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1 class="restaurant-logo">Epicure</h1>
        <div class="hero-subtitle">Искусство гастрономии</div>
        <p class="hero-description">
            Где кулинария становится искусством, а каждый ужин —
            незабываемым впечатлением. Авторская кухня, утончённая атмосфера
            и безупречный сервис ждут вас.
        </p>

        <?php if (!isset($_SESSION['user'])): ?>
            <div class="hero-buttons">
                <a href="register.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Регистрация
                </a>
                <a href="login.php" class="btn btn-secondary">
                    <i class="fas fa-sign-in-alt"></i> Вход
                </a>
            </div>
        <?php else: ?>
            <div class="user-info">
                <p>Добро пожаловать, <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Navigation -->
<nav class="main-nav">
    <div class="nav-container">
        <ul class="nav-links">
            <li><a href="index.php" class="active">Главная</a></li>
            <li><a href="menu.php">Меню</a></li>
            <li><a href="services.php">Услуги</a></li>
            <li><a href="booking.php">Бронирование</a></li>

            <?php if (isset($_SESSION['user'])): ?>
                <li><a href="profile.php">Профиль</a></li>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <li><a href="admin/dashboard.php">Админ-панель</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Выход</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- Features Section -->
<section class="content-section">
    <h2 class="section-title">Почему выбирают нас</h2>
    <p class="section-subtitle">
        Мы создаём не просто ресторан, а пространство для настоящих гастрономических открытий
    </p>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-seedling"></i>
            </div>
            <h3>Свежие продукты</h3>
            <p>Только фермерские продукты и сезонные ингредиенты от проверенных поставщиков. Качество — наш главный принцип.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3>Шеф-повар</h3>
            <p>Авторские блюда от шеф-повара с мировым признанием. Каждое блюдо — произведение кулинарного искусства.</p>
        </div>
</section>

<!-- About Section -->
<section class="content-section">
    <h2 class="section-title">О ресторане</h2>

    <div class="about-content">
        <div class="about-text">
            <p>Epicure - это место, где встречаются традиции и инновации.<p>
            <p>Наш интерьер создан для комфорта и эстетического удовольствия. Каждая деталь продумана, чтобы вы чувствовали себя особенными гостями.</p>
        </div>

        <div class="about-image">
            <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Интерьер ресторана">
        </div>
    </div>
</section>

<!-- Specials Section -->
<section class="content-section">
    <h2 class="section-title">Попробуйте наши хиты</h2>
    <p class="section-subtitle">
        Самые популярные блюда, которые выбирают наши гости
    </p>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-fish"></i>
            </div>
            <h3>Лосось на гриле</h3>
            <p>Сочное филе норвежского лосося с ароматом оливкового масла холодного отжима. Подаётся с дольками свежего лимона и прованскими травами.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-leaf"></i>
            </div>
            <h3>Салат с креветками</h3>
            <p>Крупные тигровые креветки гриль на подушке из рукколы, авокадо и грейпфрута с цитрусовой эмульсией.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-birthday-cake"></i>
            </div>
            <h3>Тирамису Амаретто</h3>
            <p>Классический итальянский десерт в нашем исполнении: воздушный крем маскарпоне с ликёром Амаретто.</p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="main-footer">
    <div class="footer-content">
        <div>
            <div class="footer-logo">Epicure</div>
            <p class="footer-description">
                Ресторан авторской кухни, где каждый приём пищи становится искусством.
                Где рождаются вкусовые воспоминания.
            </p>
        </div>

        <div class="footer-contact">
            <h3>Контакты</h3>
            <p><i class="fas fa-phone"></i> +7 (495) 123-45-67</p>
            <p><i class="fas fa-clock"></i> Пн-Вс: 12:00 - 00:00</p>
        </div>
    </div>

    <div class="copyright">
        &copy; 2024 Epicure Restaurant. Все права защищены.
    </div>
</footer>

</body>
</html>