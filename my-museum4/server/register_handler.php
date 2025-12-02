<?php
// server/register_handler.php

header('Content-Type: application/json'); // Указываем, что ответ будет в формате JSON

// Подключаем функцию для подключения к БД
require_once('../functions.php'); 

// Проверка метода запроса (должен быть POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса.']);
    exit;
}

// 1. Получение и фильтрация данных (включая email)
$login = trim($_POST['login'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// 2. Базовая валидация данных
if (empty($login) || empty($email) || empty($password) || empty($password_confirm)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Заполните все поля.']);
    exit;
}

// 3. Проверка совпадения паролей (повторная серверная проверка)
if ($password !== $password_confirm) {
    http_response_code(400);
    // Это сообщение не должно выводиться, так как его отловит JS, но на всякий случай
    echo json_encode(['success' => false, 'message' => 'Пароли не совпадают.']);
    exit;
}

// --- 4. Хеширование и сохранение ---

// Используем безопасное хеширование, которое игнорирует отдельную соль
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$role = 'Пользователь';
$salt = ''; // Оставляем пустым, т.к. соль включена в хеш

try {
    $db = getDbConnection(); // Получение подключения к БД
    
    // Проверка, не занят ли логин
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким логином уже существует.']);
        $db->close();
        exit;
    }

    // Вставка нового пользователя в таблицу users
    // ВНИМАНИЕ: Поля в вашем дампе: `id`, `login`, `password`, `salt`, `email`, `role`
    $stmt = $db->prepare("INSERT INTO users (login, password, salt, email, role) VALUES (?, ?, ?, ?, ?)");
    
    // "sssss" - 5 строк: login, password_hash, salt, email, role
    $stmt->bind_param("sssss", $login, $password_hash, $salt, $email, $role);
    
    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(['success' => true, 'message' => 'Регистрация прошла успешно.']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении пользователя: ' . $db->error]);
    }
    
    $stmt->close();
    $db->close();

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Серверная ошибка: ' . $e->getMessage()]);
}

?>