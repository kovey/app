<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-01-06 19:00:34
 *
 */
namespace Kovey\App\Event;

use Kovey\Event\EventInterface;

class Console implements EventInterface
{
    private string $path;

    private string $method;

    private Array $args;

    private string $traceId;

    private string $spanId;

    public function __construct(string $path, string $method, Array $args, string $traceId, string $spanId)
    {
        $this->path = $path;
        $this->method = $method;
        $this->args = $args;
        $this->traceId = $traceId;
        $this->spanId = $spanId;
    }

    /**
     * @description propagation stopped
     *
     * @return bool
     */
    public function isPropagationStopped() : bool
    {
        return true;
    }

    /**
     * @description stop propagation
     *
     * @return EventInterface
     */
    public function stopPropagation() : EventInterface
    {
        return $this;
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getArgs() : Array
    {
        return $this->args;
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
