<?php
namespace App\Controllers\Webhook;
use Exception;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Models\NoteType\CommonNote;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

use App\Controllers\BaseController;


class WebhookController extends BaseController
{
	private AmoCRMApiClient $apiClient;

	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);
	}

	/**
	 * Метод для распределения входящих хуков
	 * @throws Exception
	 */
	public function handleWebhook(Request $request, Response $response, $args)
	{
		$this->logRequest($request);

		$payload = $request->getParsedBody();
		if ($payload) {
			$this->logRequestError($payload);
			if (isset($payload['leads']['update'])) {
				$this->handleUpdateEvent($payload['leads']['update'][0], 'lead');
			}
			if (isset($payload['contacts']['update'])) {
				$this->handleUpdateEvent($payload['contacts']['update'][0], 'contact');
			}
			if (isset($payload['leads']['add'])) {
				$this->handleCreateEvent($payload['leads']['add'][0], 'lead');
			}
			if (isset($payload['contacts']['add'])) {
				$this->handleCreateEvent($payload['contacts']['add'][0], 'contact');
			}
		}
		return $response->withJson(['success' => true]);
	}

	/**
	 * Создаем сообщения для ответа по событию create
	 * @throws Exception
	 */
	private function handleCreateEvent($payload, $event)
	{
		$text = sprintf(
			"New %s created: %s\nResponsible user: %s\nCreated at: %s",
			$event,
			$payload['name'],
			$payload['responsible_user_id'],
			date('Y-m-d H:i:s')
		);

		if ($event == 'contact') {
			$payload['id'] = array_keys($payload['linked_leads_id'])[0];
		}
		$this->addNoteToEntity($payload['id'], $text);
	}

	/**
	 * Создаем сообщения для ответа по событию update
	 * @throws Exception
	 */
	private function handleUpdateEvent($payload, $event)
	{
		$text = "Updated at: " . $event . ", timestamp: " . date('Y-m-d H:i:s');

		if ($event == 'contact') {
			$payload['id'] = array_keys($payload['linked_leads_id'])[0];
		}
		$this->addNoteToEntity($payload['id'], $text);
	}

	/**
	 * Добавляем примечание
	 * @param $entityId
	 * @param $text
	 * @return void
	 */
	private function addNoteToEntity($entityId, $text)
	{

		try {
			$lead = $this->amo->leads()->getOne($entityId);

			// Проверяем, найдена ли сделка
			if (!$lead) {
				$this->logRequestError($entityId);
				return;
			}

			// Создаем новое примечание и добавляем его в коллекцию
			$notesCollection = (new NotesCollection())->add((new CommonNote())
				->setEntityId($entityId)
				->setText($text));
			$this->amo->notes('leads')->add($notesCollection);
			return;
		} catch (Exception $e) {
			$this->logRequestError($e->getCode()." / ".$e->getMessage());
			return;
		}
	}
}
