<?php
namespace carry0987\RememberMe\Interfaces;

interface DatabaseInterface {
    public function getTokenByUserID(int $userID, string $selector);
    public function resetToken(string $selector);
    public function updateToken(int $userID, string $selector, string $tokenHash);
    public function insertToken(int $userID, string $selector, string $tokenHash, int $expiryDate = 0);
}
