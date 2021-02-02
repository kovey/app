<?php
/**
 *
 * @description Application global
 *
 * @package     App
 *
 * @time        Tue Sep 24 00:28:03 2019
 *
 * @author      kovey
 */
namespace Kovey\App;

use Kovey\Connection\AppInterface;
use Kovey\Connection\Pools;
use Kovey\Container\ContainerInterface;
use Kovey\Process\UserProcess;
use Kovey\Event\EventManager;
use Kovey\App\Components\Bootstrap;
use Kovey\App\Components\Globals;
use Kovey\App\Components\Work;
use Kovey\App\Components\ServerInterface;
use Kovey\App\Components\AutoloadInterface;

abstract class App implements AppInterface
{
    /**
     * @description config
     *
     * @var Array
     */
    protected Array $config;

    /**
     * @description autoload
     *
     * @var AutoloadInterface
     */
    protected AutoloadInterface $autoload;

    /**
     * @description container
     *
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @description user process
     *
     * @var UserProcess
     */
    protected UserProcess $userProcess;

    /**
     * @description event manager
     *
     * @var EventManager
     */
    protected EventManager $event;

    protected Globals $globals;

    protected ServerInterface $server;

    protected Work $work;

    protected Bootstrap $bootstrap;

    protected Pools $pools;

    /**
     * @description constructor
     *
     * @param Array $config
     *
     * @return Application
     */
    public function __construct(Array $config)
    {
        $this->config = $config;
        $this->pools = new Pools();
        $this->globals = new Globals();
        $this->bootstrap = new Bootstrap();
        $this->event = new EventManager(array(
            'monitor' => Event\Monitor::class,
            'console' => Event\Console::class,
            'initPool' => Event\InitPool::class
        ));
        $this->init()
             ->initWork();
    }

    /**
     * @description register global
     *
     * @param string $name
     *
     * @param mixed $val
     *
     * @return Application
     */
    public function registerGlobal(string $name, mixed $val) : App
    {
        $this->globals->$name = $val;
        return $this;
    }

    /**
     * @description get global
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getGlobal(string $name) : mixed
    {
        return $this->globals->$name;
    }

    /**
     * @description register autoload
     *
     * @param AutoloadInterface $autoload
     *
     * @return App
     */
    public function registerAutoload(AutoloadInterface $autoload) : App
    {
        $this->autoload = $autoload;
        return $this;
    }

    /**
     * @description register container
     *
     * @param ContainerInterface $container
     *
     * @return Application
     */
    public function registerContainer(ContainerInterface $container) : Application
    {
        $this->container = $container;
        $this->work->setContainer($container);

        return $this;
    }

    /**
     * @description monitor
     *
     * @param Array $data
     *
     * @return void
     */
    public function monitor(Event\Monitor $event) : void
    {
        Monitor::write($event->getData());
        go (function ($event) {
            $this->event->dispatch($event);
        }, $event);
    }

    /**
     * @description event listener
     *
     * @param string $type
     *
     * @param callable | Array $fun
     *
     * @return App
     */
    public function on(string $type, callable | Array $fun) : App
    {
        $this->event->addEvent($type, $fun);
        return $this;
    }

    /**
     * @description app run
     *
     * @return void
     */
    public function run() : void
    {
        if (empty($this->server) || !$this->server instanceof ServerInterface) {
            throw new KoveyException('server not register');
        }

        $this->server->start();
    }

    /**
     * @description register bootstrap
     *
     * @param mixed $bootstrap
     *
     * @return Application
     */
    public function registerBootstrap(mixed $bootstrap) : App
    {
        $this->bootstrap->add($bootstrap);
        return $this;
    }

    /**
     * @description run bootstrap
     *
     * @return Application
     */
    public function bootstrap() : App
    {
        $this->bootstrap->run($this);
        return $this;
    }

    /**
     * @description get config
     *
     * @return Array
     */
    public function getConfig() : Array
    {
        return $this->config;
    }

    /**
     * @description get server
     *
     * @return Server
     */
    public function getServer() : ServerInterface
    {
        return $this->server;
    }

    /**
     * @description get user process
     *
     * @return UserProcess
     */
    public function getUserProcess() : UserProcess
    {
        return $this->userProcess;
    }

    /**
     * @description register process
     *
     * @param string $name
     *
     * @param ProcessAbstract $process
     *
     * @return App
     */
    public function registerProcess(string $name, ProcessAbstract $process) : App
    {
        if (!is_object($this->server)) {
            return $this;
        }

        $process->setServer($this->server->getServ());
        $this->userProcess->addProcess($name, $process);
        return $this;
    }

    /**
     * @description register local library path
     *
     * @param string $path
     *
     * @return App
     */
    public function registerLocalLibPath(string $path) : App
    {
        $this->autoload->addLocalPath($path);
        return $this;
    }

    /**
     * @description register pool
     *
     * @param string $name
     *
     * @param PoolInterface $pool
     *
     * @param int $partition
     *
     * @return Application
     */
    public function registerPool(string $name, PoolInterface $pool, int $partition = 0) : AppInterface
    {
        $this->pools->add($name, $pool, $partition);
        return $this;
    }

    /**
     * @description get pool
     *
     * @param string $name
     *
     * @param int $partition
     *
     * @return PoolInterface | null
     */
    public function getPool(string $name, int $partition = 0) : ?PoolInterface
    {
        return $this->pools->get($name, $partition);
    }

    /**
     * @description get container
     *
     * @return ContainerInterface
     */
    public function getContainer() : ContainerInterface
    {
        return $this->container;
    }

    /**
     * @description register user process
     *
     * @param UserProcess $userProcess
     *
     * @return App
     */
    public function registerUserProcess(UserProcess $userProcess) : App
    {
        $this->userProcess = $userProcess;
        return $this;
    }

    /**
     * @description check config
     *
     * @return Application
     *
     * @throws KoveyException
     */
    abstract public function checkConfig() : App;

    abstract protected function initWork() : App;

    abstract protected function init() : App;

    abstract public function registerServer(ServerInterface $server) : App;
}
