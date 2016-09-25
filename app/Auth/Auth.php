<?php

namespace SlimSkeleton\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use PSR7Session\Http\SessionMiddleware;
use PSR7Session\Session\LazySession;
use SlimSkeleton\Auth\Exception\EmptyPasswordException;
use SlimSkeleton\Auth\Exception\InvalidPasswordException;
use SlimSkeleton\Auth\Exception\UserNotFoundException;
use SlimSkeleton\Model\User;
use SlimSkeleton\Model\UserInterface;
use SlimSkeleton\Repository\UserRepositoryInterface;

final class Auth implements AuthInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     *
     * @throws InvalidPasswordException
     * @throws UserNotFoundException
     */
    public function login(Request $request)
    {
        $data = $request->getParsedBody();

        /** @var User $user */
        if (null === $user = $this->userRepository->findOneBy(['email' => $data['email']])) {
            throw UserNotFoundException::create($data['email']);
        }

        if (!password_verify($data['password'], $user->getPassword())) {
            throw InvalidPasswordException::create();
        }

        $this->getSession($request)->set(self::USER_KEY, $user->getId());
    }

    /**
     * @param Request $request
     */
    public function logout(Request $request)
    {
        $this->getSession($request)->remove(self::USER_KEY);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isAuthenticated(Request $request): bool
    {
        return null !== $this->getAuthenticatedUser($request);
    }

    /**
     * @param Request $request
     *
     * @return UserInterface|null
     */
    public function getAuthenticatedUser(Request $request)
    {
        if (!$this->getSession($request)->has(self::USER_KEY)) {
            return null;
        }

        $id = $this->getSession($request)->get(self::USER_KEY);

        return $this->userRepository->find($id);
    }

    /**
     * @param string $password
     *
     * @return string
     *
     * @throws EmptyPasswordException
     */
    public function hashPassword(string $password): string
    {
        if ('' === $password) {
            throw EmptyPasswordException::create();
        }

        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param Request $request
     *
     * @return LazySession
     */
    private function getSession(Request $request): LazySession
    {
        return $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
    }
}
