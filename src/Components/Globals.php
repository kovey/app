<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-02-02 11:06:39
 *
 */
namespace Kovey\App\Components;

class Globals
{
    /**
     * @description global
     *
     * @var Array
     */
    private Array $globals;

    public function __construct()
    {
        $this->globals = array();
    }

    public function __set(string $name, mixed $value) : void
    {
        $this->globals[$name] = $value;
    }

    public function __get(string $name) : mixed
    {
        return $this->globals[$name] ?? null;
    }
}
