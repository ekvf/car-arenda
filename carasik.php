<!DOCTYPE html>
<html>
<head>
<title>Аренда автомобиля</title>
</head>
<body>

<h1>Аренда автомобиля</h1>

<?php
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

?>

<form method="post" action="process_rental.php">

    <label for="Cars_ID">Выберите автомобиль:</label>
    <select id="Cars_ID" name="Cars_ID" required>
      <?php
      try {
          $stmtCars = $pdo->query("SELECT Cars_ID, Model, Color, Daily_Rate FROM Cars");
          while ($row = $stmtCars->fetch(PDO::FETCH_ASSOC)) {
              echo "<option value='" . $row['Cars_ID'] . "'>" . $row['Model'] . $row['Color'] . $row['Daily_Rate'] . "</option>";
          }
      } catch (PDOException $e) {
          die("Ошибка при получении списка автомобилей: " . $e->getMessage());
      }
      ?>
    </select><br><br>


    <label for="Rental_Date">Дата начала аренды:</label>
    <input type="date" id="Rental_Date" name="Rental_Date" required><br><br>

    <label for="Return_Date">Дата возврата:</label>
    <input type="date" id="Return_Date" name="Return_Date" required><br><br>

    <input type="submit" value="Забронировать">

</form>

</body>
</html>