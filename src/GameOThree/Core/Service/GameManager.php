<?php
/**
 * Created at 23/03/16 18:05
 */
namespace GameOThree\Core\Service;

use GameOThree\Core\Model\Game;
use GameOThree\Core\Repository\GameRepositoryInterface;
use GameOThree\Core\Exception\IllegalOperationException;

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
     *
     * @param string $playerId
     * @return Game
     */
    public function joinGame($playerId) {
        $game = $this->gameRepository->findOpenGame();

        if (!$game) {
            $game = $this->createGame($playerId);
        } else {
            $game->join($playerId);
            $this->gameRepository->save($game);
        }
        return $game;
    }

    /**
     * @param string $gameId
     * @return Game
     * @throws IllegalOperationException
     */
    public function start($gameId)
    {
        $game = $this->gameRepository->findById($gameId);
        $game->start();
        $this->gameRepository->save($game);
        return $game;
    }

    /**
     * @param string $playerId
     * @return Game
     */
    private function createGame($playerId)
    {
        $game = new Game($playerId);
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
     * @param string $gameId
     * @return Game
     */
    public function getGame($gameId)
    {
        return $this->gameRepository->findById($gameId);
    }
}
