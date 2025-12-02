<?php
require_once 'functions.php';

// Получаем ID из URL
$id = $_GET['id'] ?? null;

// Загружаем запись
$post = loadPostById($id);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>
        <?php echo $post ? htmlspecialchars($post['title']) : 'Статья не найдена'; ?> <!--Проверяет, не является ли $post пустым-->
    </title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1 { color: #2c3e50; }
        a { color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Просмотр статьи</h2>
    <?php displaySingleItem($post); ?>
</body>
</html>