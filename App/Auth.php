<?php


namespace App;


use App\Models\User;

class Auth
{

    public static function login($user, $remember_me) {

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;

        if($remember_me) {
            if($user->rememberLogin()) {
                setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');
            }
        }
    }

    public static function logout() {

        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
    }

//    public static function isLoggedIn() {
//
//        return isset($_SESSION['user_id']);
//    }

    /**
     * Remember the originally requested page in the session.
     */
    public static function rememberRequestedPage() {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the requested page to return to after requiring login, or default - home page
     *
     * @return mixed|string
     */
    public static function getReturnToPage() {
        return $_SESSION['return_to'] ?? '/';
    }

    /**
     * Get the current logged in user
     *
     * @return mixed The user model or null
     */

    public static function getUser() {

        if(isset($_SESSION['user_id'])) {
           return User::findByID($_SESSION['user_id']);
        }
    }

}