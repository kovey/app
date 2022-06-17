<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-02-02 11:52:14
 *
 */
namespace Kovey\App\Components;

use Kovey\Container\ContainerInterface;
use Kovey\Event\EventInterface;
use Kovey\Event\EventManager;

abstract class Work implements WorkInterface
{
    protected EventManager $event;

    protected ContainerInterface $container;

    protected Locker $locker;

    public function setContainer(ContainerInterface $container) : WorkInterface
    {
        $this->container = $container;
        return $this;
    }

    public function setEventManager(EventManager $eventManager) : WorkInterface
    {
        $this->event = $eventManager;
        return $this;
    }

    public function setLocker(Locker $locker) : WorkInterface
    {
        $this->locker = $locker;
        return $this;
    }
}
