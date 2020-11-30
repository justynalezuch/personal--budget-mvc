<?php

namespace App\Controllers;

use App\Models\User;
use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class SignUp extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    /**
     * Sign up new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);
        $user->save();

        View::renderTemplate('Signup/success.html');
    }

}
