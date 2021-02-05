<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-02-02 09:43:57
 *
 */
namespace Kovey\App\Components;

use Kovey\App\App;

class Bootstrap
{
    private Array $boots;

    public function __construct()
    {
        $this->boots = array();
    }

    public function add(mixed $bootstrap) : Bootstrap
    {
        if (!is_object($bootstrap)) {
            return $this;
        }

        $this->boots[] = $bootstrap;
        return $this;
    }

    public function run(App $app)
    {
        foreach ($this->boots as $bootstrap) {
            $funs = get_class_methods($bootstrap);
            foreach ($funs as $fun) {
                if (substr($fun, 0, 6) !== '__init') {
                    continue;
                }

                $bootstrap->$fun($app);
            }
        }
    }
}
