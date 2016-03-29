<?php
/**
 * Created at 23/03/16 18:05
 */
namespace GameOThree\Core\Service;

use GameOThree\Core\Model\Game;
use GameOThree\Core\Model\Player;
use GameOThree\Core\Repository\GameRepositoryInterface;

/**
 * Class GameManager
 * @package GameOThree\Core\Service
 * @author Omar Shaban <omars@php.net>
 */
class GameManager
{
    /**
     * @var GameRepositoryInterface
     */
    private $gameRepository;

    /**
     * GameManager constructor.
     * @param GameRepositoryInterface $gameRepository
     */
    public function __construct(GameRepositoryInterface $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * Search for an open game, if found join, if not create
     * @param $playerId
     * @param bool $human
     * @return Game
     */
    public function joinGame($playerId, $human = false) {
        if ($human) {
            $game = $this->gameRepository->findUncontrolledOpenGame();
        } else {
            $game = $this->gameRepository->findOpenGame();
        }

        $player = new Player($playerId, $human);

        if (!$game) {
            $game = $this->createGame($player);
        } else {
            $game->join($player);
            $this->gameRepository->save($game);
        }
        return $game;
    }

    /**
     * @param string $gameId
     * @return Game
     * @throws \GameOThree\Core\Exception\IllegalOperationException
     */
    public function start($gameId)
    {
        $game = $this->gameRepository->findById($gameId);
        $game->start();
        $this->gameRepository->save($game);
        return $game;
    }

    /**
     * @param string $player
     * @return Game
     */
    private function createGame($player)
    {
        $game = new Game($player);
        $this->gameRepository->save($game);
        return $game;
    }

    /**
     * @param string $gameId
     * @return Game
     */
    public function disconnect($gameId)
    {
        $game = $this->gameRepository->findById($gameId);
        $game->conclude();
        $this->gameRepository->save($game);
        return $game;
    }

    /**
     * @param string $gameId
     * @param string $playerId
     * @return array
     */
    public function processTurn($gameId, $playerId)
    {
        $game = $this->gameRepository->findById($gameId);
        $currentValue = $game->calculateResult($playerId);
        return [
            $game->getLastInput(),
            $currentValue
        ];
    }

    /**
     * @param $gameId
     * @param $playerId
     * @param $answer
     * @return array
     * @throws \GameOThree\Core\Exception\IllegalOperationException
     * @throws \GameOThree\Core\Exception\IncorrectAnswerException
     */
    public function submitAnswer($gameId, $playerId, $answer)
    {
        $game = $this->gameRepository->findById($gameId);
        $currentValue = $game->submitAnswer($playerId, $answer);
        return [
            $game->getLastInput(),
            $currentValue
        ];
    }

    public function getGame($gameId)
    {
        return $this->gameRepository->findById($gameId);
    }
}
