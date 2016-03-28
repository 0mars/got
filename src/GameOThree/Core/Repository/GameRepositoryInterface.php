<?php
namespace GameOThree\Core\Repository;

use GameOThree\Core\Model\Game;

/**
 * Interface GameRepositoryInterface
 * @package GameOThree\Core\Repository
 */
interface GameRepositoryInterface
{
    /**
     * @return Game
     */
    public function findOpenGame();

    /**
     * @param string $id
     * @return Game
     */
    public function findById($id);

    /**
     * @param Game $game
     * @return void
     */
    public function save(Game $game);

}