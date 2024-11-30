<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Прокат автомобилей</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    <header>
        <h1>Прокат автомобилей</h1>
        <nav>
            <ul>
                <li><a href="#about">О нас</a></li>
                <li><a href="#cars">Автомобили</a></li>
                <li><a href="#contact">Контакты</a></li>
                <li><a href="profil.php">Профиль</a></li>
            </ul>
        </nav>
    </header>

    <section id="about">
        <h2>О нас</h2>
        <p>Мы предлагаем широкий выбор автомобилей для аренды на любой вкус и бюджет.</p>
    </section>

    <section id="cars">
        <h2>Автомобили</h2>
        <div class="car">
            <h3>Toyota Prius</h3>
            <p>Цена: 2000 рублей/день</p>
            <a href="prius.php"><button onclick="bookCar('Toyota Prius')">Забронировать</button></a>
        </div>
        <div class="car">
            <h3>Volkswagen Polo</h3>
            <p>Цена:2000 рублей/день</p>
            <a href="polo.php"><button onclick="bookCar('Volkswagen Polo')">Забронировать</button></a>
        </div>
        <div class="car">
            <h3>Hyundai Solaris</h3>
            <p>Цена: 1500 рублей/день</p>
            <a href="solaris.php"><button onclick="bookCar('Hyundai Solaris')">Забронировать</button></a>
        </div>
        <div class="car">
            <h3>Kia Rio</h3>
            <p>Цена:1500 рублей/день</p>
            <a href="rio.php"><button onclick="bookCar('Kia Rio')">Забронировать</button></a>
        </div>
        <div class="car">
            <h3>Mercedes-Benz E200</h3>
            <p>Цена:5000 рублей/день</p>
            <a href="e200.php"><button onclick="bookCar('Mercedes-Benz E200')">Забронировать</button></a>
        </div>
        <div class="car">
            <h3>Skoda Octavia</h3>
            <p>Цена:2500 рублей/день</p>
            <a href="octavia.php"><button onclick="bookCar('Skoda Octavia')">Забронировать</button></a>
        </div>
    </section>

    <section id="contact">
        <h2>Контакты</h2>
        <p>Телефон: +7 999 999 9999</p>
        <p>Email: rentalcar@gmail.com</p>
    </section>

    <footer>
        <p>&copy; 2024 Прокат автомобилей</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>