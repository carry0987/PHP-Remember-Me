<?php 
class Util
{
    private $path = '/';

    public function __construct($path)
    {
        $this->path = rtrim($path, '/\\');
    }

    public function getToken($length)
    {
        $token = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i ++) {
            $token .= $codeAlphabet[$this->cryptoRandSecure(0, $max)];
        }
        return $token;
    }

    public function cryptoRandSecure($min, $max)
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
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            //Discard irrelevant bits
            $rnd = $rnd & $filter;
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    public function redirect($url)
    {
        header('Location:'.$url);
        exit;
    }

    public function clearAuthCookie()
    {
        if (isset($_COOKIE['member_login'])) {
            $this->setCookie('member_login', '');
        }
        if (isset($_COOKIE['random_password'])) {
            $this->setCookie('random_password', '');
        }
        if (isset($_COOKIE['random_selector'])) {
            $this->setCookie('random_selector', '');
        }
    }

    public function setCookie($cookieName, $value, $cookieTime = 0)
    {
        $security = (isset($_SERVER['HTTPS'])) ? true : false;
        setcookie($cookieName, $value, $cookieTime, $this->path.'/', null, $security, true);
    }
}
