<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use SlimSkeleton\Repository\UserRepository;

class UserController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @param UserRepository $userRepository
     * @param Twig $twig
     */
    public function __construct(UserRepository $userRepository, Twig $twig)
    {
        $this->userRepository = $userRepository;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function listAll(Request $request, Response $response)
    {
        $users = $this->userRepository->findAll();

        return $this->twig->render($response, '@SlimSkeleton/user/list.html.twig', [
            'users' => json_decode(json_encode($users))
        ]);

    }

    public function view(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        $user = $this->userRepository->find($id);

        var_dump($user);
    }
}
