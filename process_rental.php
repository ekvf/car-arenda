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

// Получение данных из формы
$carsId = isset($_POST['Cars_ID']) ? intval($_POST['Cars_ID']) : 0;
$rentalDate = isset($_POST['Rental_Date']) ? $_POST['Rental_Date'] : '';
$returnDate = isset($_POST['Return_Date']) ? $_POST['Return_Date'] : '';

// Валидация данных
if (empty($carsId) || empty($rentalDate) || empty($returnDate)) {
    die("Не все поля заполнены!");
}

if (!is_numeric($carsId) || $carsId <= 0) {
    die("Неверный формат ID автомобиля!");
}

try {
    // Проверка существования автомобиля
    $stmtCheckCar = $pdo->prepare("SELECT * FROM Cars WHERE Cars_ID = :cars_id");
    $stmtCheckCar->execute([':cars_id' => $carsId]);
    if ($stmtCheckCar->rowCount() == 0) {
        die("Автомобиль с таким ID не найден!");
    }
    $car = $stmtCheckCar->fetch(PDO::FETCH_ASSOC);
    $dailyRate = $car['Daily_Rate'];

    // Проверка корректности дат
    $rentalDateObj = new DateTime($rentalDate);
    $returnDateObj = new DateTime($returnDate);
    if ($rentalDateObj > $returnDateObj) {
        die("Дата возврата должна быть позже даты начала аренды!");
    }

    // Проверка доступности автомобиля (исправленная логика)
    $stmtCheckAvailability = $pdo->prepare("
        SELECT *
        FROM Rented_Car
        WHERE Cars_ID = :cars_id
        AND (
            (:rental_date BETWEEN Rental_Date AND Return_Date) OR
            (:return_date BETWEEN Rental_Date AND Return_Date) OR
            (Rental_Date BETWEEN :rental_date AND :return_date)
        )
    ");
    $stmtCheckAvailability->execute([':cars_id' => $carsId, ':rental_date' => $rentalDate, ':return_date' => $returnDate]);
    if ($stmtCheckAvailability->rowCount() > 0) {
        die("Автомобиль уже забронирован на указанный период!");
    }

    // Расчет количества дней аренды
    $interval = $rentalDateObj->diff($returnDateObj);
    $rentalDays = $interval->days;

    // Расчет общей стоимости аренды
    $totalCost = $rentalDays * $dailyRate;

    // Вставка данных в таблицу Rented_Car
    if (!isset($_SESSION['Client_ID'])) {
        die("Ошибка: пользователь не авторизован.");
    }
    $userId = $_SESSION['Client_ID'];
    $stmtInsertRental = $pdo->prepare("INSERT INTO Rented_Car (Cars_ID, Client_ID, Rental_Date, Return_Date, Total_Cost) VALUES (:cars_id, :client_id, :rental_date, :return_date, :total_cost)");
    $stmtInsertRental->execute([
        ':cars_id' => $carsId,
        ':client_id' => $userId,
        ':rental_date' => $rentalDate,
        ':return_date' => $returnDate,
        ':total_cost' => $totalCost
    ]);

    header("Location: Rental.php");
    exit;

} catch (PDOException $e) {
    die("Ошибка при бронировании автомобиля: нельзя забронировать два автомобиля в целях безопасности.");
}

$pdo = null; // Закрытие соединения

?>