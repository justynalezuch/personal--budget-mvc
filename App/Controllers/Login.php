<?php

namespace App\Controllers;

use App\Flash;
use App\Models\User;
use \Core\View;
use \App\Auth;

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

       $user = User::authenticate($_POST['email'], $_POST['password']);
       if($user) {
           Auth::login($user);
           $this->redirect(Auth::getReturnToPage());
       }
       else {
           View::renderTemplate('Login/new.html', [
               'email' => $_POST['email']
           ]);
       }

    }

    /**
     * Log out user, redirect to new request - to start new session and show logout message.
     */
    public function destroyAction() {
        Auth::logout();
        $this->redirect('/login/show-logout-message');
    }

    /**
     * Show a "logged out" flash message.
     */
    public function showLogoutMessageAction() {

        Flash::addMessage('Wylogowałeś się poprawnie.');
        $this->redirect('/');
    }


}