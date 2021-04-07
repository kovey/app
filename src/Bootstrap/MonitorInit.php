<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-04-07 13:54:54
 *
 */
namespace Kovey\App\Bootstrap;

use Kovey\Library\Config\Manager;
use Kovey\App\App;
use Kovey\Rpc\Client\Monitor\Monitor;
use Kovey\Logger\Logger;
use Kovey\App\Event;

class MonitorInit
{
    public function __initEvents(App $app)
    {
        if (Manager::get('server.monitor.open') !== 'On') {
            return;
        }

        $app->on('monitor', function (Event\Monitor $event) {
                $monitor = new Monitor(Manager::get('rpc.monitor'), Manager::get('server.server.project'), Manager::get('server.server.name'));
                if (!$monitor->sendToMonitor($event->getData())) {
                    Logger::writeWarningLog(__LINE__, __FILE__, $monitor->getError());
                }
            });
    }
}
