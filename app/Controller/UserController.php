<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\NotFoundException;
use Slim\Router;
use Slim\Views\Twig;
use SlimSkeleton\Auth\AuthInterface;
use SlimSkeleton\Auth\Exception\EmptyPasswordException;
use SlimSkeleton\Model\User;
use SlimSkeleton\Repository\UserRepositoryInterface;
use SlimSkeleton\Validation\ValidatorInterface;

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
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param AuthInterface      $auth
     * @param Router             $router
     * @param Twig               $twig
     * @param UserRepositoryInterface     $userRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(
        AuthInterface $auth,
        Router $router,
        Twig $twig,
        UserRepositoryInterface $userRepository,
        ValidatorInterface $validator
    ) {
        $this->auth = $auth;
        $this->router = $router;
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function listAll(Request $request, Response $response)
    {
        $users = $this->userRepository->findBy();

        return $this->twig->render($response, '@SlimSkeleton/user/list.html.twig', [
            'users' => prepareForView($users),
            'authenticatedUser' => prepareForView($this->auth->getAuthenticatedUser($request)),
        ]);
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
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
            'user' => prepareForView($user),
            'authenticatedUser' => prepareForView($this->auth->getAuthenticatedUser($request)),
        ]);
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        $user = new User();

        if ('POST' === $request->getMethod()) {
            try {
                $data = $request->getParsedBody();

                $user->setEmail($data['email']);
                $user->setPassword($this->auth->hashPassword($data['password']));

                if ([] === $errorMessages = $this->validator->validateModel($user)) {
                    $this->userRepository->insert($user);

                    return $response
                        ->withStatus(302)
                        ->withHeader('Location', $this->router->pathFor('user_edit', ['id' => $user->getId()]))
                        ;
                }
            } catch (EmptyPasswordException $e) {
                $errorMessages['password'] = [$e->getMessage()];
            }
        }

        return $this->twig->render($response, '@SlimSkeleton/user/create.html.twig', [
            'user' => prepareForView($user),
            'errorMessages' => $errorMessages ?? [],
            'authenticatedUser' => prepareForView($this->auth->getAuthenticatedUser($request)),
        ]);
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws NotFoundException
     */
    public function edit(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        /** @var User $user */
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw new NotFoundException($request, $response);
        }

        if ('POST' === $request->getMethod()) {
            try {
                $data = $request->getParsedBody();

                $user->setEmail($data['email']);
                $user->setPassword($this->auth->hashPassword($data['password']));

                if ([] === $errorMessages = $this->validator->validateModel($user)) {
                    $this->userRepository->update($user);

                    return $response
                        ->withStatus(302)
                        ->withHeader('Location', $this->router->pathFor('user_edit', ['id' => $user->getId()]))
                    ;
                }
            } catch (EmptyPasswordException $e) {
                $errorMessages['password'] = [$e->getMessage()];
            }
        }

        return $this->twig->render($response, '@SlimSkeleton/user/edit.html.twig', [
            'user' => prepareForView($user),
            'errorMessages' => $errorMessages ?? [],
            'authenticatedUser' => prepareForView($this->auth->getAuthenticatedUser($request)),
        ]);
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws NotFoundException
     */
    public function delete(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        /** @var User $user */
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw new NotFoundException($request, $response);
        }

        $authenticatedUser = $this->auth->getAuthenticatedUser($request);

        if ($authenticatedUser->getId() === $user->getId()) {
            throw new \Exception('Cant delete own user!');
        }

        $this->userRepository->delete($user);

        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('user_list'));
    }
}
