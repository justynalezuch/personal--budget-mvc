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
        $user = $this->getUserOrExit($token);

        View::renderTemplate('Password/reset.html', [
            'token' => $token
        ]);
    }

    /**
     * Reset the user's password
     */
    public function resetPasswordAction() {

        $token = $_POST['token'];
        $user = $this->getUserOrExit($token);

        if($user->resetPassword($_POST['password'])) {
            echo 'password valid';
        } else {
            View::renderTemplate('Password/reset.html', [
                'token' => $token,
                'user' => $user
            ]);
        }
    }

    protected function getUserOrExit($token) {

        $user = User::findByPasswordReset($token);

        if($user) {
            return $user;
        } else {
            View::renderTemplate('Password/token_expired.html');
            exit;
        }
    }
}
