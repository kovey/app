<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-04-07 13:29:05
 *
 */
namespace Kovey\Web\App\Bootstrap;

use Kovey\Library\Config\Manager;
use Kovey\App\App;
use Kovey\Process\Process;

class ProcessInit
{
    /**
     * @description init process
     *
     * @param Application $app
     *
     * @return void
     */
    public function __initProcess(App $app) : void
    {
        $app->registerProcess('kovey_config', (new Process\Config())->setProcessName(Manager::get('server.server.name') . ' config'));
        if (Manager::get('server.monitor.open') !== 'On') {
            return;
        }

        $app->registerProcess('kovey_monitor', new Process\Monitor());
    }
}
