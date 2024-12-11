<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Security-Policy" content="frame-ancestors 'self' https://oauth.telegram.org">  <!-- Добавлен meta-тег -->
  <title>Авторизация через Telegram</title>
</head>
<body>
  <h1>Авторизуйтесь через Telegram</h1>
  <div id="telegram-auth"></div>
  <script async src="https://telegram.org/js/telegram-widget.js?22" data-telegram-login="VolkovLapBot" data-size="large" data-userpic="false" data-onauth="onTelegramAuth(user)" data-request-access="write"></script>
  <script type="text/javascript">
    function onTelegramAuth(user) {
      console.log('Logged in as', user);
      fetch('login.php', { // Изменено: теперь отправляем запрос на login.php
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ telegramId: user.id, firstName: user.first_name, lastName: user.last_name, username: user.username })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = data.redirectUrl;
        } else {
          alert('Ошибка авторизации: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Ошибка:', error);
        alert('Ошибка авторизации.');
      });
    }
  </script>
</body>
</html>
<?php
session_start();
require_once('db.php'); // Подключение к базе данных

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $telegramId = $data['telegramId'];

    $stmt = $conn->prepare("SELECT Client_ID FROM Tenant WHERE telegram_id = ?");
    $stmt->execute([$telegramId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['Client_ID'] = $user['Client_ID'];
        echo json_encode(['success' => true, 'redirectUrl' => 'index.php']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Пользователь не найден']);
    }
}
?>