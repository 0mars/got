<?php
namespace GameOThree\Core\Tests\Model;

use GameOThree\Core\Model\Game;

/**
 * Class GameTest
 * @package GameOThree\Core\Tests\Model
 * @author Omar Shaban <omars@php.net>
 */
class GameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function create()
    {
        $player1 = 'MyPlayerID';
        $game = new Game($player1);
        $this->assertSame($player1, $game->getPlayer1());
        $this->assertSame(Game::STATUS_OPEN,$game->getStatus());
    }

    /**
     * @test
     */
    public function join()
    {
        $game = new Game('p1');
        $game->join('p2');
        $this->assertSame(Game::STATUS_READY, $game->getStatus());
    }

    /**
     * @test
     */
    public function getPlayerNumber()
    {
        $game = new Game('p1');
        $game->join('p2');
        $this->assertEquals(1, $game->getPlayerNumber('p1'));
        $this->assertEquals(2, $game->getPlayerNumber('p2'));
    }

    /**
     * @test
     */
    public function start()
    {
        $game = new Game('p1');
        $player2 = 'p2';
        $game->join($player2);
        $game->start();
        $this->assertEquals($player2, $game->getPlayer2());
        $this->assertSame(Game::STATUS_IN_PROGRESS, $game->getStatus());
        $this->assertGreaterThanOrEqual(9, $game->getStartValue());
        $this->assertInstanceOf(\DateTime::class, $game->getStartedAt());
    }

    /**
     * @test
     * @expectedException \GameOThree\Core\Exception\IllegalOperationException
     */
    public function startException()
    {
        $game = new Game('p1');
        $game->start();
    }

    /**
     * @test
     */
    public function conclude()
    {
        $game = new Game('p1');
        $game->conclude();
        $this->assertEquals(Game::STATUS_CONCLUDED, $game->getStatus());
    }

    /**
     * @test
     * @expectedException GameOThree\Core\Exception\IllegalOperationException
     */
    public function calculateResultException()
    {
        $game = new Game('p1');
        $game->calculateResult('p1');
    }

    /**
     * @test
     */
    public function calculateResult()
    {
        $game = new Game('p1');
        $game->join('p2');
        $game->start();
        $currentValue = $game->getStartValue();
        $game->calculateResult('p1');
        $this->assertEquals(($currentValue+$game->getLastInput())/3, $game->getCurrentValue());
    }

    /**
     * @test
     */
    public function wholeGame()
    {
        $game = new Game('p1');
        $game->join('p2');
        $game->start();

        while ($game->getCurrentValue() !== 1) {
            $game->calculateResult('p1');
        }
        $this->assertNotNull($game->getWinner());
    }

    /**
     * @test
     */
    public function getId()
    {
        $game = new Game('p1');
        $this->assertNull($game->getId());
    }
}
