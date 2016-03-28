<?php
namespace GameOThree\Core\Model;

use GameOThree\Core\Exception\IllegalOperationException;

/**
 * Class Game
 * @package GameOThree\Core\Model
 * @author Omar Shaban <omars@php.net>
 */
class Game implements GameInterface
{
    const STATUS_OPEN        = 1;
    const STATUS_READY       = 2;
    const STATUS_IN_PROGRESS = 3;
    const STATUS_CONCLUDED   = 4;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $player1;

    /**
     * @var string
     */
    private $player2;

    /**
     * @var int
     */
    private $startValue;

    /**
     * @var int
     */
    private $currentValue;

    /**
     * @var int
     */
    private $turn = 1;

    /**
     * @var int
     */
    private $result;

    /**
     * @var int
     */
    private $status = self::STATUS_OPEN;

    /**
     * @var int
     */
    private $winner;

    /**
     * @var \DateTime
     */
    private $startedAt;

    /**
     * @var \DateTme
     */
    private $lastUpdate;

    /**
     * @var int
     */
    private $lastInput;

    /**
     * Game constructor.
     * @param $player1
     */
    public function __construct($player1)
    {
        $this->player1 = $player1;
        $this->touch();
    }

    /**
     * @return void
     */
    private function touch()
    {
        $this->lastUpdate = new \DateTime();
    }

    /**
     * @param string $playerId
     * @return Game
     */
    public function join($playerId)
    {
        $this->player2 = $playerId;
        $this->status = self::STATUS_READY;
        $this->touch();
        return $this;
    }

    /**
     * @return GameInterface
     * @throws IllegalOperationException
     */
    public function start()
    {
        if ($this->getStatus() !== self::STATUS_READY) {
            throw new IllegalOperationException("Cannot start the game without it being ready");
        }
        $this->status = self::STATUS_IN_PROGRESS;
        $this->startValue = $this->currentValue = mt_rand(9, 200);
        $this->startedAt = new \DateTime();
        $this->touch();
        return $this;
    }

    /**
     * @return GameInterface
     */
    public function conclude()
    {
        $this->status = self::STATUS_CONCLUDED;
        $this->touch();
        return $this;
    }

    /**
     * @todo removal
     * @return GameInterface
     */
    public function switchTurn()
    {
        $this->turn = $this->turn == 1 ? 2 : 1;
        $this->touch();
        return $this;
    }

    /**
     * @param string $playerId
     * @return int
     * @throws IllegalOperationException
     */
    public function calculateResult($playerId)
    {
        if ($this->getStatus() != self::STATUS_IN_PROGRESS) {
            throw new IllegalOperationException('Invalid Game State: cannot calculate result');
        }
        $this->touch();
        $remainder = $this->currentValue % 3;
        if ($remainder === 1) {
            $this->lastInput = -1;
            $this->currentValue--;
        } elseif ($remainder === 2) {
            $this->lastInput = 1;
            $this->currentValue++;
        } else {
            $this->lastInput = 0;
        }
        $this->currentValue = $this->currentValue/3;

        if ($this->currentValue == 1) {
            $this->winner = $playerId == $this->getPlayer1()?1:2;
            $this->status = self::STATUS_CONCLUDED;
        }

        return $this->currentValue;
    }

    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * @return int
     */
    public function getLastInput()
    {
        return $this->lastInput;
    }

    /**
     * @return int
     */
    public function getCurrentValue()
    {
        return $this->currentValue;
    }

    /**
     * @return string
     */
    public function getPlayer2()
    {
        return $this->player2;
    }

    /**
     * @return string
     */
    public function getPlayer1()
    {
        return $this->player1;
    }

    /**
     * @return int
     */
    public function getTurn()
    {
        return $this->turn;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getStartValue()
    {
        return $this->startValue;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @param string $playerId
     * @return int
     */
    public function getPlayerNumber($playerId)
    {
        return $this->getPlayer1() == $playerId ? 1 : 2;
    }
}
