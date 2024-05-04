# Приложение для работы с amoCRM 🚀

## Как использовать ? 🛠️

### Запуск из IDE
Для запуска приложения из вашей среды разработки (IDE) используйте следующую команду:

```shell
php -S localhost:8000 -t public
```

### Запуск через Docker 🐳
Для запуска приложения через Docker выполните следующие шаги:
1. Перейдите в папку docker.
2. Запустите Docker Compose с помощью следующей команды:

```shell
docker-compose up -d --build
```

## Настройка интеграции ⚙️

Для правильной работы приложения с amoCRM выполните следующие шаги:

1. Откройте файл `./config/settings.php`.
2. Укажите свои значения для соответствующих настроек.

### Настройка интеграции в amoCRM

1. Перейдите по ссылке [https://malyshevdev.amocrm.ru/amo-market/](https://malyshevdev.amocrm.ru/amo-market/).
2. Нажмите на "...".
3. Создайте интеграцию и выберите "Внешняя интеграция".
4. Введите свои данные и выберите необходимые разрешения.
5. Нажмите "Установленные" в разделе `/amo-market/`.
6. Выберите вашу интеграцию.
7. Поставьте галку "Согласен" и нажмите "Установить".

p.s Ключи доступа находятся в окне интеграции в разделе "Ключи доступа".

### Настройка Webhook в amoCRM

1. Перейдите по ссылке [https://malyshevdev.amocrm.ru/amo-market/](https://malyshevdev.amocrm.ru/amo-market/).
2. Нажмите на "+ WEB HOOK".
3. Введите вашу ссылку и выберите события.

## Функции данного приложения

Это приложение реагирует на следующие события webhook в amoCRM:

- Сделка добавлена
- Сделка изменена
- Контакт добавлен
- Контакт изменен