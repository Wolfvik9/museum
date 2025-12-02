<?php //4 зд
require_once 'functions.php';
$id = $_GET['id'] ?? 0;
$item = loadPaintingById($id);
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Живопись</title></head>
<body>
    <h2>Живопись</h2>
    <?php displaySingleItem($item, 'paintings.php'); ?>
</body>
</html>