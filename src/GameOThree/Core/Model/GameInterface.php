<?php
namespace GameOThree\Core\Model;

/**
 * Interface GameInterface
 * @package GameOThree\Core\Model
 */
interface GameInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $playerId
     * @return GameInterface
     */
    public function join($playerId);

    /**
     * @return GameInterface
     */
    public function start();

    /**
     * @return GameInterface
     */
    public function conclude();

    /**
     * @param string $playerId
     * @return GameInterface
     */
    public function calculateResult($playerId);

    /**
     * @return integer
     */
    public function getLastInput();
}