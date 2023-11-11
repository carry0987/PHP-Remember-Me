<?php
namespace carry0987\RememberMe\Interfaces;

interface TokenProviderInterface {
    public function getTokenByUserID(int $userID, string $selector);
    public function resetToken(string $selector);
    public function updateToken(int $userID, string $selector, string $pw_hash);
    public function insertToken(int $userID, string $selector, string $random_pw_hash, int $expiry_date = 0);
}
