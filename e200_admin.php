<?php
$servername = "localhost";
$username = "volkov.d.r";
$password = "2345f";
$dbname = "volkov.d.r";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


// Обработка редактирования заявки
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $ID = $_POST['ID'];
    $cars_id = $_POST['Cars_ID'];
    $client_id = $_POST['Client_ID'];
    $rental_date = $_POST['Rental_Date'];
    $return_date = $_POST['Return_Date'];
    $total_cost = $_POST['Total_Cost'];

    try {
        $stmt = $conn->prepare("UPDATE Rented_Car SET Cars_ID = :Cars_ID, Client_ID = :Client_ID, Rental_Date = :Rental_Date, Return_Date = :Return_Date, Total_Cost = :Total_Cost WHERE ID = :ID");
        $stmt->bindParam(':Cars_ID', $cars_id);
        $stmt->bindParam(':Client_ID', $client_id);
        $stmt->bindParam(':Rental_Date', $rental_date);
        $stmt->bindParam(':Return_Date', $return_date);
        $stmt->bindParam(':Total_Cost', $total_cost);
        $stmt->bindParam(':ID', $ID);
        $stmt->execute();
        header("Location: prius_admin.php");
    } catch(PDOException $e) {
        echo "Error updating rental: " . $e->getMessage();
    }
}


// Обработка отмены заявки
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['ID'])) {
    $rental_id = $_GET['ID'];
    try {
        $stmt = $conn->prepare("UPDATE Rented_Car SET status = 'cancelled' WHERE ID = :ID");
        $stmt->bindParam(':ID', $rental_id);
        $stmt->execute();
        header("Location: prius_admin.php");
    } catch(PDOException $e) {
        echo "Error cancelling rental: " . $e->getMessage();
    }
}


// Вывод списка заявок
try {
    $stmt = $conn->query("SELECT * FROM Rented_Car");
    $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error fetching rentals: " . $e->getMessage();
}

?>

 <li><a href="index_admin.php">На Главную</a></li>
<h2>Заявки на аренду автомобилей</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>ID автомобиля</th>
            <th>ID клиента</th>
            <th>Дата аренды</th>
            <th>Дата возврата</th>
            <th>Общая стоимость</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rentals as $rental): ?>
            <tr>
                <td><?php echo $rental['ID']; ?></td>
                <td><?php echo $rental['Cars_ID']; ?></td>
                <td><?php echo $rental['Client_ID']; ?></td>
                <td><?php echo $rental['Rental_Date']; ?></td>
                <td><?php echo $rental['Return_Date']; ?></td>
                <td><?php echo $rental['Total_Cost']; ?></td>
                <td><?php echo $rental['status'] ?? 'active'; ?></td>
                <td>
                    <a href="#" onclick="showEditForm(<?php echo $rental['ID']; ?>)">Редактировать</a> | 
                    <a href="prius_admin.php?action=cancel&ID=<?php echo $rental['ID']; ?>">Отменить</a>
                </td>
            </tr>
            <tr id="edit-form-<?php echo $rental['ID']; ?>" style="display:none;">
                <td colspan="8">
                    <form method="post">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="ID" value="<?php echo $rental['ID']; ?>">
                        <label for="Cars_ID">ID автомобиля:</label>
                        <input type="text" name="Cars_ID" value="<?php echo $rental['Cars_ID']; ?>"><br><br>
                        <label for="Client_ID">ID клиента:</label>
                        <input type="text" name="Client_ID" value="<?php echo $rental['Client_ID']; ?>"><br><br>
                        <label for="Rental_Date">Дата аренды:</label>
                        <input type="date" name="Rental_Date" value="<?php echo $rental['Rental_Date']; ?>"><br><br>
                        <label for="Return_Date">Дата возврата:</label>
                        <input type="date" name="Return_Date" value="<?php echo $rental['Return_Date']; ?>"><br><br>
                        <label for="total_cost">Общая стоимость:</label>
                        <input type="text" name="Total_Cost" value="<?php echo $rental['Total_Cost']; ?>"><br><br>
                        <input type="submit" value="Сохранить">
                        <button type="button" onclick="hideEditForm(<?php echo $rental['ID']; ?>)">Отмена</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
function showEditForm(id) {
    document.getElementById('edit-form-' + id).style.display = 'table-row';
}

function hideEditForm(id) {
    document.getElementById('edit-form-' + id).style.display = 'none';
}
</script>
