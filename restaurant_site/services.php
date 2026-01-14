<?php
require "db_connect.php";
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Epicure | Услуги</title>
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

        /* Header */
        .services-header {
            background: linear-gradient(rgba(26, 26, 26, 0.9), rgba(26, 26, 26, 0.9)),
            url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            padding: 100px 20px 80px;
            text-align: center;
            color: var(--white);
        }

        .restaurant-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5rem;
            font-weight: 400;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 300;
            margin-bottom: 20px;
            letter-spacing: 1px;
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

        /* Services Section */
        .services-section {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.8rem;
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent));
            border-radius: 2px;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--text-light);
            text-align: center;
            max-width: 800px;
            margin: 40px auto 60px;
            line-height: 1.8;
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .service-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .service-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .service-card:hover .service-image {
            transform: scale(1.05);
        }

        .service-content {
            padding: 25px;
        }

        .service-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .service-icon i {
            font-size: 24px;
            color: var(--white);
        }

        .service-content h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            margin-bottom: 15px;
            color: var(--secondary-color);
            line-height: 1.3;
        }

        .service-content p {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .service-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary-color);
            color: var(--white);
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Additional Info */
        .additional-info {
            text-align: center;
            margin-top: 50px;
            padding: 40px;
            background: var(--light-bg);
            border-radius: 16px;
        }

        .additional-info h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--secondary-color);
        }

        .additional-info p {
            color: var(--text-light);
            max-width: 800px;
            margin: 0 auto 25px;
            line-height: 1.8;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 35px;
            background: var(--primary-color);
            color: var(--white);
            text-decoration: none;
            border-radius: 30px;
            font-weight: 500;
            transition: var(--transition);
        }

        .cta-button:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
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
                font-size: 2.5rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .nav-links {
                flex-wrap: wrap;
                gap: 15px;
            }

            .section-title {
                font-size: 2.2rem;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .service-image {
                height: 180px;
            }
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="services-header">
    <div class="restaurant-logo">Epicure</div>
    <h1 class="page-title">Наши услуги</h1>
</header>

<!-- Navigation -->
<nav class="main-nav">
    <div class="nav-container">
        <ul class="nav-links">
            <li><a href="index.php">Главная</a></li>
            <li><a href="menu.php">Меню</a></li>
            <li><a href="services.php" class="active">Услуги</a></li>
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

<!-- Services Section -->
<section class="services-section">
    <h2 class="section-title">Что мы предлагаем</h2>
    <p class="section-subtitle">
        Всё, что мы делаем — это забота о вашем комфорте,
        атмосфере и гастрономическом удовольствии
    </p>

    <div class="services-grid">
        <!-- Бронирование -->
        <div class="service-card">
            <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                 alt="Бронирование столиков"
                 class="service-image">
            <span class="service-badge">Популярно</span>
            <div class="service-content">
                <div class="service-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3>Бронирование столиков</h3>
                <p>
                    Забронируйте столик онлайн или по телефону. Выберите удобное время,
                    количество гостей и особые пожелания. Мы подготовим для вас лучший
                    столик и создадим особую атмосферу для вашего вечера.
                </p>
            </div>
        </div>

        <!-- Банкеты -->
        <div class="service-card">
            <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                 alt="Банкетное обслуживание"
                 class="service-image">
            <div class="service-content">
                <div class="service-icon">
                    <i class="fas fa-glass-cheers"></i>
                </div>
                <h3>Банкетное обслуживание</h3>
                <p>
                    Организуем свадьбы, юбилеи, корпоративы и другие торжества.
                    Индивидуальное меню, профессиональное обслуживание,
                    музыкальное сопровождение и все необходимые услуги под ключ.
                </p>
            </div>
        </div>

        <!-- Частные мероприятия -->
        <div class="service-card">
            <img src="https://images.unsplash.com/photo-1578474846511-04ba529f0b88?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                 alt="Частные мероприятия"
                 class="service-image">
            <span class="service-badge">VIP</span>
            <div class="service-content">
                <div class="service-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Частные мероприятия</h3>
                <p>
                    Закрытые ужины, деловые встречи, презентации и камерные события.
                    Полная конфиденциальность, эксклюзивное меню и персонализированное
                    обслуживание для особых случаев.
                </p>
            </div>
        </div>

        <!-- Праздники -->
        <div class="service-card">
            <img src="https://images.unsplash.com/photo-1532634922-8fe0b757fb13?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                 alt="Праздники и торжества"
                 class="service-image">
            <div class="service-content">
                <div class="service-icon">
                    <i class="fas fa-birthday-cake"></i>
                </div>
                <h3>Праздники и торжества</h3>
                <p>
                    Проведение дней рождений, семейных праздников, романтических ужинов.
                    Авторские десерты, праздничная подача, цветы и музыка —
                    сделаем ваш праздник незабываемым.
                </p>
            </div>
        </div>
    </div>

    <!-- Дополнительная информация -->
    <div class="additional-info">
        <h3>Индивидуальный подход</h3>
        <p>
            Каждое мероприятие мы готовим с особой тщательностью. Обсудим все детали,
            подберём меню по вашему вкусу, учтём диетические ограничения и создадим
            атмосферу, которая запомнится вам и вашим гостям надолго.
        </p>
        <p>
            Наша команда профессионалов позаботится о каждом аспекте: от разработки
            специального меню до музыкального сопровождения и декора.
        </p>
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