<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-02-01 14:29:01
 *
 */
namespace Kovey\App\Components;

use Kovey\Container\ContainerInterface;
use Kovey\Event\EventInterface;
use Kovey\Event\EventManager;

interface WorkInterface
{
    public function setContainer(ContainerInterface $container) : WorkInterface;

    public function run(EventInterface $event) : Array;

    public function setEventManager(EventManager $eventManager) : WorkInterface;

    public function setLocker(Locker $locker) : WorkInterface;
}
