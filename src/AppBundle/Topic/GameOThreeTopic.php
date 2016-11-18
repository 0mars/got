<?php
namespace AppBundle\Topic;

use GameOThree\Core\Service\GameManager;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;

/**
 * @package AppBundle\Topic
 * @author Omar Shaban <omars@php.net>
 */
class GameOThreeTopic implements TopicInterface
{
    /**
     * @var GameManager
     */
    private $gameManager;

    /**
     * @param GameManager $gameManager
     */
    public function __construct(GameManager $gameManager)
    {
        $this->gameManager = $gameManager;
    }

    /**
     * @param string $msg
     */
    private function log($msg)
    {
        echo date('Y-m-d H:i:s') . ': ' . $msg . PHP_EOL;
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $this->log("Subscribe called");
        $clientsConnected = count($topic);
        if ($clientsConnected > 2) {
            $connection->close();
        } elseif ($clientsConnected == 2) {
            $game = $this->gameManager->start($this->getGameId($topic));
            $readyEvent = [
                'event' => [
                    "name" => "game_started",
                    "value" => $game->getStartValue()
                ]
            ];
            $topic->broadcast($readyEvent);
            $topic->broadcast(['msg' => "game in progress"]);
            $topic->broadcast(
                ['event' => ['name' => 'ack_start']],
                [],
                [$connection->WAMP->sessionId]
            );
        } else {
            $topic->broadcast(
                ['event' => ['name' => 'notification', 'message' => 'Waiting for the other player...']],
                [$connection->WAMP->sessionId]
            );
        }
        $topic->broadcast(['msg' => "total players on this topic: " . count($topic)]);
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $this->gameManager->disconnect($this->getGameId($topic));
        $this->notify($topic, 'Game concluded: the other player had left.');
        foreach ($topic as $client) {
            $client->close();
        }
    }

    /**
     * @param Topic $topic
     * @param string $message
     */
    public function notify(Topic $topic, $message)
    {
        $topic->broadcast(
            [
                'event' => [
                    'name' => 'notification',
                    'message' => $message
                ]
            ]
        );
    }

    /**
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @param array $event
     * @param array $exclude
     * @param array $eligible
     */
    public function onPublish(
        ConnectionInterface $connection,
        Topic $topic,
        WampRequest $request,
        array $event,
        array $exclude,
        array $eligible
    ) {
        $topic->broadcast("S - rec msg from :" . $connection->WAMP->sessionId);
        if (isset($event['event']['name']) && $event['event']['name'] == 'ack') {
            sleep(mt_rand(1, 5));
            list($input, $value) = $this->gameManager->processTurn($this->getGameId($topic), $connection->WAMP->sessionId);
            $player = $this->gameManager->getGame($this->getGameId($topic))->getPlayerNumber($connection->WAMP->sessionId);
            $topic->broadcast(
                [
                    'event' => [
                        'name' => 'display_turn_result',
                        'input' => $input === 1 ? "+1":"$input",
                        'value' => $value,
                        'player' => $player
                    ]
                ]
            );
            if ($value == 1) {
                $topic->broadcast(
                    [
                        'event' => [
                            'name' => 'winner',
                            'input' => $input,
                            'value' => $value,
                            'player' => $player
                        ]
                    ]
                );
            } else {
                $topic->broadcast(
                    [
                        'event' => [
                            'name' => 'ack_continue',
                            'input' => $input,
                            'value' => $value
                        ]
                    ], [$connection->WAMP->sessionId]
                );
            }
        }
    }

    /**
     * Like RPC is will use to prefix the channel
     * @return string
     */
    public function getName()
    {
        return 'game_o_three.topic';
    }

    /**
     * Returns the game ID from topic
     * @param Topic $topic
     * @return string
     */
    private function getGameId(Topic $topic)
    {
        return str_replace('game/', '', $topic->getId());
    }
}