<?php
namespace carry0987\RememberMe\Example;

use carry0987\RememberMe\Interfaces\TokenRepositoryInterface;
use PDO;

class DBController implements TokenRepositoryInterface
{
    private $pdo = null;

    public function connectDB(string $host, string $user, string $password, string $database, int $port = 3306)
    {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$database;port=$port;charset=utf8mb4", $user, $password);
        } catch (\PDOException $e) {
            self::throwDBError($e->getMessage(), $e->getCode());
        }
    }

    public function setConnection(PDO $connectDB): self
    {
        $this->pdo = $connectDB;

        return $this;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function getTokenByUserID(int $userID, string $selector)
    {
        $stmt = $this->pdo->prepare('SELECT pw_hash, expiry_date FROM remember_me WHERE user_id = :user_id AND selector_hash = :selector');
        $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':selector', $selector, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function invalidateToken($selector): bool
    {
        $stmt = $this->pdo->prepare('UPDATE remember_me SET pw_hash = :pw_hash, expiry_date = 0 WHERE selector_hash = :selector');
        $stmt->bindValue(':pw_hash', '', PDO::PARAM_STR);
        $stmt->bindParam(':selector', $selector, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function getUserInfo(int $userID): array
    {
        $stmt = $this->pdo->prepare('SELECT username FROM user WHERE uid = :user_id');
        $stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByName(string $username): array
    {
        $results = [];
        $query = $this->pdo->prepare('SELECT uid, password FROM user WHERE username = ?');
        try {
            $query->execute([$username]);
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } catch (\PDOException $e) {
            self::throwDBError($e->getMessage(), $e->getCode());
        }
    }

    public function updateToken(int $userID, string $selector, string $tokenHash): bool
    {
        $query = $this->pdo->prepare('UPDATE remember_me SET pw_hash = ?, expiry_date = ? WHERE user_id = ? AND selector_hash = ?');
        $getTime = time() + (30 * 24 * 60 * 60);
        try {
            $query->execute([$tokenHash, $getTime, $userID, $selector]);
            return true;
        } catch (\PDOException $e) {
            self::throwDBError($e->getMessage(), $e->getCode());
        }
    }

    public function insertToken(int $userID, string $selector, string $tokenHash, int $expiryDate = 0): bool
    {
        $query = $this->pdo->prepare('INSERT INTO remember_me (user_id, selector_hash, pw_hash, expiry_date) VALUES (?, ?, ?, ?)');
        try {
            $query->execute([$userID, $selector, $tokenHash, $expiryDate]);
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
