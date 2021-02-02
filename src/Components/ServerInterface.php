<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-02-02 12:05:25
 *
 */
namespace Kovey\App\Components;

interface ServerInterface
{
    public function start() : void;

    public function on(string $event, callable | Array $callback) : ServerInterface;

    public function getServ() : \Swoole\Server;
}
