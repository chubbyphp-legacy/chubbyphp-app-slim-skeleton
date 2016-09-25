<?php

namespace SlimSkeleton\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;
use SlimSkeleton\Auth\Exception\EmptyPasswordException;
use SlimSkeleton\Auth\Exception\InvalidPasswordException;
use SlimSkeleton\Auth\Exception\UserNotFoundException;
use SlimSkeleton\Model\User;
use SlimSkeleton\Model\UserInterface;
use SlimSkeleton\Repository\UserRepositoryInterface;
use SlimSkeleton\Session\SessionInterface;

final class Auth implements AuthInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @param SessionInterface        $session
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(SessionInterface $session, UserRepositoryInterface $userRepository)
    {
        $this->session = $session;
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

        $this->session->set($request, self::USER_KEY, $user->getId());
    }

    /**
     * @param Request $request
     */
    public function logout(Request $request)
    {
        $this->session->remove($request, self::USER_KEY);
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
        if (!$this->session->has($request, self::USER_KEY)) {
            return null;
        }

        $id = $this->session->get($request, self::USER_KEY);

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
}
