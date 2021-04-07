<?php
/**
 *
 * @description bootstrap when app start
 *
 * @package     App\Bootstrap
 *
 * @time        Tue Sep 24 09:00:10 2019
 *
 * @author      kovey
 */
namespace Kovey\App\Bootstrap;

use Kovey\Library\Config\Manager;
use Kovey\App\App;
use Kovey\Connection\Pool\Mysql;
use Kovey\Connection\Pool\Redis;

class PoolInit
{
    public function __initRedisPool(App $app) : void
    {
        if (Manager::get('server.pool.redis') !== 'On') {
            return;
        }

        $pool = Manager::get('redis.pool');
        if (!is_array($pool) || empty($pool)) {
            return;
        }

        $this->initPool($pool, Manager::get('redis.write'), Redis::getWriteName(), $app);
        $this->initPool($pool, Manager::get('redis.read'), Redis::getReadName(), $app);
    }

    public function __initMysqlPool(App $app)
    {
        if (Manager::get('server.pool.db') !== 'On') {
            return;
        }

        $pool = Manager::get('db.pool');
        if (!is_array($pool) || empty($pool)) {
            return;
        }

        $this->initPool($pool, Manager::get('db.write'), Mysql::getWriteName(), $app, 0);
        $this->initPool($pool, Manager::get('db.read'), Mysql::getReadName(), $app, 0);
    }

    private function initPool(Array $pool, Array $configs, string $name, App $app, int $type = 1)
    {
        if (empty($configs)) {
            return;
        }

        foreach ($configs as $shardingKey => $config) {
            if (!is_array($config) || empty($config)) {
                continue;
            }

            if ($type == 1) {
                $app->registerPool($name, new Redis($pool, $config), $shardingKey);
                continue;
            }

            $app->registerPool($name, new Mysql($pool, $config), $shardingKey);
        }
    }
}
