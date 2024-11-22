<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="https://unpkg.com/@blaze/css@12.2.0/dist/blaze/blaze.css">
    <script type="module" src="https://unpkg.com/@blaze/atoms@12.2.0/dist/blaze-atoms/blaze-atoms.esm.js"></script>
    <script nomodule="" src="https://unpkg.com/@blaze/atoms@12.2.0/dist/blaze-atoms/blaze-atoms.js"></script>
    <script src="node_modules/@blaze/atoms/dist/blaze-atoms.js"></script>
    <link rel="stylesheet" href="Style_Auth.css">
</head>

<body>
    <?php 
        session_start();
        require_once('db.php'); 

        $login = isset($_POST['login']) ? $_POST['login'] : ''; 
        $pass = isset($_POST['pass']) ? trim($_POST['pass']) : ''; 
        $error_message = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Проверка на пустые поля
            if (empty($login) || empty($pass)) { 
                $error_message = "Заполните все поля"; 
            } else { 
                // Хешируем пароль
                $hashed_pass = hash('sha256', $pass);
                $sql = "SELECT * FROM `Tenant` WHERE Phone_Number = '$login' AND Tenant_password = '$hashed_pass'"; 
                $result = $conn->query($sql); 

                if ($result->num_rows > 0) { 
                    while ($row = $result->fetch_assoc()) { 
                        $_SESSION['user_id'] = $row['id']; 
                        header("Location: index.php"); 
                        exit; 
                    } 
                } else {
                    // Неверный логин или пароль
                    $error_message = "Неверный логин или пароль"; 
                }
            } 
        }
    ?>

    <form class="o-container o-container--xsmall c-card u-high u-center-block" method='POST'> 
        <header class="c-card__header"> 
            <h2 class="c-heading">Авторизация</h2> 
        </header> 
        <div class="c-card__body"> 

            <?php if (!empty($error_message)): ?>
                <div class="c-alert c-alert--danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="o-form-element"> 
                <label class="c-label">                 
                    <input class="c-field c-field--label" name='login' type="number" placeholder="Логин" value="<?php echo htmlspecialchars($login); ?>" /> 
                    <div role="tooltip" class="c-hint">The email used to register the account</div> 
                </label> 
            </div> 

            <label class="o-form-element c-label">               
                <input class="c-field c-field--label" type="password" name='pass' placeholder="Пароль" /> 
            </label> 

            <div class="o-form-element"> 
                <label class="c-toggle c-toggle--success">Запомнить меня? 
                    <input type="checkbox" checked /> 
                    <div class="c-toggle__track"> 
                        <div class="c-toggle__handle"></div> 
                    </div> 
                </label> 
            </div> 

        </div> 
        
        <footer class="c-card__footer"> 
            <input type="submit" value='Вход' class="c-button c-button--brand c-button--block c-button--vhod "> 

            </input> 

            <a href="reg.php"> 
                <input type="button" value='Регистрация' class="c-button c-button--brand c-button--block c-button--reg "></input> 
            </a> 

        </footer> 

    </form> 

</body>

</html>
