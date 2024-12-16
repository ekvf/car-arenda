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
        header("Location: index_admin.php");
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
        header("Location: index_admin.php");
    } catch(PDOException $e) {
        echo "Error cancelling rental: " . $e->getMessage();
    }
}


// Вывод списка заявок
try {
    $stmt = $conn->query("SELECT * FROM Rented_Car WHERE status != 'cancelled' OR status IS NULL");
    $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching rentals: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Прокат автомобилей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            font-family: sans-serif;
        }
        .edit-form {
            display: none;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white p-3">
        <div class="container">
            <h1>Прокат автомобилей</h1>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Автомобили</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profil_admin.php">Профиль администратора</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <div class="container mt-4">
        <h2>Заявки на аренду автомобилей</h2>
        <table class="table table-striped">
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
                            <button type="button" class="btn btn-primary btn-sm" onclick="showEditForm(<?php echo $rental['ID']; ?>)">Редактировать</button>
                            <a href="index_admin.php?action=cancel&ID=<?php echo $rental['ID']; ?>" class="btn btn-danger btn-sm">Отменить</a>
                        </td>
                    </tr>
                    <tr class="edit-form" id="edit-form-<?php echo $rental['ID']; ?>">
                        <td colspan="8">
                            <form method="post" class="p-3">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="ID" value="<?php echo $rental['ID']; ?>">

                                <div class="mb-3">
                                    <label for="Cars_ID" class="form-label">ID автомобиля:</label>
                                    <input type="text" class="form-control" name="Cars_ID" value="<?php echo $rental['Cars_ID']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="Client_ID" class="form-label">ID клиента:</label>
                                    <input type="text" class="form-control" name="Client_ID" value="<?php echo $rental['Client_ID']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="Rental_Date" class="form-label">Дата аренды:</label>
                                    <input type="date" class="form-control" name="Rental_Date" value="<?php echo $rental['Rental_Date']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="Return_Date" class="form-label">Дата возврата:</label>
                                    <input type="date" class="form-control" name="Return_Date" value="<?php echo $rental['Return_Date']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="total_cost" class="form-label">Общая стоимость:</label>
                                    <input type="text" class="form-control" name="Total_Cost" value="<?php echo $rental['Total_Cost']; ?>">
                                </div>
                                <button type="submit" class="btn btn-success">Сохранить</button>
                                <button type="button" class="btn btn-secondary" onclick="hideEditForm(<?php echo $rental['ID']; ?>)">Отмена</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function showEditForm(id) {
            document.getElementById('edit-form-' + id).style.display = 'table-row';
        }

        function hideEditForm(id) {
            document.getElementById('edit-form-' + id).style.display = 'none';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>