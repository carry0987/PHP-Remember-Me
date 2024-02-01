<?php
namespace carry0987\RememberMe\Interfaces;

interface TokenRepositoryInterface
{
    public function getTokenByUserID(int $userID, string $selector);
    public function invalidateToken(string $selector): bool;
}
