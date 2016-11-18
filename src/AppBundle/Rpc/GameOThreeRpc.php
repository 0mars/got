<?php

namespace AppBundle\Rpc;

use GameOThree\Core\Service\GameManager;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\RPC\RpcInterface;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;

/**
 * Class GameOThreeRpc
 * @package AppBundle\Rpc
 * @author Omar Shaban <omars@php.net>
 */
class GameOThreeRpc implements RpcInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var GameManager
     */
    protected $gameManager;

    /**
     * @param LoggerInterface $logger
     * @param GameManager $gameManager
     */
    public function __construct(LoggerInterface $logger, GameManager $gameManager)
    {
        $this->logger = $logger;
        $this->gameManager = $gameManager;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'game_o_three.rpc';
    }

    /**
     * @param ConnectionInterface $connection
     * @param WampRequest $request
     * @param $params
     * @return array
     */
    public function play(ConnectionInterface $connection, WampRequest $request, $params)
    {
        $game = $this->gameManager->joinGame($connection->WAMP->sessionId);
        return array("game_id" => $game->getId(), 'status'=> $game->getStatus());
    }
}
