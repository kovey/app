<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-04-07 13:17:19
 *
 */
namespace Kovey\App\Bootstrap;

use Kovey\Library\Config\Manager;
use Kovey\App\App;
use Kovey\Logger\Logger;
use Kovey\Logger\Db;
use Kovey\Logger\Monitor;
use Kovey\Logger\Redis;
use Kovey\Process\UserProcess;
use Kovey\Container\Container;

class BaseInit
{
    /**
     * @description init logger
     *
     * @param Application $app
     *
     * @return void
     */
    public function __initLogger(App $app) : void
    {
        ko_change_process_name(Manager::get('server.server.name') . ' root');
        Logger::setLogPath(Manager::get('server.server.logger_dir'));
        Logger::setCategory(Manager::get('server.server.name'));
        Monitor::setLogDir(Manager::get('server.server.logger_dir'));
        Db::setLogDir(Manager::get('server.server.logger_dir'));
        Redis::setLogDir(Manager::get('server.server.logger_dir'));
    }

    /**
     * @description init $app
     *
     * @param Application $app
     *
     * @return void
     */
    public function __initApp(App $app) : void
    {
        $app->registerContainer(new Container())
            ->registerUserProcess(new UserProcess(Manager::get('server.server.worker_num')));
    }
}
