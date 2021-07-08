<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-04-07 13:22:40
 *
 */
namespace Kovey\App\Bootstrap;

use Kovey\Library\Config\Manager;
use Kovey\App\App;
use Kovey\Container\Event;
use Kovey\Connection\Pool;
use Kovey\Container\Keyword\EventName;
use Kovey\Sharding\Mysql;
use Kovey\Sharding\Redis;
use Kovey\Sharding\Sharding\GlobalIdentify;

class ContainerEventInit
{
    /**
     * @description init event
     *
     * @param Application $app
     *
     * @return void
     */
    public function __initRedis(App $app) : void
    {
        if (Manager::get('server.pool.redis') !== 'On') {
            return;
        }
        $app->getContainer()
            ->on(EventName::EVENT_REDIS, function (Event\Redis $event) use ($app) {
                return new Pool($app->getPool($event->getPoolName()));
            })
            ->on(EventName::EVENT_SHARDING_REDIS, function (Event\ShardingRedis $event) use ($app) {
                return new Redis(Manager::get('redis.sharding.redis_count'), function ($shardingKey) use ($app, $event) {
                    new Pool($app->getPool($event->getPoolName(), $shardingKey));
                });
            });
    }

    public function __initDatabase(App $app) : void
    {
        if (Manager::get('server.pool.db') !== 'On') {
            return;
        }

        $app->on(EventName::EVENT_DATABASE, function (Event\Database $event) use ($app) {
                return new Pool($app->getPool($event->getPoolName()));
            })
            ->on(EventName::EVENT_SHARDING_DATABASE, function (Event\ShardingDatabase $event) use ($app) {
                return new Mysql(Manager::get('db.sharding.db_count'), function ($shardingKey) use ($app, $event) {
                    new Pool($app->getPool($event->getPoolName(), $shardingKey));
                });
            });
    }

    public function __initGlobal(App $app) : void 
    {
        $app->on(EventName::EVENT_GLOBAL_ID, function (Event\GlobalId $event) use ($app) {
            $pool = new Pool($app->getPool($event->getRedisPoolName())); 
            try {
                $gl = new GlobalIdentify($pool->getConnection(), $event->getGlobalKey());
                return $gl->getGlobalIdentify();
            } finally {
                $pool->collect();
            }
        });
    }
}
