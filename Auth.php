<?php
require 'DBController.php';
class Auth
{
    function getMemberByUsername($username)
    {
        $db_handle = new DBController();
        $query = 'SELECT * FROM members WHERE member_name = ?';
        $result = $db_handle->runQuery($query, 's', array($username));
        return $result;
    }
    
    function getTokenByUsername($username, $expired)
    {
        $db_handle = new DBController();
        $query = 'SELECT * FROM tbl_token_auth WHERE username = ? AND is_expired = ?';
        $result = $db_handle->runQuery($query, 'si', array($username, $expired));
        return $result;
    }
    
    function markAsExpired($tokenId)
    {
        $db_handle = new DBController();
        $query = 'UPDATE tbl_token_auth SET is_expired = ? WHERE id = ?';
        $expired = 1;
        $result = $db_handle->update($query, 'ii', array($expired, $tokenId));
        return $result;
    }
    
    function insertToken($uid, $username, $random_password_hash, $random_selector_hash, $expiry_date)
    {
        $db_handle = new DBController();
        $query = 'INSERT INTO tbl_token_auth (user_id, username, password_hash, selector_hash, expiry_date) VALUES (?, ?, ?, ?, ?)';
        $result = $db_handle->insert($query, 'isssi', array($uid, $username, $random_password_hash, $random_selector_hash, $expiry_date));
        return $result;
    }
    
    function update($query)
    {
        mysqli_query($this->conn,$query);
    }
}
