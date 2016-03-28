<?php
/**
 * Created at 28/03/16 23:49
 */
namespace AppBundle\Tests;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class KernelTestCaseTrait
 * @package AppBundle\Tests
 * @author Omar Shaban <omars@php.net>
 */
trait KernelTestCaseTrait
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    protected function bootKernel()
    {
        $this->container = new Container();
        $this->kernel = new \Kernel($this->container);
        $this->kernel->boot();
    }
}
