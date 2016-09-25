<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SlimSkeleton\Repository\UserRepository;

class UserController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function getList(Request $request, Response $response)
    {
        $users = $this->userRepository->findAll();

        var_dump($users); die;
    }
}
