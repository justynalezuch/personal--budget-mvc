<?php

namespace Core;

use mysql_xdevapi\Exception;

/**
 * Base controller
 */

abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        $method = $name . 'Action';

        if(method_exists($this, $method)) {
            if($this->before() !== false) {
                call_user_func_array([$this, $method], $arguments);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller ". get_class($this));

        }
    }

    /**
     * Before filter - called before an action method
     */
    protected function before() {

    }

    /**
     * After filter - called after an action method
     */
    protected function after() {

    }

    public function redirect($url) {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url,  true, 303);
        exit;
    }
}
