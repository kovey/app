<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-02-05 15:45:54
 *
 */
namespace Kovey\App\Components;

use Kovey\Event\EventManager;

interface BusinessInterface
{
    public function begin() : BusinessInterface;

    public function run(EventManager $eventManager) : BusinessInterface;

    public function end() : BusinessInterface;

    public function monitor(ServerAbstract $server) : BusinessInterface;
}
