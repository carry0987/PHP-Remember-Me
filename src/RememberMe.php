<?php
namespace carry0987\RememberMe;

use carry0987\RememberMe\Interfaces\UserProviderInterface;
use carry0987\RememberMe\Interfaces\TokenProviderInterface;

class RememberMe
{
    protected $userProvider;
    protected $tokenProvider;
    protected static $path;

    public function __construct(UserProviderInterface $userProvider, TokenProviderInterface $tokenProvider, string $path)
    {
        $this->userProvider = $userProvider;
        $this->tokenProvider = $tokenProvider;
        self::$path = rtrim($path, '/');
    }

    public function checkUserInfo(int $userID, string $selector, string $randomPW)
    {
        $current_time = time();
        $result = false;
        //Initiate auth token verification diirective to false
        $isPasswordVerified = false;
        $isExpiryDateVerified = false;
        //Get token for username
        $userToken = $this->tokenProvider->getTokenByUserID($userID, $selector);
        if ($userToken !== false) {
            //Validate random password cookie with database
            if (password_verify($randomPW, $userToken['pw_hash'])) {
                $isPasswordVerified = true;
            }
            //Check cookie expiration by date
            if ($userToken['expiry_date'] >= $current_time) {
                $isExpiryDateVerified = true;
            }
        }
        //Redirect if all cookie based validation retuens true
        //Else, mark the token as expired and clear cookies
        if ($userToken !== false && $isPasswordVerified === true && $isExpiryDateVerified === true) {
            $result = $userToken;
        } else {
            if ($userToken !== false) {
                $this->tokenProvider->resetToken($selector);
                //$result = $userToken;
            } else {
                $result = false;
            }
            //Clear cookies
            $this->clearAuthCookie(self::$path);
        }
        return $result;
    }

    public static function getToken(int $length)
    {
        $token = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i ++) {
            $token .= $codeAlphabet[self::cryptoRandSecure(0, $max)];
        }
        return $token;
    }

    public static function clearAuthCookie(string $path = null)
    {
        if (isset($_COOKIE['random_pw'])) {
            self::$path = $path;
            self::setCookie('random_pw', 'none');
        }
    }

    public static function setCookie(string $cookieName, $value, int $cookieTime = 0)
    {
        $domain = (string) null;
        return setcookie($cookieName, $value, $cookieTime, self::$path.'/', $domain, true, true);
    }

    private static function cryptoRandSecure(int $min, int $max)
    {
        $range = $max - $min;
        if ($range < 1) {
            //Not so random
            return $min;
        }
        $log = ceil(log($range, 2));
        //Length in bytes
        $bytes = (int) ($log / 8) + 1;
        //Length in bits
        $bits = (int) $log + 1;
        //Set all lower bits to 1
        $filter = (int) (1 << $bits) - 1;
        do {
            $rnd = hexdec(bin2hex(random_bytes($bytes)));
            //Discard irrelevant bits
            $rnd = $rnd & $filter;
        } while ($rnd >= $range);
        return $min + $rnd;
    }
}
