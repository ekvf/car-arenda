<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('db.php');

$Client_ID = $_SESSION['Client_ID'];

$query = "SELECT `Full_Name`, `Address`, `Phone_Number`, `Discount_code`, `Tenant_password` FROM `Tenant` WHERE `Client_ID` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $Client_ID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $Full_Name = $row['Full_Name'];
    $Address = $row['Address'];
    $Phone_Number = $row['Phone_Number'];
    $Discount_code = $row['Discount_code'];
} else {
    // Обработка случая, когда пользователь не найден
    die("Пользователь не найден");
}
$stmt->close();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Full_Name = isset($_POST['Full_Name']) ? trim($_POST['Full_Name']) : '';
    $Address = isset($_POST['Address']) ? trim($_POST['Address']) : '';
    $Phone_Number = isset($_POST['Phone_Number']) ? trim($_POST['Phone_Number']) : '';
    $Discount_code = isset($_POST['Discount_code']) ? trim($_POST['Discount_code']) : '';
    $new_password = isset($_POST['Tenant_password']) ? trim($_POST['Tenant_password']) : ''; // Изменено: теперь просто получаем новый пароль

    $update_sql = "UPDATE `Tenant` 
                   SET `Full_Name` = ?, 
                       `Address` = ?,
                       `Phone_Number` = ?,
                       `Discount_code` = ?";

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql .= ", `Tenant_password` = ?";
    }

    $update_sql .= " WHERE `Client_ID` = ?";


    $stmt = $conn->prepare($update_sql);

    if ($stmt) {
        if (!empty($new_password)) {
            $stmt->bind_param("sssssi", $Full_Name, $Address, $Phone_Number, $Discount_code, $hashed_password, $Client_ID);
        } else {
            $stmt->bind_param("sssi", $Full_Name, $Address, $Phone_Number, $Discount_code, $Client_ID);
        }

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            echo "Ошибка выполнения запроса: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Ошибка подготовки запроса: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Профиль</title>
</head>
<body>
    <header class="bg-primary text-white p-3">
        <div class="container">
            <h1>Профиль</h1>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">На Главную</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Выйти</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <section class="container mt-4">
        <h2>Личные данные</h2>
        <form method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="Full_Name" class="form-label">ФИО:</label>
                <input type="text" class="form-control" id="Full_Name" name="Full_Name" value="<?= $Full_Name ?>" required>
                <div class="invalid-feedback">Пожалуйста, введите ФИО.</div>
            </div>
            <div class="mb-3">
                <label for="Address" class="form-label">Адрес:</label>
                <input type="text" class="form-control" id="Address" name="Address" value="<?= $Address ?>" required>
                <div class="invalid-feedback">Пожалуйста, введите адрес.</div>
            </div>
            <div class="mb-3">
                <label for="Phone_Number" class="form-label">Номер телефона:</label>
                <input type="tel" class="form-control" id="Phone_Number" name="Phone_Number" value="<?= $Phone_Number ?>" required>
                <div class="invalid-feedback">Пожалуйста, введите номер телефона.</div>
            </div>
            <div class="mb-3">
                <label for="Tenant_password" class="form-label">Пароль:</label>
                <input type="password" class="form-control" id="Tenant_password" name="Tenant_password" placeholder="Введите новый пароль (оставьте пустым для сохранения текущего)">
            </div>
            <div class="mb-3">
                <label for="Discount_code" class="form-label">Код скидки:</label>
                <input type="text" class="form-control" id="Discount_code" name="Discount_code" value="<?= $Discount_code ?>" required>
                <div class="invalid-feedback">Пожалуйста, введите код скидки.</div>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>
    </section>

    <script>
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>