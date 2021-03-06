<?php

namespace App\Models;

use App\Token;
use Core\View;
use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean
     */
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();

            $sql = 'INSERT INTO users (name, email, password_hash, activation_hash)
            VALUES (:name, :email, :password_hash, :activation_hash)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        // Name
        if ($this->name == '') {
            $this->errors[] = 'Podaj nazwę użytkownika';
        }

        if (ctype_alnum($this->name) == false) {
            $this->errors[] = "Nazwa użytkownika może składać się tylko z liter i cyfr (bez polskich znaków)";
        }

        if (strlen($this->name) < 3 || strlen($this->name) > 50) {
            $this->errors[] = "Nazwa użytkownika powinna być nie krótsza niż 3 znaki i nie dłuższa niż 50 znaków.";
        }

        // Email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Podaj poprawny adres email.';
        }

        if (static::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'Istnieje już konto zarejestrowane na podany adres email.';
        }

        if (strlen($this->email) > 50) {
            $this->errors[] = "Adres email nie może być dłuższy niż 50 znaków.";
        }

        // Password
//        if ($this->password != $this->password_confirmation) {
//            $this->errors[] = 'Podane hasła różnią się od siebie.';
//        }

        if(isset($this->password)) {
            if (strlen($this->password) < 8 || strlen($this->password) > 20) {
                $this->errors[] = "Hasło musi posiadać od 8 do 20 znaków.";
            }

            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
                $this->errors[] = 'Hasło musi posiadać co najmniej jedną literę.';
            }

            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors[] = 'Hasło musi posiadać co najmniej jedną cyfrę.';
            }
        }
    }

    /**
     * Check if email already exist.
     *
     * @param $email
     * @return bool
     */
    public static function emailExists($email, $ignore_id = null)
    {
       $user = static::findByEmail($email);
       if($user) {
           if($user->id != $ignore_id) {
               return true;
           }
       }
       return false;
    }

    /**
     * Find user by email.
     *
     * @param $email
     * @return mixed
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Authenticate user
     *
     * @param $email
     * @param $password
     * @return bool
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if($user && $user->is_active) {
            if(password_verify($password, $user->password_hash)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Find user model by ID.
     *
     * @param $id
     * @return mixed
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function sendPasswordReset($email) {

        $user = static::findByEmail($email);

        if($user) {
            if($user->startPasswordReset()) {
                $user->sendPasswordResetEmail();
            }
        }
    }

    /**
     * Start password reset, generate new token and expiry
     * @return mixed
     * @throws \Exception
     */
    protected function startPasswordReset() {

        $token = new Token();
        $token_hash = $token->getHash();
        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60 * 60 * 2; // 2 hours from now

        $sql = 'UPDATE users
                SET password_reset_hash = :password_reset_hash,
                    password_reset_expires_at = :password_reset_expires_at
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':password_reset_hash', $token_hash, PDO::PARAM_STR);
        $stmt->bindValue(':password_reset_expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    protected function sendPasswordResetEmail() {

        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;

        $body = View::getTemplate('Password/reset_email.html', ['url' => $url]);
        $altbody = View::getTemplate('Password/reset_email.txt', ['url' => $url]);

        \App\Mail::send($this->email, 'Password reset', $body, $altbody);
    }

    /**
     * Find user base on password reset token
     *
     * @param $token
     * @return mixed
     * @throws \Exception
     */
    public static function findByPasswordReset($token) {

        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM users WHERE password_reset_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $user = $stmt->fetch();
        if($user) {
            if(strtotime($user->password_reset_expires_at) > time()) {
                return $user;
            }
        }
    }

    /**
     * @param $password
     * @return bool
     */
    public function resetPassword($password) {

        $this->password = $password;
        $this->validate();

       if(empty($this->errors)){

           $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

           $sql = 'UPDATE users
                SET password_hash = :password_hash,
                    password_reset_hash = NULL,
                    password_reset_expires_at = NULL
                WHERE id = :id';

           $db = static::getDB();
           $stmt = $db->prepare($sql);

           $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
           $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

           return $stmt->execute();
       }

       return false;
    }

    public function sendActivationEmail() {

        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

        $body = View::getTemplate('Signup/activation_email.html', ['url' => $url]);
        $altbody = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);


        \App\Mail::send($this->email, 'Account activation', $body, $altbody);
        echo 'send';
    }


    /**
     * Activate the user account with the specified activation token
     *
     * @param string $value Activation token from the URL
     *
     * @return void
     */
    public static function activate($value)
    {
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $sql = 'UPDATE users
                SET is_active = 1,
                    activation_hash = null
                WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * Update user data
     *
     * @param $data
     * @return bool
     */
    public function updateProfile($data) {

            $this->name = $data['name'];
            $this->email = $data['email'];

            if($data['password'] != '') {
                $this->password = $data['password'];
            }

            $this->validate();

            if(empty($this->errors)) {

                $sql = 'UPDATE users
                SET name = :name,
                    email = :email';
                if(isset($this->password)) {
                    $sql .= ' , password_hash = :password_hash';
                }
                $sql .= ' WHERE id = :id;';

                $db = static::getDB();
                $stmt = $db->prepare($sql);

                $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
                $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
                $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

                if(isset($this->password)) {

                    $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                    $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
                }

                return $stmt->execute();
            }

            return false;
    }

}
