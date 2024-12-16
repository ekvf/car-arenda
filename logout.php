<?php
session_start(); // Начинаем сессию
session_unset(); // Удаляем все переменные сессии
session_destroy(); // Уничтожаем сессию

header("Location: login.php"); // Перенаправляем на страницу входа
exit;
?>