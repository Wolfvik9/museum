<?php //4 зд
require_once 'functions.php';
$id = $_GET['id'] ?? 0;
$item = loadArticleById($id);
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Статья</title></head>
<body>
    <h2>Статья</h2>
    <?php displaySingleItem($item, 'articles.php'); ?>
</body>
</html> 