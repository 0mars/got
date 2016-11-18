<?php
namespace AppBundle\Topic;

use GameOThree\Core\Exception\IncorrectAnswerException;
use GameOThree\Core\Service\GameManager;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;

/**
 * Class GameOThreeTopic
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
     * GameOThreeTopic constructor.
     * @param GameManager $gameManager
     */
    public function __construct(GameManager $gameManager)
    {
        $this->gameManager = $gameManager;
    }

    /**
     * @param string $message
     */
    private function log($message)
    {
        echo date('Y-m-d H:i:s') . ': ' . $message . PHP_EOL;
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
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
                ['event' => ['name' => $game->getController() == $game->getOtherPlayerById($connection->WAMP->sessionId)?'answer_needed':'ack_start']],
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
     * @return void
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
     * @param $event
     * @param array $exclude
     * @param array $eligible
     */
    public function onPublish(
        ConnectionInterface $connection,
        Topic $topic,
        WampRequest $request,
        $event,
        array $exclude,
        array $eligible
    ) {
        $topic->broadcast("S - rec msg from :" . $connection->WAMP->sessionId);
        $playerId = $connection->WAMP->sessionId;
        if (isset($event['event']['name']) && $event['event']['name'] == 'ack') {
            sleep(mt_rand(1, 2));

            $game = $this->gameManager->getGame($this->getGameId($topic));
            $player = $game->getPlayerById($playerId);
            $otherPlayer = $game->getOtherPlayerById($playerId);

            if ($player->isHuman() && $player == $game->getController() && isset($event['event']['answer'])) {
                try {
                    list($input, $value) = $this->gameManager->submitAnswer($this->getGameId($topic), $playerId, (int)$event['event']['answer']);
                } catch (IncorrectAnswerException $e) {
                    var_dump($event);
                    $topic->broadcast(
                        [
                            'event' => [
                                'name' => 'answer_needed',
                            ]
                        ], [] ,[$connection->WAMP->sessionId]
                    );
                    return;
                }

            } else {
                list($input, $value) = $this->gameManager->processTurn($this->getGameId($topic), $playerId);
            }


            $game = $this->gameManager->getGame($this->getGameId($topic));
            $player = $game->getPlayerNumber($connection->WAMP->sessionId);
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
                            'name' => !$otherPlayer->isHuman() ? 'ack_continue' :'answer_needed',
                            'input' => $input,
                            'value' => $value
                        ]
                    ], [],[$otherPlayer->getId()]
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