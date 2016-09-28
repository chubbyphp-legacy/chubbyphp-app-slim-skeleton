<?php

namespace SlimSkeleton\Security;

use Psr\Http\Message\ServerRequestInterface as Request;
use SlimSkeleton\Security\Exception\AbstractLoginException;
use SlimSkeleton\Security\Exception\EmptyPasswordException;
use SlimSkeleton\Model\UserInterface;

interface AuthInterface
{
    const USER_KEY = 'user';

    /**
     * @param Request $request
     *
     * @throws AbstractLoginException
     */
    public function login(Request $request);

    /**
     * @param Request $request
     */
    public function logout(Request $request);

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isAuthenticated(Request $request): bool;

    /**
     * @param Request $request
     *
     * @return UserInterface|null
     */
    public function getAuthenticatedUser(Request $request);

    /**
     * @param string $password
     *
     * @throws EmptyPasswordException
     *
     * @return string
     */
    public function hashPassword(string $password): string;
}
