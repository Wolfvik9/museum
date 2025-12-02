<?php
// register.php
// Этот файл содержит форму регистрации и JS-логику для её отправки.
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация пользователя</title>
    <link rel="stylesheet" href="css/style.css"> 
    <style>
        /* Дополнительные стили для формы (можете добавить их в style.css) */
        .registration-form-container {
            max-width: 400px;
            margin: 100px auto 50px; /* Отступ сверху, чтобы не закрывалось шапкой */
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #e37f3d; /* Цвет, похожий на вашу навигацию */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s;
        }
        .form-group button:hover { background-color: #b96f29; }
        .form-title { text-align: center; color: #4b2914; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="registration-form-container">
        <h2 class="form-title">Регистрация</h2>
        
        <form id="registration-form" action="server/register_handler.php" method="POST">
            
            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" required>
            </div>
            
            <div class="form-group">
                <label for="email">Почта:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Подтверждение пароля:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            
            <div class="form-group">
                <button type="submit">Зарегистрироваться</button>
            </div>
            
            <p style="text-align: center; font-size: 14px;">
                У вас уже есть аккаунт? <a href="#" class="async-login-link">Вход</a>
            </p>
        </form>
    </div>

    <script>
        document.getElementById('registration-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const password = form.password.value;
            const password_confirm = form.password_confirm.value;

            // 1. Проверка на совпадение паролей (по заданию - через alert)
            if (password !== password_confirm) {
                alert('Предупреждение: Пароли не совпадают!'); // Рис. 2
                return;
            }

            try {
                // Отправка данных асинхронно
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                // Сервер должен вернуть JSON
                const result = await response.json();
                
                // 2. Обработка ответа
                if (response.ok && result.success) {
                    alert('Сообщение: Пользователь успешно зарегистрирован!'); // Рис. 3
                    form.reset(); // Очистить форму
                } else {
                    // Вывод ошибки, пришедшей от сервера
                    alert('Ошибка регистрации: ' + (result.message || 'Неизвестная ошибка.'));
                }
            } catch (error) {
                console.error('Ошибка при отправке данных:', error);
                alert('Произошла ошибка сети или сервера. Проверьте путь к register_handler.php');
            }
        });
    </script>
</body>
</html>