<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-02-02 12:15:35
 *
 */
namespace Kovey\App\Components;

use Kovey\Event\EventManager;
use Swoole\Server;
use Kovey\Logger\Logger;
use Swoole\Server\PipeMessage;
use Kovey\App\Event;

abstract class ServerAbstract implements ServerInterface
{
    protected EventManager $event;

    protected Array $config;

    protected Server $serv;

    protected bool $isRunDocker;

    final public function __construct(Array $config)
    {
        $this->config = $config;
        $this->isRunDocker = ($this->config['run_docker'] ?? 'Off') === 'On';

        $this->event = new EventManager(array(
            'monitor' => Event\Monitor::class,
            'console' => Event\Console::class,
            'initPool' => Event\InitPool::class
        ));

        $this->initLog();

        $this->initServer();

        $this->serv->on('workerStart', array($this, 'workerStart'));
        $this->serv->on('managerStart', array($this, 'managerStart'));
        $this->serv->on('pipeMessage', array($this, 'pipeMessage'));
        $this->serv->on('workerError', array($this, 'workerError'));
    }

    private function initLog()
    {
        $logDir = dirname($this->config['pid_file']);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        if (!is_dir($this->config['logger_dir'] . '/server')) {
            mkdir($this->config['logger_dir'] . '/server');
        }
    }

    public function on(string $type, callable | Array $callback) : ServerInterface
    {
        $this->event->addEvent($type, $callback);
        return $this;
    }

    public function getServ() : Server
    {
        return $this->serv;
    }

    public function start() : void
    {
        $this->serv->start();
    }

    public function monitor(Array $data, string $traceId) : void
    {
        if (isset($this->config['monitor_open']) && $this->config['monitor_open'] === 'Off') {
            return;
        }

        try {
            $this->event->dispatch(new Event\Monitor($data));
        } catch (\Throwable $e) {
            Logger::writeExceptionLog(__LINE__, __FILE__, $e, $traceId);
        }
    }

    protected function initPool() : void
    {
        try {
            $this->event->dispatch(new Event\InitPool($this));
        } catch (\Throwable $e) {
            Logger::writeExceptionLog(__LINE__, __FILE__, $e);
        }
    }

    protected function console(Array $data) : void
    {
        try {
            $this->event->dispatch(new Event\Console($data['p'] ?? '', $data['m'] ?? '', $data['a'] ?? '', $data['t'] ?? '', $data['s'] ?? ''));
        } catch (\Throwable $e) {
            Logger::writeExceptionLog(__LINE__, __FILE__, $e, $data['t'] ?? '');
        }
    }

    public function workerError(\Swoole\Server $serv, \Swoole\Server\StatusInfo $info) : void
    {
        Logger::writeWarningLogSync(__LINE__, __FILE__, json_encode($info));
    }

    /**
     * @description Manager start event
     *
     * @param Swoole\Http\Server $serv
     *
     * @return void
     */
    public function managerStart(\Swoole\Server $serv) : void
    {
        ko_change_process_name($this->config['name'] . ' master');
    }

    /**
     * @description Worker start event
     *
     * @param Swoole\Http\Server $serv
     *
     * @param int $workerId
     *
     * @return void
     */
    public function workerStart(\Swoole\Server $serv, int $workerId) : void
    {
        ko_change_process_name($this->config['name'] . ' worker');
        $this->initPool();
    }

    public function pipeMessage(\Swoole\Server $serv, PipeMessage $message) : void
    {
        $this->console($message->data);
    }

    /**
     * @description get ip
     *
     * @param int $fd
     *
     * @return string
     */
    public function getClientIP(int $fd) : string
    {
        $info = $this->serv->getClientInfo($fd);
        if (empty($info)) {
            return '';
        }

        return $info['remote_ip'] ?? '';
    }

    abstract protected function initServer();
}
