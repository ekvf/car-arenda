<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('db.php'); // Подключение к базе данных
$Client_ID = $_SESSION['Client_ID'];
$pass = isset($_POST['Tenant_password']) ? trim($_POST['Tenant_password']) : '';
$query = "SELECT `Full_Name`, `Address`, `Phone_Number`, `Discount_code`, `Tenant_password` FROM `Tenant` WHERE `Client_ID` = $Client_ID";
$result = mysqli_query($conn, $query);

while ($row = $result->fetch_assoc()) {
    $Full_Name = $row['Full_Name'];
    $Address = $row['Address'];
    $Phone_Number = $row['Phone_Number'];
    $Discount_code = $row['Discount_code'];
    $Tenant_password = hash('sha256', $row['Tenant_password']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из массива $_POST
    $Full_Name = isset($_POST['Full_Name']) ? trim($_POST['Full_Name']) : '';
    $Address = isset($_POST['Address']) ? trim($_POST['Address']) : '';
    $Phone_Number = isset($_POST['Phone_Number']) ? trim($_POST['Phone_Number']) : '';
    $Discount_code = isset($_POST['Discount_code']) ? trim($_POST['Discount_code']) : '';
    $Tenant_password = isset($_POST['Tenant_password']) ? hash('sha256', trim($_POST['Tenant_password'])) : '';
		var_dump($_POST);
    // Вставка данных в базу данных
    $insert_sql = "UPDATE `Tenant` 
                   SET `Full_Name`=?, 
                       `Address`=?,
                       `Phone_Number`=?,
                       `Tenant_password`=?,
                       `Discount_code`=? 
                   WHERE `Client_ID`=?";


    // Подготовим выражение
    if ($stmt = $conn->prepare($insert_sql)) {
        // Привяжем параметры
        $stmt->bind_param("sssssi", $Full_Name, $Address, $Phone_Number, $Tenant_password, $Discount_code, $Client_ID);
        
        // Выполним запрос
        if ($stmt->execute()) {
            // Успешное обновление
            var_dump($Full_Name, $Address, $Phone_Number, $Tenant_password, $Discount_code, $Client_ID);
            //header("Location: index.php");
            //exit;
        } else {
            echo "Ошибка выполнения запроса: " . $stmt->error;
        }
    } else {
        echo "Ошибка подготовки запроса: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Профиль</title>
</head>
<body>
    <header>
        <h1>Профиль</h1>
        <nav>
            <ul>
                <li><a href="index.php">На Главную</a></li>
                <li><a href="logout.php">Выйти</a></li
            </ul>
        </nav>
    </header>
    
    <section id="prof">
        <h2>Личные данные</h2>
        <form method="POST">
            <div class="prof">
                <h3>ФИО</h3>
                <input type="text" name="Full_Name" value="<?=$Full_Name?>" required />
            </div>
            <div class="prof">
                <h3>Адрес</h3>
                <input type="text" name="Address" value="<?=$Address?>" required />
            </div>
            <div class="prof">
                <h3>Номер телефона</h3>
                <input type="text" name="Phone_Number" value="<?=$Phone_Number?>" required />
            </div>
            <div class="prof">
                <h3>Пароль</h3>
                <input type="password" name="Tenant_password" placeholder="Введите новый пароль (оставьте пустым для сохранения текущего)" />
            </div>
            <div class="prof">
                <h3>Код скидки</h3>
                <input type="text" name="Discount_code" value="<?=$Discount_code?>" required />
            </div>
            <button type="submit">Сохранить изменения</button>
        </form>

    </section>

</body>
</html>