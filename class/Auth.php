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
        $userID = (int) $userID;
        $db_handle = DBController::getInstance();
        $query = 'SELECT * FROM members WHERE member_id = ?';
        $result = $db_handle->runQuery($query, 'i', array($userID));
        return $result;
    }

    public function getTokenByUserID($userID, $selector = 0)
    {
        $userID = (int) $userID;
        $db_handle = DBController::getInstance();
        $query = 'SELECT * FROM tbl_token_auth INNER JOIN members ON member_id = user_id WHERE user_id = ? AND selector_hash = ?';
        $result = $db_handle->runQuery($query, 'is', array($userID, $selector));
        $tokenResult = ($result !== false) ? $result : false;
        return $tokenResult;
    }

    public function markAsExpired($selector)
    {
        $db_handle = DBController::getInstance();
        $query = 'UPDATE tbl_token_auth SET password_hash = ? WHERE selector_hash = ?';
        $empty = 1;
        $result = $db_handle->update($query, 'ss', array($empty, $selector));
        return $result;
    }

    public function updateToken($uid, $selector, $random_password_hash)
    {
        $db_handle = DBController::getInstance();
        $query = 'UPDATE tbl_token_auth SET password_hash = ? WHERE user_id = ? AND selector_hash = ?';
        $result = $db_handle->insert($query, 'sis', array($random_password_hash, $uid, $selector));
        return $result;
    }

    public function insertToken($uid, $random_selector_hash, $random_password_hash, $expiry_date)
    {
        $db_handle = DBController::getInstance();
        $query = 'INSERT INTO tbl_token_auth (user_id, selector_hash, password_hash, expiry_date) VALUES (?, ?, ?, ?)';
        $result = $db_handle->insert($query, 'issi', array($uid, $random_selector_hash, $random_password_hash, $expiry_date));
        return $result;
    }

    public function update($query)
    {
        mysqli_query($this->conn, $query);
    }
}
