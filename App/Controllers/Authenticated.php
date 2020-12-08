<?php


namespace App\Controllers;


abstract class Authenticated extends \Core\Controller
{

    /**
     * Require the user to be authenticated before giving access to methods in the controller
     */
    protected function before()
    {
        $this->requireLogin();
    }
}