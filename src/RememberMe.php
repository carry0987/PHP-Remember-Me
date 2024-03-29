<?php
namespace carry0987\RememberMe;

use carry0987\RememberMe\Interfaces\TokenRepositoryInterface;
use carry0987\RememberMe\Interfaces\CookieHandlerInterface;

class RememberMe
{
    protected $tokenRepository;
    protected $cookieHandler;

    public function __construct(TokenRepositoryInterface $tokenRepository, CookieHandlerInterface $cookieHandler)
    {
        $this->tokenRepository = $tokenRepository;
        $this->cookieHandler = $cookieHandler;
    }

    public function verifyToken(int $userID, string $selector, string $randomPW): array
    {
        $current_time = time();
        $result = [];
        //Initiate auth token verification directive to false
        $isPasswordVerified = false;
        $isExpiryDateVerified = false;
        //Get token for username
        $userToken = $this->tokenRepository->getTokenByUserID($userID, $selector);
        if (!empty($userToken)) {
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
        if (!empty($userToken) && $isPasswordVerified === true && $isExpiryDateVerified === true) {
            $result = $userToken;
        } else {
            if (!empty($userToken)) {
                $this->tokenRepository->invalidateToken($selector);
            } else {
                $result = [];
            }
            //Clear cookies
            $this->cookieHandler->clearAuthCookie();
        }

        return $result;
    }

    public static function getToken(int $length): string
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

    private static function cryptoRandSecure(int $min, int $max): int
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
