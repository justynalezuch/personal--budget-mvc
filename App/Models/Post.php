<?php

namespace App\Models;

use Core\Model;
use PDO;

class Post extends \Core\Model
{

    /**
     * Get all the posts as an associative array
     *
     * @return array
     */
    public static function getAll()
    {
        $host = 'localhost';
        $dbname = 'mvc-personal-budget';
        $username = 'root';
        $password = '';
    
        try {
//            $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",
//                          $username, $password);
            $db =  Model::getDB();

            $stmt = $db->query('SELECT id, title, content FROM posts
                                ORDER BY created_at');
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
