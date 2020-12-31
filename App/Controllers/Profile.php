<?php


namespace App\Controllers;


use Core\View;
use App\Auth;

class Profile extends Authenticated
{
    public function showAction() {

        View::renderTemplate('Profile/show.html', [
            'user' => Auth::getUser()
        ]);

    }

}