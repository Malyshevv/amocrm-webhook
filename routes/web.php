<?php

use App\Controllers\Home\HomeController;
use App\Controllers\Webhook\WebhookController;
use Slim\Factory\AppFactory;
use DI\Container;

// Подключаем автозагрузчик Composer
require __DIR__ . '/../vendor/autoload.php';

// Создаем контейнер зависимостей
$container = new Container();

// Устанавливаем контейнер в качестве контейнера по умолчанию для Slim
AppFactory::setContainer($container);

// Создаем экземпляр Slim приложения
$app = AppFactory::create();

// Загружаем настройки
$settings = require '../config/settings.php';
$container->set('settings', $settings['settings']);

$app->get('/', [HomeController::class, 'index']);
$app->post('/webhook', [WebhookController::class, 'handleWebhook']);

// Запускаем приложение
$app->run();
