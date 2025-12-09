
let gameArea = document.getElementById('gameArea');
let ship;
let asteroids = [];
let bullets = [];
let score = 0;

let asteroidInterval;
let scoreInterval;
let gameInterval;

const scoreDisplay = document.getElementById('score'); // получение элемента отображения счёта по ID
const finalScoreDisplay = document.getElementById('finalScore'); 
const gameOverScreen = document.getElementById('gameOver');
const startBtn = document.getElementById('startBtn');
const restartGameBtn = document.getElementById('restartGameBtn');

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

// клава
let keys = { left: false, right: false }; // объект для хранения состояний клавиш

document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft' || e.key === 'a') keys.left = true;
    if (e.key === 'ArrowRight' || e.key === 'd') keys.right = true;
    if (e.key === ' ') {
        e.preventDefault(); 
        shoot();
    }
});

document.addEventListener('keyup', (e) => {
    if (e.key === 'ArrowLeft' || e.key === 'a') keys.left = false;
    if (e.key === 'ArrowRight' || e.key === 'd') keys.right = false;
});

// стрельба
function shoot() {
    if (!ship) return;
    const bullet = document.createElement('div');
    bullet.classList.add('bullet');
    bullet.style.left = (parseInt(ship.style.left) + 27) + 'px'; 
    bullet.style.bottom = '70px';
    gameArea.appendChild(bullet); // добавление пули в игровое поле
    bullets.push(bullet);// в массиве
}

//движение корабля
function moveShip() {
    if (!ship) return;
    let x = parseInt(ship.style.left); //текущая позиция
    if (keys.left) x -= 6; //если клавиша нажата
    if (keys.right) x += 6;
    x = Math.max(0, Math.min(gameArea.clientWidth - 60, x)); // ограничение позиции
    ship.style.left = x + 'px'; // обноление поз
}

// астероид
function createAsteroid() {
    const a = document.createElement('div');
    a.classList.add('asteroid');
    a.style.left = getRandomInt(0, gameArea.clientWidth - 50) + 'px';
    a.style.top = '-60px'; 
    a.dataset.speed = getRandomInt(2, 6);//скорость
    gameArea.appendChild(a);
    asteroids.push(a);
}

//движение пуль
function moveBullets() {
    for (let i = bullets.length - 1; i >= 0; i--) {
        let b = bullets[i];//текущая пуля
        let bottom = parseInt(b.style.bottom);//получ позиция
        bottom += 10; //движение вверх
        b.style.bottom = bottom + 'px'; //обновление позиции
        if (bottom > gameArea.clientHeight) {//удаление за экр
            b.remove();
            bullets.splice(i, 1);
        }
    }
}

// убитие 
function moveAsteroids() {
    for (let i = asteroids.length - 1; i >= 0; i--) {
        const ast = asteroids[i];
        const speed = parseInt(ast.dataset.speed); //получение скорости астероида
        let top = parseInt(ast.style.top); // и текущей позиции
        top += speed; //движение вниз
        ast.style.top = top + 'px';

        // удалить за экраном
        if (top > gameArea.clientHeight) {
            ast.remove();
            asteroids.splice(i, 1);
            continue;
        }

        // попадание пули
        for (let j = bullets.length - 1; j >= 0; j--) {
            if (checkCollision(bullets[j], ast)) { //проверка столкновения пули и астероида
                createExplosion(ast.style.left, ast.style.top);//взрыв
                ast.remove();// -аст
                bullets[j].remove(); // -пули
                asteroids.splice(i, 1);
                bullets.splice(j, 1);
                score += 10; //+10 за уничтожение
                scoreDisplay.textContent = score; //обновление отображения счета
                break;
            }
        }

        // столкновение с кораблём
        if (checkCollision(ship, ast)) {
            endGame();
        }
    }
}

// бум
function createExplosion(x, y) {
    const exp = document.createElement('div');
    exp.classList.add('explosion');
    exp.style.left = (parseInt(x) - 30) + 'px'; //по горизонтили
    exp.style.top = (parseInt(y) - 30) + 'px'; //по вертикали
    gameArea.appendChild(exp); //добавление взрыва
    setTimeout(() => exp.remove(), 600);
}

// проверка столкновения
function checkCollision(el1, el2) {
    const r1 = el1.getBoundingClientRect();
    const r2 = el2.getBoundingClientRect();
    return !(r1.right < r2.left || r1.left > r2.right || r1.bottom < r2.top || r1.top > r2.bottom);
}//возврат true, если прямоугольники пересекаются

// обновление игры 
function updateGame() {
    moveShip();
    moveAsteroids();
    moveBullets();
}

// старт
function startGame() {
    // сброс
    score = 0;
    scoreDisplay.textContent = '0';
    asteroids = [];
    bullets = [];
    gameArea.innerHTML = '';  // очищаем поле
    gameOverScreen.style.display = 'none';

    // корабль
    ship = document.createElement('div');
    ship.classList.add('ship');
    ship.style.left = (gameArea.clientWidth / 2 - 30) + 'px';//начальная позиция корабля
    ship.style.bottom = '20px'; //установка позиции снизу
    gameArea.appendChild(ship);

    // восстанавливаем Game Over экран
    const gameOverDiv = document.createElement('div');
    gameOverDiv.className = 'game-over';
    gameOverDiv.id = 'gameOver';
    gameOverDiv.innerHTML = `
        <h2>GAME OVER</h2>
        <p>Ваш счёт: <span id="finalScore">0</span></p>
        <button id="restartGameBtn">Играть снова</button>
    `;
    gameArea.appendChild(gameOverDiv);//добавление

    // Перепривязываем кнопку
    document.getElementById('restartGameBtn').onclick = startGame;

    // запуск интервалов создания астероидов каждые 800
    asteroidInterval = setInterval(createAsteroid, 800);
    scoreInterval = setInterval(() => {
        score++;
        scoreDisplay.textContent = score;
    }, 1000);
    gameInterval = setInterval(updateGame, 30);  // игрового цикла каждые 30 мс плавное движ
}

// конец
function endGame() {
    clearInterval(asteroidInterval);
    clearInterval(scoreInterval);
    clearInterval(gameInterval);

    finalScoreDisplay.textContent = score;
    gameOverScreen.style.display = 'flex';
    alert("Game Over! Score: " + score);
}

// кнопки добавления слушателй
startBtn.addEventListener('click', startGame);
restartGameBtn.addEventListener('click', startGame);