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

    public function editAction() {

        View::renderTemplate('Profile/edit.html', [
            'user' => Auth::getUser()
        ]);

    }

}