<?php

namespace SlimSkeleton\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use SlimSkeleton\Auth\Exception\AbstractAuthException;

interface AuthInterface
{
    const USER_KEY = 'user';

    /**
     * @param Request $request
     * @return bool
     */
    public function isAuthenticated(Request $request): bool;

    /**
     * @param Request $request
     *
     * @throws AbstractAuthException
     */
    public function authenticate(Request $request);

    /**
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string;
}
