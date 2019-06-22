<?php
require 'DBController.php';
class Auth
{
    public function getMemberByUsername($username)
    {
        $db_handle = DBController::getInstance();
        $query = 'SELECT * FROM members WHERE member_name = ?';
        $result = $db_handle->runQuery($query, 's', array($username));
        return $result;
    }

    public function getMemberByUserID($userID)
    {
        $db_handle = DBController::getInstance();
        $query = 'SELECT * FROM members WHERE member_id = ?';
        $result = $db_handle->runQuery($query, 'i', array($userID));
        return $result;
    }

    public function getTokenByUsername($username)
    {
        $db_handle = DBController::getInstance();
        $query = 'SELECT * FROM tbl_token_auth WHERE username = ?';
        $result = $db_handle->runQuery($query, 's', array($username));
        return $result;
    }

    public function getTokenByUserID($userID)
    {
        $db_handle = DBController::getInstance();
        $query = 'SELECT * FROM tbl_token_auth WHERE user_id = ?';
        $result = $db_handle->runQuery($query, 'i', array($userID));
        return $result;
    }

    public function deleteToken($userID)
    {
        $db_handle = DBController::getInstance();
        $query = 'DELETE FROM tbl_token_auth WHERE user_id = ?';
        $result = $db_handle->update($query, 'i', array($userID));
        return $result;
    }

    public function updateToken($randomArray, $userID)
    {
        $db_handle = DBController::getInstance();
        $query = 'UPDATE tbl_token_auth SET password_hash = ?, selector_hash = ?, expiry_date = ? WHERE user_id = ?';
        $getTime = time() + (30 * 24 * 60 * 60);
        $result = $db_handle->update($query, 'ssii', array($randomArray[0], $randomArray[1], $getTime, $userID));
        return $result;
    }

    public function insertToken($uid, $username, $random_password_hash, $random_selector_hash, $expiry_date)
    {
        $db_handle = DBController::getInstance();
        $query = 'INSERT INTO tbl_token_auth (user_id, username, password_hash, selector_hash, expiry_date) VALUES (?, ?, ?, ?, ?)';
        $result = $db_handle->insert($query, 'isssi', array($uid, $username, $random_password_hash, $random_selector_hash, $expiry_date));
        return $result;
    }

    public function update($query)
    {
        mysqli_query($this->conn, $query);
    }
}
