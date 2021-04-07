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
    public function __initEvents(App $app) : void
    {
            $app->getContainer()
                ->on('Redis', function (Event\Redis $event) use ($app) {
                    return new Pool($app->getPool($event->getPoolName()));
                })
                ->on('Database', function (Event\Database $event) use ($app) {
                    return new Pool($app->getPool($event->getPoolName()));
                })
                ->on('ShardingDatabase', function (Event\ShardingDatabase $event) use ($app) {
                    return new Mysql(Manager::get('db.sharding.db_count'), function ($shardingKey) use ($app, $event) {
                        new Pool($app->getPool($event->getPoolName(), $shardingKey));
                    });
                })
                ->on('ShardingRedis', function (Event\ShardingRedis $event) use ($app) {
                    return new Redis(Manager::get('redis.sharding.redis_count'), function ($shardingKey) use ($app, $event) {
                        new Pool($app->getPool($event->getPoolName(), $shardingKey));
                    });
                })
                ->on('GlobalId', function (Event\GlobalId $event) use ($app) {
                    $gl = new GlobalIdentify((new Pool($app->getPool($event->getRedisPoolName())))->getConnection(), (new Pool($app->getPool($event->getDbPoolName())))->getConnection());
                    $gl->setTableInfo($event->getTableName(), $event->getFieldName(), $event->getPrimaryName());
                    return $gl->getGlobalIdentify();
                });
    }
}