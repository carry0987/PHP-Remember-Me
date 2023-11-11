<?php
namespace carry0987\RememberMe\Interfaces;

interface UserProviderInterface {
    public function getUserByName(string $username);
}
