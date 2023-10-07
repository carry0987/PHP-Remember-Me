<?php
namespace carry0987\RememberMe;

class DBController
{
    private $connectDB = null;

    public function connectDB(string $host, string $user, string $password, string $database, int $port = 3306)
    {
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            //Handle Exception of MySQLi
            $driver = new \mysqli_driver();
            $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
            $this->connectDB = new \mysqli($host, $user, $password, $database, $port);
            $this->mysqli_version = $this->connectDB->server_info;
            $this->connectDB->set_charset('utf8mb4');
            return $this->connectDB;
        } catch (\mysqli_sql_exception $e) {
            echo $this->throwDBError($e->getMessage(), $e->getCode());
            exit();
        }
    }

    public function setConnection(\mysqli $connectDB)
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
        $query = 'SELECT uid, password FROM user WHERE username = ?';
        $stmt = $this->connectDB->stmt_init();
        try {
            $stmt->prepare($query);
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows != 0) {
                while ($row = $result->fetch_assoc()) {
                    $results[] = $row;
                }
            }
            return isset($results[0]['uid']) ? $results[0] : false;
        } catch (\mysqli_sql_exception $e) {
            echo self::throwDBError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    public function getTokenByUserID(int $userID, string $selector)
    {
        $query = 'SELECT pw_hash, expiry_date, user.username FROM remember_me 
                INNER JOIN user ON user.uid = remember_me.user_id 
                WHERE user_id = ? AND selector_hash = ?';
        $stmt = $this->connectDB->stmt_init();
        try {
            $stmt->prepare($query);
            $stmt->bind_param('is', $userID, $selector);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows != 0) {
                while ($row = $result->fetch_assoc()) {
                    $results = $row;
                }
            } else {
                $results = false;
            }
            return $results;
        } catch (\mysqli_sql_exception $e) {
            echo self::throwDBError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    public function resetToken(string $selector)
    {
        $query = 'UPDATE remember_me SET pw_hash = ?, expiry_date = ? WHERE selector_hash = ?';
        $stmt = $this->connectDB->stmt_init();
        $empty = '';
        $date = 0;
        try {
            $stmt->prepare($query);
            $stmt->bind_param('sis', $empty, $date, $selector);
            $stmt->execute();
            return true;
        } catch (\mysqli_sql_exception $e) {
            echo self::throwDBError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    public function updateToken(int $userID, string $selector, string $pw_hash)
    {
        $query = 'UPDATE remember_me SET pw_hash = ?, expiry_date = ? WHERE user_id = ? AND selector_hash = ?';
        $stmt = $this->connectDB->stmt_init();
        $getTime = time() + (30 * 24 * 60 * 60);
        try {
            $stmt->prepare($query);
            $stmt->bind_param('siis', $pw_hash, $getTime, $userID, $selector);
            $stmt->execute();
            return true;
        } catch (\mysqli_sql_exception $e) {
            echo self::throwDBError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    public function insertToken(int $userID, string $selector, string $random_pw_hash, int $expiry_date = 0)
    {
        $query = 'INSERT INTO remember_me (user_id, selector_hash, pw_hash, expiry_date) VALUES (?, ?, ?, ?)';
        $stmt = $this->connectDB->stmt_init();
        try {
            $stmt->prepare($query);
            $stmt->bind_param('issi', $userID, $selector, $random_pw_hash, $expiry_date);
            $stmt->execute();
            return true;
        } catch (\mysqli_sql_exception $e) {
            echo self::throwDBError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    //Throw database error excetpion
    private static function throwDBError(string $message, int $code)
    {
        $error = '<h1>Service unavailable</h1>'."\n";
        $error .= '<h2>Error Info :'.$message.'</h2>'."\n";
        $error .= '<h3>Error Code :'.$code.'</h3>'."\n";
        return $error;
    }
}
