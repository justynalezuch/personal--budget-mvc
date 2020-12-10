<?php


namespace App;


class Flash
{
    /**
     * Message types
     */
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    /**
     * @param $message
     */
    public static function addMessage($message, $type = 'success') {

        if(! isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }

        $_SESSION['flash_notifications'][] = [
          'body' => $message,
          'type' => $type
        ];
    }

    /**
     * Get flash notifications
     *
     * @return mixed Array of messages or null
     */
    public static function getMessages(){

        if (isset($_SESSION['flash_notifications'])) {

            $messages = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);
            return $messages;
        }
    }

}