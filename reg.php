<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="https://unpkg.com/@blaze/css@12.2.0/dist/blaze/blaze.css">
    <script type="module" src="https://unpkg.com/@blaze/atoms@12.2.0/dist/blaze-atoms/blaze-atoms.esm.js"></script>
    <script nomodule="" src="https://unpkg.com/@blaze/atoms@12.2.0/dist/blaze-atoms/blaze-atoms.js"></script>
    <script src="node_modules/@blaze/atoms/dist/blaze-atoms.js"></script>
    <link rel="stylesheet" href="Style_Reg.css">
</head>

<body>
    <?php 
    session_start();
    require_once('db.php'); 
    $error_message = '';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $number = isset($_POST['login']) ? $_POST['login'] : ''; 
        $password = isset($_POST['password']) ? trim($_POST['password']) : ''; 

       
        if (empty($number) || empty($password)) { 
            $error_message = "Заполните все поля"; 
        } else {  
            
            $hashed_password = hash('sha256', $password);
            $sql = "INSERT INTO `Tenant` (Phone_Number, Tenant_password) VALUES ('$number', '$hashed_password')";
            $result = $conn->query($sql);
            
            if ($result) {
                $userId = $conn->insert_id; 
                $_SESSION['Client_ID'] = $userId;
                header("Location: login.php");
                exit;
            } else {
                $error_message = "Ошибка регистрации: " . $conn->error;
            }
        }
    }
    ?>

    <form class="o-container o-container--xsmall c-card u-high u-center-block" method='POST'> 
        <header class="c-card__header"> 
            <h2 class="c-heading">Регистрация</h2> 
        </header> 
        <div class="c-card__body"> 

            <?php if (!empty($error_message)): ?>
                <div class="c-alert c-alert--danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <div class="o-form-element"> 
                <label class="c-label">                 
                    <input class="c-field c-field--label" type="number" name='login' placeholder="Phone_number" value="" >
                    <div role="tooltip" class="c-hint">The number used to register the account</div> 
                </label> 
            </div> 

            <label class="o-form-element c-label">               
                <input class="c-field c-field--label" type="password" name='password' placeholder="Tenant_password" /> 
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
            <input type="submit" class="c-button c-button--brand c-button--block c-button--reg" value='Регистрация'> 
        </footer> 

    </form> 

</body>

</html>