<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Аренда</title>
</head>
<body>
    <header class="bg-primary text-white p-3">
        <div class="container">
            <h1>Аренда</h1>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">На Главную</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <div class="container mt-4">
        <?php
        session_start();

        // Подключение к базе данных (замените на ваши данные)
        $db_host = 'localhost';
        $db_name = 'volkov.d.r';
        $db_user = 'volkov.d.r';
        $db_pass = '2345f';

        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }

        // Проверка авторизации
        if (!isset($_SESSION['Client_ID'])) {
            die("Ошибка: пользователь не авторизован.");
        }
        $clientId = $_SESSION['Client_ID'];

        try {
            // Запрос для получения информации о забронированных автомобилях
            $stmt = $pdo->prepare("
                SELECT 
                    rc.Rental_Date, 
                    rc.Return_Date, 
                    c.Model, 
                    c.Color, 
                    rc.Total_Cost
                FROM 
                    Rented_Car rc
                JOIN 
                    Cars c ON rc.Cars_ID = c.Cars_ID
                WHERE 
                    rc.Client_ID = ?
            ");
            $stmt->execute([$clientId]);
            $rentedCars = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($rentedCars)) {
                echo "<h2>Ваши бронирования:</h2>";
                echo "<form method='post' action='Rental.php'>"; // Добавлена форма
                echo "<table class='table table-striped'>";
                echo "<tr><th>Дата аренды</th><th>Дата возврата</th><th>Модель</th><th>Цвет</th><th>Стоимость</th><th>Отмена</th></tr>";
                foreach ($rentedCars as $rentedCar) {
                    echo "<tr>";
                    echo "<td>" . $rentedCar['Rental_Date'] . "</td>";
                    echo "<td>" . $rentedCar['Return_Date'] . "</td>";
                    echo "<td>" . $rentedCar['Model'] . "</td>";
                    echo "<td>" . $rentedCar['Color'] . "</td>";
                    echo "<td>" . $rentedCar['Total_Cost'] . " руб.</td>";
                    echo "<td><button type='submit' name='cancel' value='" . $rentedCar['Rental_Date'] . "' class='btn btn-danger btn-sm'>Отменить</button></td>"; // Кнопка отмены
                    echo "</tr>";
                }
                echo "</table>";
                echo "</form>"; // Закрытие формы
            } else {
                echo "<p>У вас нет активных бронирований.</p>";
            }

            // Обработка отмены бронирования
            if (isset($_POST['cancel'])) {
                $rentalDateToCancel = $_POST['cancel'];
                $stmtCancel = $pdo->prepare("DELETE FROM Rented_Car WHERE Client_ID = ? AND Rental_Date = ?");
                $stmtCancel->execute([$clientId, $rentalDateToCancel]);
                echo "<p>Бронирование отменено!</p>";
                // Возможно, нужно обновить страницу, чтобы отобразить изменения
                header("Refresh:0");
            }

        } catch (PDOException $e) {
            die("Ошибка: " . $e->getMessage());
        }

        $pdo = null; // Закрытие соединения

        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>