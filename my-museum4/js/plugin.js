
function ReorderPlugin(containerSelector, itemSelector) {
    // 1. Нахожу контейнер
    const container = document.querySelector(containerSelector);

    if (!container) {
        console.error('Контейнер не найден.');
        return;
    }

    // 2. Нахожу все элементы внутри контейнера
    const items = Array.from(container.querySelectorAll(itemSelector));

    // 3. ДОЛЖНО БЫТЬ РОВНО 6 ЭЛЕМЕНТОВ
    if (items.length !== 6) {
        console.warn(`Ожидалось 6 элементов для специальной сортировки, но найдено ${items.length}. Сортировка отменена.`);
        return;
    }

    // 4. Задаю новый порядок
    const orderedItems = [
        items[4], // 5 элемент ("Знаменитые музеи...")
        items[2], // 3 элемент ("Кто такие передвижники...")
        items[5], // 6 элемент ("В Лувр без очередей...")
        items[0], // 1 элемент ("Художник и его талант.")
        items[3], // 4 элемент ("Что такое модульная картина...")
        items[1]  // 2 элемент ("Загадка картины «Моны Лизы»...")
    ];

    // 5. Перемещаем элементы в новом порядке
    orderedItems.forEach(item => {
        container.appendChild(item);
    });

    console.log(`Порядок ${items.length} элементов успешно изменен`);
}
