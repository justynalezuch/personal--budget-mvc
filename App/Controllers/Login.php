<?php

namespace App\Controllers;

use App\Models\User;
use \Core\View;

/**
 * Login controller
 *
 * PHP version 7.0
 */

class Login extends \Core\Controller
{
    /**
     * Show the login page
     *
     * @return void;
     */
    public function newAction() {
        View::renderTemplate('Login/new.html');
    }

    public function createAction() {

       $user = User::findByEmail($_POST['email']);
       var_dump($user);

    }


}
