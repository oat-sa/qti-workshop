<?php declare(strict_types=1);


namespace App\Service\ItemSessionManager;


use App\Service\ItemRepository\ItemRepository;
use App\Service\ItemSessionRepository\ItemSessionRepository;
use qtism\common\datatypes\files\FileSystemFileManager;
use qtism\data\IAssessmentItem;
use qtism\runtime\pci\json\Marshaller;
use qtism\runtime\pci\json\Unmarshaller;
use qtism\runtime\tests\AssessmentItemSession;
use Exception;

class ItemSessionManager
{
    private $itemRepository;

    private $itemSessionRepository;

    public function __construct(ItemRepository $itemRepository, ItemSessionRepository $itemSessionRepository) {

        $this->itemRepository = $itemRepository;
        $this->itemSessionRepository = $itemSessionRepository;
    }

    /**
     * @param string $itemId
     * @return array
     * @throws ItemSessionManagerException
     */
    public function init(string $itemId)
    {
        try {
            /** @var IAssessmentItem $item */
            $item = $this->itemRepository->get($itemId)->getDocumentComponent();
            $itemSession = new AssessmentItemSession($item);
            $itemSession->getItemSessionControl()->setMaxAttempts(0);
            $itemSession->beginItemSession();

            $marshaller = new Marshaller();
            $data = $marshaller->marshall($itemSession, Marshaller::MARSHALL_ARRAY);
            $sessionId = uniqid();
            $time = new \DateTime();

            $this->itemSessionRepository->store(
                $sessionId,
                [
                    'itemId' => $itemId,
                    'state' => $itemSession->getState(),
                    'session' => $data,
                    'time' => $time->format('Y-m-d H:i:s')
                ]
            );

            return [
                'sessionId' => $sessionId,
                'sessionData' => $data
            ];
        } catch (Exception $e) {
            throw new ItemSessionManagerException(
                "An error occurred while initializing an item session for item '${itemId}'.",
                0,
                $e
            );
        }

    }

    /**
     * @param string $sessionId
     * @param array $responses
     * @return array
     * @throws ItemSessionManagerException
     */
    public function attempt(string $sessionId, array $responses)
    {
        try {
            $sessionData = $this->itemSessionRepository->get($sessionId);
            /** @var IAssessmentItem $item */
            $item = $this->itemRepository->get($sessionData['itemId'])->getDocumentComponent();
            $itemSession = new AssessmentItemSession($item);
            $itemSession->setTime(new \DateTime($sessionData['time']));
            $itemSession->getItemSessionControl()->setMaxAttempts(0);

            $itemSession->setState($sessionData['state']);
            $marshaller = new Marshaller();
            $unmarshaller = new Unmarshaller(new FileSystemFileManager());

            $variables = $unmarshaller->unmarshall($sessionData['session']);

            foreach ($variables as $variableIdentifier => $variable) {
                $itemSession[$variableIdentifier] = $variable;
            }

            $itemSession->beginAttempt();


            $responses = $unmarshaller->unmarshall($responses);
            foreach ($responses as $responseIdentifier => $response) {
                $itemSession[$responseIdentifier] = $response;
            }

            $newTime = new \DateTime();
            $itemSession->setTime($newTime);
            $itemSession->endAttempt();
            $newSessionData = $marshaller->marshall($itemSession, Marshaller::MARSHALL_ARRAY);

            $this->itemSessionRepository->store(
                $sessionId,
                [
                    'itemId' => $sessionData['itemId'],
                    'state' => $itemSession->getState(),
                    'session' => $newSessionData,
                    'time' => $newTime->format('Y-m-d H:i:s')
                ]
            );

            return [
                'sessionId' => $sessionId,
                'sessionData' => $newSessionData
            ];

        } catch (Exception $e) {
            throw new ItemSessionManagerException(
                "An error occurred while initializing an item session for item '${itemId}'.",
                0,
                $e
            );
        }
    }
}