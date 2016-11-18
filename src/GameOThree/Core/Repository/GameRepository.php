<?php
/**
 * Created at 27/03/16 12:48
 */
namespace GameOThree\Core\Repository;

use Doctrine\Common\Persistence\ObjectManager;
use GameOThree\Core\Model\Game;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * Class GameRepository
 * @package GameOThree\Core\Repository
 * @author Omar Shaban <omars@php.net>
 */
class GameRepository implements GameRepositoryInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * GameRepository constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritDoc}
     */
    public function findOpenGame()
    {
        $res = $this->objectManager->getRepository(Game::class)->findOneBy(['status' => Game::STATUS_OPEN]);
        return $res;
    }

    /**
     * {@inheritDoc}
     */
    public function findUncontrolledOpenGame()
    {
        $res = $this->objectManager->getRepository(Game::class)->findOneBy(
            ['status' => Game::STATUS_OPEN, 'controller'=> null]
        );
        return $res;
    }

    /**
     * {@inheritDoc}
     */
    public function findById($id)
    {
        return $this->objectManager->getRepository(Game::class)->findOneById($id);
    }

    /**
     * {@inheritDoc}
     */
    public function save(Game $game)
    {
        $this->objectManager->persist($game);
        $this->objectManager->flush();
    }
}
