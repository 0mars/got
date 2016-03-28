<?php
/**
 * Created at 28/03/16 23:46
 */
namespace GameOThree\Core\Tests\Service;

use GameOThree\Core\Model\Game;
use GameOThree\Core\Repository\GameRepositoryInterface;
use GameOThree\Core\Service\GameManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use \Mockery as m;

/**
 * Class GameManagerTest
 * @package GameOThree\Core\Tests\Service
 * @author Omar Shaban <omars@php.net>
 */
class GameManagerTest extends KernelTestCase
{
    /**
     * @test
     */
    public function orchestrate()
    {
        $game = new Game('p1');
        $repository = m::mock(GameRepositoryInterface::class)
            ->shouldReceive([
                                'findOpenGame'=> $game,
                                'findById' => $game,
                                'save'=> null
                            ])->withAnyArgs()
            ->getMock();
        ;

        $gameManager = new GameManager($repository);
        $this->assertEquals(Game::STATUS_OPEN, $game->getStatus());
        $gameManager->joinGame('p2');
        $this->assertEquals(Game::STATUS_READY, $game->getStatus());
        $game = $gameManager->start('anyIdWouldDo');
        $this->assertEquals(Game::STATUS_IN_PROGRESS, $game->getStatus());
        $this->assertInstanceOf(Game::class, $game);
        list($input, $result) = $gameManager->processTurn('anyIdWouldDo','p2');
        $this->assertEquals($input, $game->getLastInput());
        $this->assertEquals($result, $game->getCurrentValue());
    }


    /**
     * @test
     */
    public function disconnect()
    {
        $game = new Game('p1');
        $repository = m::mock(GameRepositoryInterface::class)
                       ->shouldReceive([
                                           'findOpenGame'=> null,
                                           'findById' => $game,
                                           'save'=> null
                                       ])
                       ->getMock();
        ;

        $gameManager = new GameManager($repository);
        $gameManager->joinGame('p2');
        $gameManager->disconnect('id');
        $this->assertEquals(Game::STATUS_CONCLUDED, $game->getStatus());
    }
}
