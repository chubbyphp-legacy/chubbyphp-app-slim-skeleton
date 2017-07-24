<?php

declare(strict_types=1);

namespace SlimSkeleton\Controller\UserRelatedCrud;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\RepositoryInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use SlimSkeleton\ErrorHandler\ErrorResponseHandler;
use SlimSkeleton\Model\Traits\OwnedByUserTrait;
use SlimSkeleton\Service\RedirectForPath;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class DeleteController
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var ErrorResponseHandler
     */
    private $errorResponseHandler;

    /**
     * @var RedirectForPath
     */
    private $redirectForPath;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param string                  $type
     * @param AuthenticationInterface $authentication
     * @param AuthorizationInterface  $authorization
     * @param ErrorResponseHandler    $errorResponseHandler
     * @param RedirectForPath         $redirectForPath
     * @param RepositoryInterface     $repository
     */
    public function __construct(
        string $type,
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        ErrorResponseHandler $errorResponseHandler,
        RedirectForPath $redirectForPath,
        RepositoryInterface $repository
    ) {
        $this->type = $type;
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->errorResponseHandler = $errorResponseHandler;
        $this->redirectForPath = $redirectForPath;
        $this->repository = $repository;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $typeLower = strtolower($this->type);
        $typeUpper = strtoupper($this->type);

        $id = $request->getAttribute('id');

        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        /** @var OwnedByUserTrait|ModelInterface $element */
        $element = $this->repository->findOneBy(['id' => $id, 'userId' => $authenticatedUser->getId()]);
        if (null === $element) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                404,
                sprintf('%s.error.notfound', $typeLower)
            );
        }

        if (!$this->authorization->isGranted($authenticatedUser, sprintf('%s_DELETE', $typeUpper), $element)) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                403,
                sprintf('%s.error.permissiondenied', $typeLower)
            );
        }

        $this->repository->remove($element);

        return $this->redirectForPath->get(
            $response, 302, sprintf('%s_list', $typeLower), ['locale' => $request->getAttribute('locale')]
        );
    }
}
