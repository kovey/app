<?php
/**
 * @description database base
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-07-03 15:50:33
 *
 */
namespace Kovey\App\Model;

use Kovey\Container\Module\HasDbInterface;
use Kovey\Connection\Pool;
use Kovey\Db\Model\Base as MB;

abstract class Base extends MB implements HasDbInterface
{
    public function setDatabase(mixed $database) : Base
    {
        $this->database = $database;
        return $this;
    }
}
