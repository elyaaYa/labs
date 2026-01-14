<?php
require "db_connect.php";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Epicure | Меню ресторана</title>
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
        .menu-header {
            background: linear-gradient(rgba(26, 26, 26, 0.9), rgba(26, 26, 26, 0.9)),
            url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            padding: 80px 20px 60px;
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

        .menu-subtitle {
            font-size: 1.1rem;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 300;
        }

        /* Menu Navigation */
        .menu-nav {
            position: sticky;
            top: 0;
            background: var(--white);
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }

        .menu-nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .menu-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .menu-tab {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            padding: 12px 25px;
            font-size: 1rem;
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
            border-radius: 50px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
        }

        .menu-tab i {
            font-size: 1.2rem;
        }

        .menu-tab:hover {
            color: var(--primary-color);
            background: rgba(193, 154, 107, 0.1);
        }

        .menu-tab.active {
            color: var(--primary-color);
            background: rgba(193, 154, 107, 0.1);
        }

        /* Menu Container */
        .menu-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px 80px;
        }

        /* Menu Section */
        .menu-section {
            display: none;
        }

        .menu-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.8rem;
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }

        .section-description {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.8;
        }

        /* Dishes Grid */
        .dishes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        /* Dish Card */
        .dish-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .dish-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .dish-image {
            height: 220px;
            width: 100%;
            object-fit: cover;
        }

        .dish-content {
            padding: 25px;
        }

        .dish-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .dish-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 600;
            color: var(--secondary-color);
            line-height: 1.3;
        }

        .dish-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .dish-description {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .dish-tags {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }

        .tag {
            display: inline-block;
            padding: 5px 12px;
            background: var(--light-bg);
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-light);
        }

        .tag.chef {
            background: rgba(193, 154, 107, 0.1);
            color: var(--primary-color);
        }

        .tag.signature {
            background: rgba(156, 39, 176, 0.1);
            color: #9c27b0;
        }

        /* Footer */
        .menu-footer {
            background: var(--secondary-color);
            color: var(--white);
            padding: 50px 20px;
            text-align: center;
        }

        .menu-footer .restaurant-logo {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .menu-footer p {
            opacity: 0.8;
            margin-bottom: 25px;
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
            .restaurant-logo {
                font-size: 2.5rem;
            }

            .menu-tab {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .section-title {
                font-size: 2.2rem;
            }

            .dishes-grid {
                grid-template-columns: 1fr;
            }

            .dish-title {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="menu-header">
    <div class="restaurant-logo">Epicure</div>
    <p class="menu-subtitle">Искусство вкуса и гармонии</p>
</header>

<!-- Menu Navigation -->
<nav class="menu-nav">
    <div class="menu-nav-container">
        <div class="menu-tabs">
            <button class="menu-tab active" onclick="showMenu('main', this)">
                <i class="fas fa-utensils"></i>
                <span>Основные блюда</span>
            </button>
            <button class="menu-tab" onclick="showMenu('salads', this)">
                <i class="fas fa-leaf"></i>
                <span>Салаты</span>
            </button>
            <button class="menu-tab" onclick="showMenu('rolls', this)">
                <i class="fas fa-fish"></i>
                <span>Роллы</span>
            </button>
            <button class="menu-tab" onclick="showMenu('desserts', this)">
                <i class="fas fa-birthday-cake"></i>
                <span>Десерты</span>
            </button>
            <button class="menu-tab" onclick="showMenu('drinks', this)">
                <i class="fas fa-glass-whiskey"></i>
                <span>Напитки</span>
            </button>
        </div>
    </div>
</nav>

<!-- Menu Container -->
<main class="menu-container">

    <!-- Основные блюда -->
    <section id="main" class="menu-section active">
        <div class="section-header">
            <h2 class="section-title">Основные блюда</h2>
            <p class="section-description">
                Основа нашего меню - свежайшие морепродукты и рыба, приготовленные
                с уважением к натуральному вкусу. Каждое блюдо - это симфония ароматов,
                где главная партия отдана качественным ингредиентам.
            </p>
        </div>

        <div class="dishes-grid">
            <!-- Лосось на гриле -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Лосось на гриле"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Лосось на гриле с травами</h3>
                        <span class="dish-price">1490 ₽</span>
                    </div>
                    <p class="dish-description">
                        Сочное филе норвежского лосося, обжаренное до золотистой корочки
                        с ароматом оливкового масла холодного отжима. Подаётся с дольками
                        свежего лимона, молодым картофелем и букетом прованских трав.
                    </p>
                    <div class="dish-tags">
                        <span class="tag chef">Шеф-рекомендация</span>
                    </div>
                </div>
            </div>

            <!-- Сибас с розмарином -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Сибас с розмарином"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Сибас на кедровой доске</h3>
                        <span class="dish-price">1650 ₽</span>
                    </div>
                    <p class="dish-description">
                        Нежное филе сибаса, запечённое на кедровой доске с розмарином
                        и чесноком. Аромат дымка гармонично сочетается с цитрусовыми
                        нотками апельсина и лёгким соусом из белого вина.
                    </p>
                    <div class="dish-tags">
                        <span class="tag signature">Фирменное</span>
                    </div>
                </div>
            </div>

            <!-- Мидии в белом вине -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1559925393-8be0ec4767c8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Мидии в белом вине"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Мидии в шампанском</h3>
                        <span class="dish-price">1250 ₽</span>
                    </div>
                    <p class="dish-description">
                        Свежайшие мидии, томлённые в соусе из французского шампанского
                        со сливками, чесноком и петрушкой. Идеально сочетается с хрустящим
                        багетом для макания в ароматный соус.
                    </p>
                    <div class="dish-tags">
                        <span class="tag chef">Шеф-рекомендация</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Салаты -->
    <section id="salads" class="menu-section">
        <div class="section-header">
            <h2 class="section-title">Салаты</h2>
            <p class="section-description">
                Лёгкие и сбалансированные салаты из свежих сезонных овощей и морепродуктов.
                Каждый салат - это гармония текстур и ярких вкусовых акцентов.
            </p>
        </div>

        <div class="dishes-grid">
            <!-- Салат с креветками -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1546069901-d5bfd2cbfb1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Салат с креветками"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Салат с тигровыми креветками</h3>
                        <span class="dish-price">890 ₽</span>
                    </div>
                    <p class="dish-description">
                        Крупные тигровые креветки гриль на подушке из рукколы, авокадо
                        и грейпфрута. Заправляется лёгкой цитрусовой эмульсией с мёдом
                        и дижонской горчицей. Идеальный баланс сладости и кислоты.
                    </p>
                </div>
            </div>

            <!-- Греческий салат -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Греческий салат"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Греческий салат с осьминогом</h3>
                        <span class="dish-price">950 ₽</span>
                    </div>
                    <p class="dish-description">
                        Классический греческий салат обогащённый нежным осьминогом гриль.
                        Свежие томаты, огурцы, красный лук, оливки фета и каперсы
                        в оливковом масле первого отжима с орегано.
                    </p>
                    <div class="dish-tags">
                        <span class="tag signature">Фирменное</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Роллы -->
    <section id="rolls" class="menu-section">
        <div class="section-header">
            <h2 class="section-title">Роллы</h2>
            <p class="section-description">
                Авторские роллы, созданные нашим шеф-поваром. Сочетание традиционных
                техник и современных вкусовых решений. Все роллы готовятся только
                из свежайшей рыбы и морепродуктов.
            </p>
        </div>

        <div class="dishes-grid">
            <!-- Филадельфия -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Ролл Филадельфия"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Филадельфия Премиум</h3>
                        <span class="dish-price">1290 ₽</span>
                    </div>
                    <p class="dish-description">
                        Классика в премиальном исполнении: нежный норвежский лосось,
                        воздушный сливочный сыр, авокадо и огурец. Украшен икрой тобико
                        и золотой фольгой. Подаётся с соевым соусом и васаби.
                    </p>
                    <div class="dish-tags">
                        <span class="tag signature">Фирменное</span>
                    </div>
                </div>
            </div>

            <!-- Ролл с угрём -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1553621042-f6e147245754?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Ролл с угрём"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Унаги Темпура</h3>
                        <span class="dish-price">1450 ₽</span>
                    </div>
                    <p class="dish-description">
                        Хрустящий ролл в кляре темпура с нежным угрём, авокадо
                        и сливочным сыром. Полит фирменным соусом унаги и посыпан
                        кунжутом. Контраст текстур и насыщенный вкус.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Десерты -->
    <section id="desserts" class="menu-section">
        <div class="section-header">
            <h2 class="section-title">Десерты</h2>
            <p class="section-description">
                Нежные десерты, созданные для идеального завершения трапезы.
                Только натуральные ингредиенты, минимум сахара и максимум вкуса.
            </p>
        </div>

        <div class="dishes-grid">
            <!-- Тирамису -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Тирамису"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Тирамису Амаретто</h3>
                        <span class="dish-price">690 ₽</span>
                    </div>
                    <p class="dish-description">
                        Классический итальянский десерт в нашем исполнении: воздушный
                        крем маскарпоне, пропитанные эспрессо с ликёром Амаретто савоярди,
                        щедро посыпанный какао-порошком. Ностальгия по Италии в каждой ложке.
                    </p>
                    <div class="dish-tags">
                        <span class="tag signature">Фирменное</span>
                    </div>
                </div>
            </div>

            <!-- Шоколадный фондан -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Шоколадный фондан"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Шоколадный фондан</h3>
                        <span class="dish-price">750 ₽</span>
                    </div>
                    <p class="dish-description">
                        Тёплый шоколадный кекс с жидкой начинкой из бельгийского шоколада.
                        Подаётся с шариком ванильного мороженого и карамелизированными
                        орехами пекан. Настоящий праздник для сладкоежек.
                    </p>
                    <div class="dish-tags">
                        <span class="tag chef">Шеф-рекомендация</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Напитки -->
    <section id="drinks" class="menu-section">
        <div class="section-header">
            <h2 class="section-title">Напитки</h2>
            <p class="section-description">
                Освежающие безалкогольные напитки собственного приготовления.
                Натуральные ингредиенты, авторские рецепты и забота о вашем самочувствии.
            </p>
        </div>

        <div class="dishes-grid">
            <!-- Апельсиновый фреш -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1600271886742-f049cd451bba?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Апельсиновый фреш"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Солнечный апельсиновый фреш</h3>
                        <span class="dish-price">420 ₽</span>
                    </div>
                    <p class="dish-description">
                        Настоящий заряд бодрости! Свежевыжатый сок из спелых испанских апельсинов,
                        богатый витамином C. Каждый глоток - это вкус солнечного утра и свежести.
                    </p>
                </div>
            </div>

            <!-- Лимонад -->
            <div class="dish-card">
                <img src="https://images.unsplash.com/photo-1600271886742-f049cd451bba?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                     alt="Цитрусовый лимонад"
                     class="dish-image">
                <div class="dish-content">
                    <div class="dish-header">
                        <h3 class="dish-title">Цитрусовый лимонад с мятой</h3>
                        <span class="dish-price">450 ₽</span>
                    </div>
                    <p class="dish-description">
                        Освежающий лимонад собственного приготовления: свежевыжатые лимоны,
                        лайм и апельсин с добавлением мяты и щепотки морской соли.
                        Идеальный баланс кислинки и свежести.
                    </p>
                    <div class="dish-tags">
                        <span class="tag signature">Фирменное</span>
                    </div>
                </div>
            </div>
    </section>

</main>

<!-- Footer -->
<footer class="menu-footer">
    <div class="restaurant-logo">Epicure</div>
    <p>Где каждый приём пищи становится искусством. Ждём вас в нашем ресторане.</p>

    <div class="footer-info">
        <div><i class="fas fa-phone"></i> +7 (495) 123-45-67</div>
        <div><i class="fas fa-clock"></i> Ежедневно 12:00 - 00:00</div>
    </div>
</footer>

<script>
    function showMenu(id, button) {
        // Убираем активный класс у всех вкладок
        document.querySelectorAll('.menu-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Скрываем все секции
        document.querySelectorAll('.menu-section').forEach(section => {
            section.classList.remove('active');
        });

        // Активируем выбранную вкладку и секцию
        button.classList.add('active');
        document.getElementById(id).classList.add('active');

        // Плавный скролл к секции
        document.getElementById(id).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
</script>

</body>
</html>