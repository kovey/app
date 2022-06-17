<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2022-06-17 12:09:42
 *
 */
namespace Kovey\App\Components;

use Swoole\Table;

class Locker
{
    private Table $locker;

    const MAX_LOCKER_COUNT = 8192;

    const KEY_FORMAT = '%s_%s';

    public function __construct()
    {
        $this->table = new Table(self::MAX_LOCKER_COUNT);
        $this->table->column('t', Table::TYPE_INT, 8);
        $this->table->create();
    }

    public function getKey(string | int $prefix, string | int $key) : string
    {
        return sprintf(self::KEY_FORMAT, $prefix, $key);
    }

    public function lock(string | int $prefix, string | int $key, int $expire = 60) : bool
    {
        $key = $this->getKey($prefix, $key);
        $now = time();
        if ($this->table->exist($key)) {
            $old = $this->table->get($key, 't');
            if (!empty($old) && $now <= $old) {
                return false;
            }
        }

        return $this->table->set($key, array('t' => $now + $expire));
    }

    public function unlock(string | int $prefix, string | int $key) : bool
    {
        return $this->table->del($this->getKey($prefix, $key));
    }
}
