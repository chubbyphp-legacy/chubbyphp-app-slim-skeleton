<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\NotFoundException;
use Slim\Router;
use Slim\Views\Twig;
use SlimSkeleton\Auth\AuthInterface;
use SlimSkeleton\Repository\UserRepository;

class UserController
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param AuthInterface $auth
     * @param Router $router
     * @param Twig $twig
     * @param UserRepository $userRepository
     */
    public function __construct(AuthInterface $auth, Router $router, Twig $twig, UserRepository $userRepository)
    {
        $this->auth = $auth;
        $this->router = $router;
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function listAll(Request $request, Response $response)
    {
        $users = $this->userRepository->findBy();

        return $this->twig->render($response, '@SlimSkeleton/user/list.html.twig', [
            'users' => json_decode(json_encode($users), true),
            'authenticatedUser' => $this->auth->getAuthenticatedUser($request)
        ]);

    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws NotFoundException
     */
    public function view(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw new NotFoundException($request, $response);
        }

        return $this->twig->render($response, '@SlimSkeleton/user/view.html.twig', [
            'user' => json_decode(json_encode($user), true),
            'authenticatedUser' => $this->auth->getAuthenticatedUser($request)
        ]);
    }
}
