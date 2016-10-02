<?php

namespace SlimSkeleton\Controller;

use Chubbyphp\Validation\ValidatorInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Router;
use Slim\Views\Twig;
use SlimSkeleton\Security\AuthInterface;
use SlimSkeleton\Security\Exception\EmptyPasswordException;
use SlimSkeleton\Controller\Traits\RenderErrorTrait;
use SlimSkeleton\Controller\Traits\RedirectForPathTrait;
use SlimSkeleton\Controller\Traits\TwigDataTrait;
use SlimSkeleton\Model\User;
use SlimSkeleton\Repository\UserRepositoryInterface;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;

final class UserController
{
    use RedirectForPathTrait;
    use RenderErrorTrait;
    use TwigDataTrait;

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
        $this->auth = $auth;
        $this->router = $router;
        $this->session = $session;
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

        return $this->twig->render($response, '@SlimSkeleton/user/list.html.twig',
            $this->getTwigData($request, [
                'users' => prepareForView($users),
            ])
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
            return $this->renderError($request, $response, 404);
        }

        return $this->twig->render($response, '@SlimSkeleton/user/view.html.twig',
            $this->getTwigData($request, [
                'user' => prepareForView($user),
            ])
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
            $data = $request->getParsedBody();

            $user->setEmail($data['email']);

            try {
                $user->setPassword($this->auth->hashPassword($data['password']));
            } catch (EmptyPasswordException $e) {
            }

            if ([] === $errorMessages = $this->validator->validateModel($user)) {
                $this->userRepository->insert($user);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'user.flash.create.success')
                );

                return $this->getRedirectForPath($response, 302, 'user_edit', [
                    'locale' => $request->getAttribute('locale'),
                    'id' => $user->getId(),
                ]);
            }

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, 'user.flash.create.failed')
            );
        }

        return $this->twig->render($response, '@SlimSkeleton/user/create.html.twig',
            $this->getTwigData($request, [
                'errorMessages' => $errorMessages ?? [],
                'user' => prepareForView($user),
            ])
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
            return $this->renderError($request, $response, 404);
        }

        if ('POST' === $request->getMethod()) {
            $data = $request->getParsedBody();

            $user->setEmail($data['email']);
            if ($data['password']) {
                $user->setPassword($this->auth->hashPassword($data['password']));
            }

            if ([] === $errorMessages = $this->validator->validateModel($user)) {
                $this->userRepository->update($user);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'user.flash.edit.success')
                );

                return $this->getRedirectForPath($response, 302, 'user_edit', [
                    'locale' => $request->getAttribute('locale'),
                    'id' => $user->getId(),
                ]);
            }

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, 'user.flash.edit.failed')
            );
        }

        return $this->twig->render($response, '@SlimSkeleton/user/edit.html.twig',
            $this->getTwigData($request, [
                'errorMessages' => $errorMessages ?? [],
                'user' => prepareForView($user),
            ])
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
            return $this->renderError($request, $response, 404);
        }

        $authenticatedUser = $this->auth->getAuthenticatedUser($request);

        if ($authenticatedUser->getId() === $user->getId()) {
            return $this->renderError($request, $response, 403);
        }

        $this->userRepository->delete($user);

        return $this->getRedirectForPath($response, 302, 'user_list', ['locale' => $request->getAttribute('locale')]);
    }
}
