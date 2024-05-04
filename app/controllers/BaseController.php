<?php
namespace App\Controllers;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\Exceptions\InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

abstract class BaseController
{
	protected ContainerInterface $container;
	protected LoggerInterface $logger;
	public AmoCRMApiClient $amo;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
		$this->logger = $this->createLogger();

		$amoSettings = $this->settings['amocrm'];

		$this->amo = new AmoCRMApiClient($amoSettings['clientId'], $amoSettings['clientSecret'], $amoSettings['redirectUri']);
		$longLivedAccessToken = new LongLivedAccessToken($amoSettings['token']);
		$this->amo->setAccessToken($longLivedAccessToken)
			->setAccountBaseDomain($amoSettings['baseDomain']);
	}

	private function checkLogsDirExist(): void
	{
		$dir = __DIR__ . '/../../logs'; // Путь к целевой папке

		if (!is_dir($dir)) {
			// Создаем папку
			if (!mkdir($dir, 0775, true)) {
				die('Не удалось создать папку...');
			}

			// Устанавливаем права доступа
			if (!chmod($dir, 0775)) {
				die('Не удалось установить права доступа для папки...');
			}
		}
	}

	protected function createLogger(): LoggerInterface
	{
		$logger = new Logger('app');
		$this->checkLogsDirExist();
		$logFile = __DIR__ . '/../../logs/app.log';
		$logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));
		return $logger;
	}

	/**
	 * Иногда фреймвворк slim падает страхованный вариант для get запросов
	 * @param  string  $name
	 * @return mixed
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function __get(string $name)
	{
		if ($this->container->has($name)) {
			return $this->container->get($name);
		}

		throw new \RuntimeException("Dependency '$name' is not registered in the container.");
	}

	/**
	 * Формирования лога для info
	 * @param  Request  $request
	 * @return void
	 */
	protected function logRequest(Request $request): void
	{
		$uri = $request->getUri();
		$this->logger->info('Request', [
			'method' => $request->getMethod(),
			'uri' => (string) $uri,
			'body' => $request->getBody()->getContents(),
			'headers' => $request->getHeaders()
		]);
	}

	/**
	 * Формирования лога для error
	 * @param $message
	 * @return void
	 */
	protected function logRequestError($message): void
	{
		$this->logger->error('Request Error', [
			'message' => $message
		]);
	}

	protected function logInfo(string $message, array $context = []): void
	{
		$this->logger->info($message, $context);
	}

	protected function logError(string $message, array $context = []): void
	{
		$this->logger->error($message, $context);
	}
}
