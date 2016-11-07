<?php

namespace SlimSkeleton\Controller;

use Chubbyphp\ErrorHandler\HttpException;
use Chubbyphp\Model\RepositoryInterface;
use Chubbyphp\Security\Authentication\Exception\EmptyPasswordException;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authentication\PasswordManagerInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Chubbyphp\Security\Authorization\RoleHierarchyResolverInterface;
use Chubbyphp\Validation\ValidatorInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SlimSkeleton\Model\User;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;
use SlimSkeleton\Service\RedirectForPath;
use SlimSkeleton\Service\TemplateData;
use SlimSkeleton\Service\TwigRender;

final class UserController
{
    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var PasswordManagerInterface
     */
    private $passwordManager;

    /**
     * @var RedirectForPath
     */
    private $redirectForPath;

    /**
     * @var RoleHierarchyResolverInterface
     */
    private $roleHierarchyResolver;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TemplateData
     */
    private $templateData;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @var RepositoryInterface
     */
    private $userRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param AuthenticationInterface        $authentication
     * @param AuthorizationInterface         $authorization
     * @param PasswordManagerInterface       $passwordManager
     * @param RedirectForPath                $redirectForPath
     * @param RoleHierarchyResolverInterface $roleHierarchyResolver
     * @param SessionInterface               $session
     * @param TemplateData                   $templateData
     * @param TwigRender                     $twig
     * @param RepositoryInterface            $userRepository
     * @param ValidatorInterface             $validator
     */
    public function __construct(
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        PasswordManagerInterface $passwordManager,
        RedirectForPath $redirectForPath,
        RoleHierarchyResolverInterface $roleHierarchyResolver,
        SessionInterface $session,
        TemplateData $templateData,
        TwigRender $twig,
        RepositoryInterface
        $userRepository,
        ValidatorInterface $validator
    ) {
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->passwordManager = $passwordManager;
        $this->redirectForPath = $redirectForPath;
        $this->roleHierarchyResolver = $roleHierarchyResolver;
        $this->session = $session;
        $this->templateData = $templateData;
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
        if (!$this->authorization->isGranted($this->authentication->getAuthenticatedUser($request), 'ADMIN')) {
            throw HttpException::create($request, $response, 403, 'user.error.permissiondenied');
        }

        $users = $this->userRepository->findBy();

        return $this->twig->render($response, '@SlimSkeleton/user/list.html.twig',
            $this->templateData->aggregate($request, [
                'users' => prepareForView($users),
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function view(Request $request, Response $response)
    {
        if (!$this->authorization->isGranted($this->authentication->getAuthenticatedUser($request), 'ADMIN')) {
            throw HttpException::create($request, $response, 403, 'user.error.permissiondenied');
        }

        $id = $request->getAttribute('id');

        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw HttpException::create($request, $response, 404, 'user.error.notfound');
        }

        return $this->twig->render($response, '@SlimSkeleton/user/view.html.twig',
            $this->templateData->aggregate($request, [
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
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'ADMIN')) {
            throw HttpException::create($request, $response, 403, 'user.error.permissiondenied');
        }

        $possibleRoles = $this->roleHierarchyResolver->resolve(['ADMIN']);

        $user = new User();

        if ('POST' === $request->getMethod()) {
            $data = $request->getParsedBody();

            $user = $user->withEmail($data['email'] ?? '');

            if (isset($data['roles'])) {
                $user = $user->withRoles($this->getWishedRoles($data['roles'], $possibleRoles));
            }

            try {
                $user = $user->withPassword($this->passwordManager->hash($data['password'] ?? ''));
            } catch (EmptyPasswordException $e) {
            }

            if ([] === $errorMessages = $this->validator->validateModel($user)) {
                $this->userRepository->insert($user);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'user.flash.create.success')
                );

                return $this->redirectForPath->get($response, 302, 'user_edit', [
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
            $this->templateData->aggregate($request, [
                'errorMessages' => $errorMessages ?? [],
                'user' => prepareForView($user),
                'possibleRoles' => array_combine($possibleRoles, $possibleRoles),
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function edit(Request $request, Response $response)
    {
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'ADMIN')) {
            throw HttpException::create($request, $response, 403, 'user.error.permissiondenied');
        }

        $possibleRoles = $this->roleHierarchyResolver->resolve(['ADMIN']);

        $id = $request->getAttribute('id');

        /** @var User $user */
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw HttpException::create($request, $response, 404, 'user.error.notfound');
        }

        if ('POST' === $request->getMethod()) {
            $data = $request->getParsedBody();

            $user = $user->withEmail($data['email'] ?? '');

            if (isset($data['roles']) && $authenticatedUser->getId() !== $user->getId()) {
                $user = $user->withRoles($this->getWishedRoles($data['roles'], $possibleRoles));
            }

            if ($data['password']) {
                $user = $user->withPassword($this->passwordManager->hash($data['password']));
            }

            if ([] === $errorMessages = $this->validator->validateModel($user)) {
                $user = $user->withUpdatedAt(new \DateTime());

                $this->userRepository->update($user);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, 'user.flash.edit.success')
                );

                return $this->redirectForPath->get($response, 302, 'user_edit', [
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
            $this->templateData->aggregate($request, [
                'errorMessages' => $errorMessages ?? [],
                'user' => prepareForView($user),
                'possibleRoles' => array_combine($possibleRoles, $possibleRoles),
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function delete(Request $request, Response $response)
    {
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'ADMIN')) {
            throw HttpException::create($request, $response, 403, 'user.error.permissiondenied');
        }

        $id = $request->getAttribute('id');

        /** @var User $user */
        $user = $this->userRepository->find($id);
        if (null === $user) {
            throw HttpException::create($request, $response, 404, 'user.error.notfound');
        }

        if ($authenticatedUser->getId() === $user->getId()) {
            throw HttpException::create($request, $response, 403, 'user.error.cantdeleteyourself');
        }

        $this->userRepository->delete($user);

        return $this->redirectForPath->get($response, 302, 'user_list', ['locale' => $request->getAttribute('locale')]);
    }

    /**
     * @param array $roles
     *
     * @return array
     */
    private function getWishedRoles(array $roles, $possibleRoles): array
    {
        foreach ($roles as $i => $role) {
            if (!in_array($role, $possibleRoles, true)) {
                unset($roles[$i]);
            }
        }

        return $roles;
    }
}
