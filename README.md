# Каталог книг на Yii2

Веб‑приложение на Yii2 (basic) и MySQL для ведения каталога книг с несколькими авторами, подписками гостей и SMS‑уведомлениями через SMSpilot. Рабочая версия, включая отправку SMS на номера из белого списка, доступна по адресу: https://book-catalog.sokoloff-rv.ru/.

## Возможности
- CRUD для книг и авторов для зарегистрированных пользователей.
- Подписка гостей и пользователей на конкретного автора по SMS с указанием номера телефона.
- Фильтрация книг по авторам.
- Отчет «ТОП‑10 авторов по количеству книг за выбранный год».
- Авторизация и разграничение прав доступа.
- Консольная команда `php yii seed/books <count>` для генерации тестовых авторов и книг.

## Технологии
- PHP 8+, Yii2 basic
- MySQL/MariaDB
- RBAC на `yii\rbac\DbManager`
- Загрузка настроек из `.env` (vlucas/phpdotenv)
- SMSPilot API (боевой режим или эмуляция)

## Как развернуть приложение
1. Подготовьте окружение: PHP 8+ с расширениями pdo_mysql и intl, Composer, MySQL (MariaDB).
2. Клонируйте репозиторий и установите зависимости:
   ```bash
   git clone git@github.com:sokoloff-rv/book-catalog.git
   cd book-catalog
   composer install
   ```
3. Создайте файл `.env` в корне проекта и пропишите настройки:
   ```env
   APP_ENV=prod
   YII_ENV=prod
   YII_DEBUG=false

   DB_DSN="mysql:host=localhost;dbname=db_name"
   DB_USERNAME="your_db_username"
   DB_PASSWORD="your_db_password"

   COOKIE_VALIDATION_KEY="your_random_secure_key"

   SMS_API_KEY=""
   SMS_EMULATOR_API_KEY=""

   ```
4. Создайте базу данных и укажите параметры подключения в `.env` файле.
5. Примените миграции (создают таблицы и права RBAC):
   ```bash
   php yii migrate
   ```
6. (Опционально) наполните БД тестовыми данными:
   ```bash
   php yii seed/books
   ```
   При стандартном сидировании будет создано 1000 книг. Вы можете указать любое другое количество, например, 200:
   ```bash
   php yii seed/books 200
   ```

## Настройки SMS
Сервис `SmsPilotService` берет ключ API из переменной `SMS_API_KEY`. Для безопасного тестирования оставьте его пустым, но пропишите тестовый ключ в `SMS_EMULATOR_API_KEY` — тогда сообщения будут логироваться в БД без реальной отправки.
