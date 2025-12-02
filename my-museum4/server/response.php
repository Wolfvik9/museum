<?php
// server/response.php

// 1. Подключение к БД 
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'db_great_museums_async';

$mysqli = new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_error) {
    http_response_code(500);
    die("Ошибка подключения к БД: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

// -----------------------------------------------------------------
// НОВАЯ ЛОГИКА: ПРОВЕРКА, ЗАПРАШИВАЮТ ЛИ ОДИН ЭЛЕМЕНТ ИЛИ СПИСОК
// -----------------------------------------------------------------

if (isset($_GET['id'])) {
    
    // ==========================================================
    // РЕЖИМ 2: ЗАГРУЗКА ОДНОГО ЭЛЕМЕНТА (ПОЛНЫЙ ТЕКСТ)
    // ==========================================================
    
    $id = (int)$_GET['id'];
    $category_page = $_GET['category'] ?? 'Главная';

    // Загружаем один элемент по ID
    $stmt = $mysqli->prepare("SELECT title, text, image FROM publication WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

    // Генерируем HTML для одного элемента
    if (!$item) {
        echo "<p>Элемент не найден.</p>";
    } else {
        echo '<article class="single-item">';
        echo '  <h1 class="single-item__title">' . htmlspecialchars($item['title']) . '</h1>';

        if (!empty($item['image'])) {
            echo '  <div class="single-item__image-container">';
            echo '    <img src="images/' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['title']) . '" class="single-item__img">';
            echo '  </div>';
        }

        // --- ВЫВОДИМ ПОЛНЫЙ ТЕКСТ ---
        echo '  <div class="single-item__content">' . $item['text'] . '</div>';

        // АСИНХРОННАЯ Ссылка "Назад"
        echo '  <p><a href="#" class="async-back-link" data-page="' . htmlspecialchars($category_page) . '">Вернуться к списку</a></p>';
        echo '</article>';
    }

} else {

    // ==========================================================
    // РЕЖИМ 1: ЗАГРУЗКА СПИСКА (СОКРАЩЕННЫЙ ТЕКСТ)
    // ==========================================================

    $page = $_GET['page'] ?? 'Главная';

    $stmt = $mysqli->prepare("SELECT id, title, text, image, link, page AS page_type, category FROM publication WHERE page = ? OR category = ? ORDER BY id");
    $stmt->bind_param("ss", $page, $page);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = []; 
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    $stmt->close();

    // 3. Генерация HTML-ответа
    if (empty($items)) {
        echo "<p>Содержимое не найдено.</p>";
    } else {
        if ($items[0]['page_type'] === 'Главная' || $items[0]['page_type'] === 'Города') {
            foreach ($items as $item) {
                echo $item['text'];
            }
        } else {
            // Для остальных страниц
            foreach ($items as $item) {
                echo '<section class="article">';
                
                if (!empty($item['image'])) {
                    echo '<div class="article__float_left">';
                    echo '  <img src="images/' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['title']) . '" class="article__img">';
                    echo '</div>';
                }
                
                echo '<div class="article__content">';
                echo '<h3 class="article__title">' . htmlspecialchars($item['title']) . '</h3>';
                

                // --- НАЧАЛО ИСПРАВЛЕНИЯ (Убрали дублирование) ---
                
                // ЭТА СТРОКА БЫЛА ЛИШНЕЙ И УДАЛЕНА:
                // echo '<div class="article__text">' . $item['text'] . '</div>';
                
                // --- Теперь мы делаем анонс ---
                $snippet = strip_tags($item['text']);
                $maxLength = 300; 

                if (mb_strlen($snippet) > $maxLength) {
                    $snippet = mb_substr($snippet, 0, $maxLength);
                    $snippet .= '...';
                }
                
                // Выводим ТОЛЬКО ОДИН РАЗ наш обрезанный анонс
                echo '<div class="article__text">' . $snippet . '</div>';

                
                echo '<div class="article__links">';
                
                if ($item['page_type'] !== 'Великие музеи') { 
                     echo '  <a href="#" class="read-more" data-id="' . $item['id'] . '" data-category="' . htmlspecialchars($item['category']) . '">Подробнее...</a>';
                }

                if (!empty($item['link'])) {
                    echo '  <a href="' . htmlspecialchars($item['link']) . '" target="_blank" class="article__link">Смотреть в источнике</a>';
                }
                
                echo '</div>'; // Закрытие article__links
                echo '</div>'; // Закрытие article__content
                echo '</section>';
            }
        }
    }
}

// Закрываем соединение с БД в конце
$mysqli->close();
?>