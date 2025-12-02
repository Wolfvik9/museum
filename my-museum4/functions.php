<?php

// Функцию подключения к БД 
function getDbConnection() {
    $host = 'localhost';
    $user = 'root';
    $pass = 'root'; // Для MAMP пароль
    $dbname = 'db_great_museums_async'; //  имя  БД

    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    return $conn;
}

// Загрузка одной статьи
function loadArticleById($id) {
    $id = (int)$id;
    if ($id <= 0) return null;
    $db = getDbConnection(); //	Получение подключения к БД
    $stmt = $db->prepare("SELECT id, title, text AS content, image FROM publication WHERE id = ? AND category = 'Статьи'");
    $stmt->bind_param("i", $id);
    $stmt->execute(); //Выполнение запроса к базе данных.
    $result = $stmt->get_result();
    $item = $result->fetch_assoc(); //Получение результата
    $stmt->close(); $db->close(); //	Закрытие ресурсов:
    return $item;
}

// Загрузка одной картины
function loadPaintingById($id) {
    $id = (int)$id;
    if ($id <= 0) return null;
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT id, title, text AS content, image FROM publication WHERE id = ? AND category = 'Живопись'");
    $stmt->bind_param("i", $id);
    $stmt->execute(); //Выполнение запроса
    $result = $stmt->get_result();
    $item = $result->fetch_assoc(); //	Извлечение данных
    $stmt->close(); $db->close(); 
    return $item;
}

// Загрузка одной скульптуры
function loadSculptureById($id) {
    $id = (int)$id;
    if ($id <= 0) return null;
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT id, title, text AS content, image FROM publication WHERE id = ? AND category = 'Скульптура'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close(); $db->close();
    return $item;
}

// Функция для отображения одного элемента (статьи, картины и т.д.)
function displaySingleItem($item, $back_url = '#') {
    if (!$item) {
        echo "<p>Элемент не найден.</p>";
        // Если передан URL для возврата, можно добавить ссылку
        if ($back_url !== '#') {
            echo '<p><a href="' . htmlspecialchars($back_url) . '">Вернуться к списку</a></p>';
        }
        return;
    }

    echo '<article class="single-item">';

    // 1. Заголовок
    echo '  <h1 class="single-item__title">' . htmlspecialchars($item['title']) . '</h1>';

    // 2. Изображение (если есть)
    if (!empty($item['image'])) {
        echo '  <div class="single-item__image-container">';
        // Используем класс для стилизации
        echo '    <img src="images/' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['title']) . '" class="single-item__img">';
        echo '  </div>';
    }

    // 3. Содержание/Текст
    // Используем 'content' как псевдоним в функциях load*ById
    echo '  <div class="single-item__content">' . $item['content'] . '</div>';

    // 4. Ссылка "Назад"
    if ($back_url !== '#') {
        echo '  <p><a href="' . htmlspecialchars($back_url) . '">Вернуться к списку</a></p>';
    }

    echo '</article>';
}

?>