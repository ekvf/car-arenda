<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.kladr@0.2.2/jquery.kladr.min.js"></script>

</head>
<body>
    <?php
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('db.php');

$Client_ID = $_SESSION['Client_ID'];

$query = "SELECT Full_Name, Address, Phone_Number, Discount_code, Tenant_password FROM Tenant WHERE Client_ID = ?";
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

    $update_sql = "UPDATE Tenant 
                   SET Full_Name = ?, 
                       Address = ?,
                       Phone_Number = ?,
                       Discount_code = ?";

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql .= ", Tenant_password = ?";
    }

    $update_sql .= " WHERE Client_ID = ?";


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
class Kladr
{
    private $api_key;

    public function __construct()
    {
        //$this->api_key = KLADRAPIKEY; //не понадобился        
    }

    public function getStreet($street){
        $res=$this->request($street, "street", "2300000600000");

        return $res;
    }

    private function request($adress, $type, $cityid)
    {
        $res=[];
        $base_url = "https://kladr-api.ru/api.php?";
        $query_data = [
        //    'token'  => $this->api_key,
            'query'  => $adress,
            'contentType'   => !empty($type) ? $type : "street",
            'cityId' => !empty($cityid) ? $cityid : " 1100000100000",
        ];
        $data_get = http_build_query($query_data);
        $url = $base_url.$data_get;
        //query=Ломоносова &contentType=street &cityId= 1100000100000
       // $url="https://kladr-api.ru/api.php?query=Анапск&contentType=street&cityid= 1100000100000&limit=5";

        $ch = curl_init();
        $defaults = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_HTTPGET => true,
            CURLOPT_TIMEOUT => 10,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, $defaults);
        $response = json_decode(curl_exec($curl), true);

        $curlErrorNumber = curl_errno($curl);

        if ($curlErrorNumber) {
            $res['error']['msg'] = 'Error curl';
            $res['error']['uri'] = $url;
            $res['error']['curl_n'] = $curlErrorNumber;
            curl_close($curl);
      
        } else {
            $res = curl_exec($curl);
            curl_close($curl);
        }
    ?>

    <section class="container mt-4">
        <h2>Личные данные</h2>
        <form method="POST" class="needs-validation" novalidate>
            <!-- ... (другие поля формы) -->

            <div class="mb-3">
                <label for="Address" class="form-label">Адрес:</label>
                <input type="text" class="form-control" id="Address" name="Address" value="<?= $Address ?>" required>
                <div class="invalid-feedback">Пожалуйста, введите адрес.</div>
            </div>

            <!-- ... (другие поля формы) -->
        </form>
    </section>


    <script>
        $(document).ready(function() {
            $('#Address').kladr({
                token: 'ВАШ_ТОКЕН', // Замените на ваш токен
                type: $.kladr.type.ADDRESS,
                // Дополнительные настройки, если необходимо
                // Например, ограничение по региону:
                // region: '77', // Код региона Москвы
                oneString: true // Возвращать адрес одной строкой
            });
        });

        // ... (ваш остальной JavaScript код)
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>