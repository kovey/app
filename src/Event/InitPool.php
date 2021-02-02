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
use Kovey\App\Components\ServerInterface;

class InitPool implements EventInterface
{
    private ServerInterface $server;

    public function __construct(ServerInterface $server)
    {
        $this->server = $server;
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

    public function getServer() : ServerInterface
    {
        return $this->server;
    }
}
