<?php
namespace carry0987\RememberMe\Example;

use carry0987\RememberMe\Interfaces\CookieHandlerInterface;

class CookieHandler implements CookieHandlerInterface
{
    private static $path = '/';

    public static function clearAuthCookie(): bool
    {
        if (isset($_COOKIE['random_pw'])) {
            return self::setAuthCookie('random_pw', 'none', 0, self::$path);
        }

        return false;
    }

    public static function setAuthCookie(string $name, string $value, int $expire): bool
    {
        $domain = (string) null;

        return setcookie($name, $value, $expire, self::$path, $domain, true, true);
    }

    public static function setPath(string $path): void
    {
        self::$path = $path;
    }
}
