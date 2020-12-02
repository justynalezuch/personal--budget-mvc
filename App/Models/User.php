<?php

namespace App\Models;

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

    public function __construct($data)
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
        
        if(empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (name, email, password_hash)
            VALUES (:name, :email, :password_hash)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

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

        if(ctype_alnum($this->name) == false) {
            $this->errors[] = "Nazwa użytkownika może składać się tylko z liter i cyfr (bez polskich znaków)";
        }

        if(strlen($this->name) < 3 || strlen($this->name) > 50) {
            $this->errors[] = "Nazwa użytkownika powinna być nie krótsza niż 3 znaki i nie dłuższa niż 50 znaków.";
        }

        // Email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Podaj poprawny adres email.';
        }

        if(strlen($this->email) >50) {
            $this->errors[] = "Adres email nie może być dłuższy niż 50 znaków.";
        }

        // Password
        if ($this->password != $this->password_confirmation) {
            $this->errors[] = 'Podane hasła różnią się od siebie.';
        }

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
