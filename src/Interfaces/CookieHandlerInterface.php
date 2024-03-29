<?php
namespace carry0987\RememberMe\Interfaces;

interface CookieHandlerInterface
{
    public static function clearAuthCookie(): bool;
    public static function setAuthCookie(string $name, string $value, int $expire): bool;
}
