<?php


namespace App\Controllers;


use App\Flash;
use Core\View;
use App\Auth;

class Profile extends Authenticated
{
    public function before()
    {
        parent::before();
        $this->user = Auth::getUser();
    }

    public function showAction() {

        View::renderTemplate('Profile/show.html', [
            'user' => $this->user
        ]);

    }

    public function editAction() {

        View::renderTemplate('Profile/edit.html', [
            'user' => $this->user
        ]);

    }

    public function updateAction() {

        if($this->user->updateProfile($_POST)) {
            Flash::addMessage('Successfully updated profile data.');
            $this->redirect('/profile/show');
        } else {
            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user
            ]);
        }
    }
}