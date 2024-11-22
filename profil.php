<?php
session_start();
require_once('db.php'); 


$Client_ID = $_SESSION['Client_ID'];

$sql = "SELECT * FROM `Tenant` WHERE Client_ID = '$Client_ID'"; 
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); 
} else {
    echo "Пользователь не найден.";
    exit; 
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $Full_Name = $_POST['Full_Name'];
    $Address = $_POST['Address'];
    $Phone_Number = $_POST['Phone_Number'];
    $Discount_code = $_POST['Discount_code'];
    $Tenant_password = password_hash($_POST['Tenant_password'], PASSWORD_DEFAULT); 
    
    $update_sql = "UPDATE Tenant SET Full_Name='$Full_Name', Address='$Address', Phone_Number='$Phone_Number', Discount_Code='$Discount_code', Tenant_password='$Tenant_password' WHERE Client_ID='$Client_ID'"; // Измените Client_id на Client_ID

    if ($conn->query($update_sql) === TRUE) {
        echo "Данные успешно обновлены!";
        
        header("Location: profile.php");
        exit;
    } else {
        echo "Ошибка обновления: " . $conn->error;
    }
}
?>

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
                <input type="text" name="Full_Name" value="<?php echo htmlspecialchars($user_data['Full_Name']); ?>" required />
            </div>
            <div class="prof">
                <h3>Адрес</h3>
                <input type="text" name="Address" value="<?php echo htmlspecialchars($user_data['Address']); ?>" required />
            </div>
            <div class="prof">
                <h3>Номер телефона</h3>
                <input type="text" name="Phone_Number" value="<?php echo htmlspecialchars($user_data['Phone_Number']); ?>" required />
            </div>
            <div class="prof">
                <h3>Пароль</h3>
                <input type="password" name="Tenant_password" placeholder="Введите новый пароль (оставьте пустым для сохранения текущего)" />
            </div>
            <div class="prof">
                <h3>Код скидки</h3>
                <input type="text" name="Discount_code" value="<?php echo htmlspecialchars($user_data['Discount_Code']); ?>" />
            </div>
            <button type="submit">Сохранить изменения</button>
        </form>

    </section>

</body>
</html>
