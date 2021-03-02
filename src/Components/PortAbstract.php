<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-02-26 17:38:27
 *
 */
namespace Kovey\App\Components;

use Kovey\Event\EventManager;
use Swoole\Server;
use Kovey\Logger\Logger;
use Kovey\App\Event;
use Swoole\Server\Port;

abstract class PortAbstract implements ServerInterface
{
    const TCP_PORT = 1;

    protected EventManager $event;

    protected Array $config;

    protected Server $serv;

    protected Port $port;

    final public function __construct(Server $serv, Array $config, int $type = self::TCP_PORT)
    {
        $this->serv = $serv;
        $this->config = $config;
        $this->event = new EventManager(array(
            'monitor' => Event\Monitor::class
        ));
        $this->port = $this->serv->listen($this->config['host'], $this->config['port'], $type == self::TCP_PORT ? SWOOLE_SOCK_TCP : SWOOLE_SOCK_UDP);
        $this->init();
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

    abstract protected function init() : void;
}
