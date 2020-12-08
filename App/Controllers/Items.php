<?php


namespace App\Controllers;

use App\Auth;
use \Core\View;

class Items extends Authenticated
{
    public function indexAction() {

//        $this->requireLogin();
        View::renderTemplate('Items/index.html');
    }

}