<?php

namespace App\Controllers;
/**
 * Home controller
 */
class Home extends \Core\Controller
{

    protected function before()
    {
        // Make sure an admin user is logged in for example
        // return false;
    }
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        echo 'Hello from the index action in the Home controller!';
    }

}
