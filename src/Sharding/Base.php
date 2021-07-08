<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-07-03 15:50:33
 *
 */
namespace Kovey\App\Sharding;

use Kovey\Container\Module\HasDbInterface;
use Kovey\Sharding\DbInterface;
use Kovey\Sharding\Model\Base as MB;

abstract class Base extends MB implements HasDbInterface
{
    protected DbInterface $database;

    public function setDatabase(mixed $database) : Base
    {
        $this->database = $database;
        return $this;
    }
}
