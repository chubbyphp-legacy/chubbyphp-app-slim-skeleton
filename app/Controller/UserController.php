<?php

namespace SlimSkeleton\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Router;
use Slim\Views\Twig;
use SlimSkeleton\Auth\AuthInterface;
use SlimSkeleton\Auth\Exception\EmptyPasswordException;
use SlimSkeleton\Model\User;
use SlimSkeleton\Repository\UserRepositoryInterface;
use SlimSkeleton\Session\SessionInterface;
use SlimSkeleton\Validation\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param AuthInterface           $auth
     * @param Router                  $router
     * @param SessionInterface        $session
     * @param Twig                    $twig
     * @param UserRepositoryInterface $userRepository
     * @param ValidatorInterface      $validator
     */
    public function __construct(
        AuthInterface $auth,
        Router $router,
        SessionInterface $session,
        Twig $twig,
        UserRepositoryInterface $userRepository,
        ValidatorInterface $validator
    ) {
        parent::__construct($auth, $router, $session, $twig);

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

        return $this->twig->render($response, '@SlimSkeleton/user/list.html.twig',
            array_replace_recursive(
                $this->getGenericTwigVariables($request),
                [
                    'users' => prepareForView($users),
                ]
            )
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function view(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        $user = $this->userRepository->find($id);
        if (null === $user) {
            return $this->getErrorResponse(
                $request,
                $response,
                404,
                'User not found',
                sprintf('There is not user with id %s', $id)
            );
        }

        return $this->twig->render($response, '@SlimSkeleton/user/view.html.twig',
            array_replace_recursive(
                $this->getGenericTwigVariables($request),
                [
                    'user' => prepareForView($user),
                ]
            )
        );
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

                    return $this->getRedirectResponse($response, 302, 'user_edit', ['id' => $user->getId()]);
                }
            } catch (EmptyPasswordException $e) {
                $errorMessages['password'] = [$e->getMessage()];
            }
        }

        return $this->twig->render($response, '@SlimSkeleton/user/create.html.twig',
            array_replace_recursive(
                $this->getGenericTwigVariables($request),
                [
                    'user' => prepareForView($user),
                    'errorMessages' => $errorMessages ?? [],
                ]
            )
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function edit(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        /** @var User $user */
        $user = $this->userRepository->find($id);
        if (null === $user) {
            return $this->getErrorResponse(
                $request,
                $response,
                404,
                'User not found',
                sprintf('There is not user with id %s', $id)
            );
        }

        if ('POST' === $request->getMethod()) {
            try {
                $data = $request->getParsedBody();

                $user->setEmail($data['email']);
                $user->setPassword($this->auth->hashPassword($data['password']));

                if ([] === $errorMessages = $this->validator->validateModel($user)) {
                    $this->userRepository->update($user);

                    return $this->getRedirectResponse($response, 302, 'user_edit', ['id' => $user->getId()]);
                }
            } catch (EmptyPasswordException $e) {
                $errorMessages['password'] = [$e->getMessage()];
            }
        }

        return $this->twig->render($response, '@SlimSkeleton/user/edit.html.twig',
            array_replace_recursive(
                $this->getGenericTwigVariables($request),
                [
                    'user' => prepareForView($user),
                    'errorMessages' => $errorMessages ?? [],
                ]
            )
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function delete(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        /** @var User $user */
        $user = $this->userRepository->find($request->getAttribute('id'));
        if (null === $user) {
            return $this->getErrorResponse(
                $request,
                $response,
                404,
                'User not found',
                sprintf('There is not user with id %s', $id)
            );
        }

        $authenticatedUser = $this->auth->getAuthenticatedUser($request);

        if ($authenticatedUser->getId() === $user->getId()) {
            return $this->getErrorResponse(
                $request,
                $response,
                403,
                'User not deletable',
                sprintf('You can\'t delete your logged in user with id %s', $id)
            );
        }

        $this->userRepository->delete($user);

        return $this->getRedirectResponse($response, 302, 'user_list');
    }
}
