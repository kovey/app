<?php
/**
 * @description business base
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-07-03 15:40:49
 */
namespace Kovey\App\Module;

use Kovey\Container\Module\HasDbInterface;
use Kovey\Container\Module\HasRedisInterface;
use Kovey\Container\Module\HasGlobalIdInterface;
use Kovey\Connection\Pool;
use Kovey\Sharding\DbInterface;
use Kovey\Sharding\RedisInterface;
use Kovey\Library\Exception\BusiException;
use Kovey\Library\Trace\TraceInterface;;
use Kovey\App\Components\ClickhouseInterface;

abstract class Base implements HasGlobalIdInterface, HasRedisInterface, HasDbInterface, TraceInterface
{
    protected Pool | DbInterface | ClickhouseInterface $database;

    protected Pool | RedisInterface $redis;

    protected string $traceId;

    protected string $spanId;

    protected int $globalId;

    public function setDatabase(mixed $database) : Base
    {
        $this->database = $database;
        return $this;
    }

    public function setRedis(mixed $redis) : Base
    {
        $this->redis = $redis;
        return $this;
    }

    public function setGlobalId(int $globalId) : Base
    {
        $this->globalId = $globalId;
        return $this;
    }

    public function throwBusiException(int $code, string $msg) : void
    {
        throw new BusiException($msg, $code);
    }

    public function setTraceId(string $traceId) : void
    {
        $this->traceId = $traceId;
    }

    public function setSpanId(string $spanId) : void
    {
        $this->spanId = $spanId;
    }

    public function getTraceId() : string
    {
        return $this->traceId;
    }

    public function getSpanId() : string
    {
        return $this->spanId;
    }
}
