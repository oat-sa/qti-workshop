<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\ItemSessionManager\ItemSessionManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemSessionController
{
    /**
     * @var ItemSessionManager
     */
    private $itemSessionManager;

    /**
     * ItemSessionController constructor.
     * @param ItemSessionManager $itemSessionManager
     */
    public function __construct(ItemSessionManager $itemSessionManager)
    {
        $this->itemSessionManager = $itemSessionManager;
    }

    /**
     * @Route("/item/session/init/{itemId}")
     * @param string $itemId
     * @return Response
     */
    public function init(string $itemId)
    {
        $clientData = $this->itemSessionManager->init($itemId);

        return new JsonResponse($clientData);
    }

    /**
     * @Route("/item/session/attempt/{sessionId}")
     * @param string $sessionId
     * @param Request $request
     * @return Response
     *
     */
    public function attempt(string $sessionId, Request $request)
    {
        $content = $request->getContent();
        $responses = json_decode($content, true);

        $clientData = $this->itemSessionManager->attempt($sessionId, $responses);

        return new JsonResponse($clientData);
    }
}