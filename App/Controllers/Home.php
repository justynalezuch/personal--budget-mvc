<?php

namespace App\Controllers;

use App\Auth;
use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */

class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        \App\Mail::send(
            'justynalezuch@gmail.com',
            'Here is the subject',
            'This is the HTML message body <b>in bold!</b>',
            'Alt body'

            );

        View::renderTemplate('Home/index.html', [
            'user' => Auth::getUser()
        ]);
    }
}
