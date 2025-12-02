<?php // 4 зд
require_once 'functions.php'; //Подключение внешнего PHP-файла
$id = $_GET['id'] ?? 0;
$item = loadSculptureById($id); //Загрузка данных о скульптуре
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Скульптура</title></head>
<body>
    <h2>Скульптура</h2>
    <?php displaySingleItem($item, 'sculptures.php'); ?> <!-- Вызов функции для отображения данных о скульптуре и URL-адрес -->
</body>
</html>