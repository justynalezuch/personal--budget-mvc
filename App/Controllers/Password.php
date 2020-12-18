<?php

namespace App\Controllers;

use App\Models\User;
use \Core\View;

/**
 * Password controller
 *
 * PHP version 7.0
 */
class Password extends \Core\Controller
{
    /**
     * Show the forgotten password page
     *
     * @return void
     */
    public function forgotAction() {
        View::renderTemplate('Password/forgot.html');
    }

    public function requestResetAction() {

        User::sendPasswordReset($_POST['email']);

        View::renderTemplate('Password/reset_requested.html');
    }

    public function resetAction() {

        $token = $this->route_params['token'];
        $user = User::findByPasswordReset($token);

        if($user) {
            View::renderTemplate('Password/reset.html', [
                'token' => $token
            ]);
        } else {
            echo 'token invalid';
        }
    }

    /**
     * Reset the user's password
     */
    public function resetPasswordAction() {

        $user = User::findByPasswordReset($_POST['token']);

        if($user) {
            echo 'reset';
        } else {
            echo 'token invalid';
        }
    }
}
