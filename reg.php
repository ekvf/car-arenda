<?php
session_start();
require_once('db.php');
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $number = isset($_POST['login']) ? $_POST['login'] : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = isset($_POST['role']) ? (int)$_POST['role'] : 0; // Получаем роль

    if (empty($number) || empty($password) || $role === 0) {
        $error_message = "Заполните все поля и выберите роль";
    } else {
        // Проверка существования пользователя с таким номером телефона
        $stmt = $conn->prepare("SELECT COUNT(*) FROM `Tenant` WHERE `Phone_Number` = ?");
        $stmt->bind_param("i", $number);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $error_message = "Пользователь с таким номером телефона уже существует";
        } else {
            // Хеширование пароля с помощью password_hash()
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Подготовленный запрос с добавлением роли
            $stmt = $conn->prepare("INSERT INTO `Tenant` (Phone_Number, Tenant_password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $number, $hashed_password, $role); // i - integer, s - string
            $result = $stmt->execute();

            if ($result) {
                $userId = $conn->insert_id;
                $_SESSION['Client_ID'] = $userId;
                header("Location: login.php");
                exit;
            } else {
                $error_message = "Ошибка регистрации: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <h1>Регистрация</h1>
</head>
<body>
    <form method="POST">
        <label for="login">Номер телефона:</label>
        <input type="number" id="login" name="login" required><br><br>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="role">Роль:</label>
        <select id="role" name="role" required>
            <option value="1">Пользователь</option>
            <option value="2">Администратор</option>
        </select><br><br>

        <input type="submit" value="Зарегистрироваться">
    </form>
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
</body>
</html>