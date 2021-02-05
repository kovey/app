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

interface BusinessInterface
{
    public function begin() : BusinessInterface;

    public function run() : BusinessInterface;

    public function end() : BusinessInterface;

    public function monitor(ServerAbstract $server) : BusinessInterface;
}
