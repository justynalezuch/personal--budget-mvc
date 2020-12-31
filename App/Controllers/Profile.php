<?php


namespace App\Controllers;


use Core\View;

class Profile extends Authenticated
{
    public function showAction() {

        View::renderTemplate('Profile/show.html');

    }

}