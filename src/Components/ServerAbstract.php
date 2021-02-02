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

abstract class ServerAbstract implements ServerInterface
{
    protected EventManager $event;

    protected Array $config;

    protected Server $serv;

    final public function __construct(Array $config)
    {
        $this->config = $config;
        $this->event = new EventManager(array(
            'monitor' => Event\Monitor::class,
            'console' => Event\Console::class,
            'initPool' => Event\InitPool::class
        ));

        $this->initServer();
        $this->serv->on('workerStart', array($this, 'workerStart'));
        $this->serv->on('managerStart', array($this, 'managerStart'));
        $this->serv->on('pipeMessage', array($this, 'pipeMessage'));
        $this->serv->on('workerError', array($this, 'workerError'));
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
            $this->event->dispatch(new Event\Console($data['p'] ?? '', $data['m'] ?? '', $data['a'] ?? '', $data['t'] ?? ''));
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

    abstract protected function initServer();
}
