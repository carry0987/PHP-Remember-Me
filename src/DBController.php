<?php
namespace carry0987\RememberMe;

use PDO;

class DBController
{
    private $connectDB = null;

    public function connectDB(string $host, string $user, string $password, string $database, int $port = 3306)
    {
        try {
            $this->connectDB = new PDO("mysql:host=$host;dbname=$database;port=$port;charset=utf8mb4", $user, $password);
        } catch (\PDOException $e) {
            self::throwDBError($e->getMessage(), $e->getCode());
        }
    }

    public function setConnection($connectDB)
    {
        $this->connectDB = $connectDB;
        return $this;
    }

    public function getConnection()
    {
        return $this->connectDB;
    }

    public function getUserByName(string $username)
    {
        $results = array();
        $query = $this->connectDB->prepare('SELECT uid, password FROM user WHERE username = ?');
        try {
            $query->execute([$username]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            return (!empty($results)) ? $results : false;
        } catch (\PDOException $e) {
            self::throwDBError($e->getMessage(), $e->getCode());
        }
    }

    public function getTokenByUserID(int $userID, string $selector)
    {
        $query = $this->connectDB->prepare('SELECT pw_hash, expiry_date, user.username FROM remember_me 
                INNER JOIN user ON user.uid = remember_me.user_id 
                WHERE user_id = ? AND selector_hash = ?');
        try {
            $query->execute([$userID, $selector]);
            $results = $query->fetch(PDO::FETCH_ASSOC);
            return (!empty($results)) ? $results : false;
        } catch (\PDOException $e) {
            self::throwDBError($e->getMessage(), $e->getCode());
        }
    }

    public function resetToken(string $selector)
    {
        $query = $this->connectDB->prepare('UPDATE remember_me SET pw_hash = ?, expiry_date = ? WHERE selector_hash = ?');
        $empty = '';
        $date = 0;
        try {
            $query->execute([$empty, $date, $selector]);
            return true;
        } catch (\PDOException $e) {
            self::throwDBError($e->getMessage(), $e->getCode());
        }
    }

    public function updateToken(int $userID, string $selector, string $pw_hash)
    {
        $query = $this->connectDB->prepare('UPDATE remember_me SET pw_hash = ?, expiry_date = ? WHERE user_id = ? AND selector_hash = ?');
        $getTime = time() + (30 * 24 * 60 * 60);
        try {
            $query->execute([$pw_hash, $getTime, $userID, $selector]);
            return true;
        } catch (\PDOException $e) {
            self::throwDBError($e->getMessage(), $e->getCode());
        }
    }

    public function insertToken(int $userID, string $selector, string $random_pw_hash, int $expiry_date = 0)
    {
        $query = $this->connectDB->prepare('INSERT INTO remember_me (user_id, selector_hash, pw_hash, expiry_date) VALUES (?, ?, ?, ?)');
        try {
            $query->execute([$userID, $selector, $random_pw_hash, $expiry_date]);
            return true;
        } catch (\PDOException $e) {
            self::throwDBError($e->getMessage(), $e->getCode());
        }
    }

    private static function throwDBError(string $message, int $code)
    {
        $error = '<h1>Service unavailable</h1>'."\n";
        $error .= '<h2>Error Info :'.$message.'</h2>'."\n";
        $error .= '<h3>Error Code :'.$code.'</h3>'."\n";

        throw new \PDOException($error);
    }
}
