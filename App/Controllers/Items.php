<?php


namespace App\Controllers;

use App\Auth;
use \Core\View;

class Items extends \Core\Controller
{
    public function indexAction() {

        if(! Auth::isLoggedIn()) {
            Auth::rememberRequestedPage();
            $this->redirect('/login');
        }

        View::renderTemplate('Items/index.html');
    }

}