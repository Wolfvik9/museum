// js/main.js
document.addEventListener('DOMContentLoaded', () => {
    const mainContent = document.querySelector('main.container');
    const pageTitle = document.getElementById('page-title');

    // Функция для загрузки СПИСКА (Статьи, Живопись, и т.д.)
    async function loadContent(page) {
        mainContent.innerHTML = '<h2 class="title__heading">Загрузка...</h2>';

        try {
            const response = await fetch(`server/response.php?page=${encodeURIComponent(page)}`);
            if (!response.ok) {
                throw new Error(`Ошибка сети: ${response.status}`);
            }
            const html = await response.text();
            
            mainContent.innerHTML = html;
            pageTitle.textContent = page;

            // -----------------------------------------------------------------
            // !!! ИНТЕГРАЦИЯ ПЛАГИНА (Практическая работа №11) !!!
            // -----------------------------------------------------------------
            // Проверяем, что функция плагина существует (подключен plugin.js)
            // И что мы находимся на странице со списком статей.
            if (typeof ReorderPlugin === 'function' && page !== 'Главная' && !page.includes('Музеи')) {
                // Вызываем плагин для реверсивной сортировки
                // Контейнер: 'main.container', Элементы: '.article' (класс, используемый в response.php)
                 ReorderPlugin('main.container', '.article');
            }
            // -----------------------------------------------------------------


        } catch (error) {
            console.error('Ошибка при загрузке контента:', error);
            mainContent.innerHTML = '<p>Не удалось загрузить содержимое. Попробуйте снова.</p>';
        }
    }

    // -----------------------------------------------------------------
    // НОВАЯ ФУНКЦИЯ (ЗАДАНИЕ №4)
    // -----------------------------------------------------------------
    // Функция для загрузки ОДНОГО ЭЛЕМЕНТА ("Подробнее...")
    async function loadSingleItem(id, category) {
        mainContent.innerHTML = '<h2 class="title__heading">Загрузка...</h2>';

        try {
            // Запрашиваем контент с сервера, передавая ID и Категорию (для кнопки "Назад")
            const response = await fetch(`server/response.php?id=${id}&category=${encodeURIComponent(category)}`);
            if (!response.ok) {
                throw new Error(`Ошибка сети: ${response.status}`);
            }
            const html = await response.text(); 
            
            // Вставляем готовый HTML в контейнер
            mainContent.innerHTML = html;
            
            // Обновляем заголовок на странице (берем его из h1, который прислал сервер)
            const newTitle = mainContent.querySelector('h1.single-item__title');
            pageTitle.textContent = newTitle ? newTitle.textContent : 'Просмотр';

        } catch (error) {
            console.error('Ошибка при загрузке элемента:', error);
            mainContent.innerHTML = '<p>Не удалось загрузить содержимое. Попробуйте снова.</p>';
        }
    }


    // -----------------------------------------------------------------
    // ИЗМЕНЕННАЯ ЛОГИКА ОБРАБОТКИ КЛИКОВ (ДЕЛЕГИРОВАНИЕ)
    // -----------------------------------------------------------------
    // Один обработчик кликов на весь документ
    document.addEventListener('click', (event) => {
        const target = event.target; // Элемент, по которому кликнули

        // 1. Клик по ГЛАВНОЙ НАВИГАЦИИ (e.g., "Статьи", "Живопись")
        if (target.matches('.nav__link[data-page]')) {
            event.preventDefault(); 
            const page = target.dataset.page;
            if (page) {
                loadContent(page);
            }
        }

        // 2. Клик по ссылке "ПОДРОБНЕЕ" (ЗАДАНИЕ №4)
        if (target.matches('.read-more[data-id]')) {
            event.preventDefault();
            const id = target.dataset.id;
            const category = target.dataset.category; // Категория нужна для кнопки "Назад"
            if (id && category) {
                loadSingleItem(id, category);
            }
        }

        // 3. Клик по ссылке "ВЕРНУТЬСЯ К СПИСКУ" (ЗАДАНИЕ №4)
        if (target.matches('.async-back-link[data-page]')) {
            event.preventDefault();
            const page = target.dataset.page; // Загружаем страницу, с которой пришли
            if (page) {
                loadContent(page);
            }
        }
    });

    // Загружаем контент для главной страницы при первом входе
    loadContent('Главная');
});