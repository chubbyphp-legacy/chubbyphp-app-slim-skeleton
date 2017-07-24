<?php

declare(strict_types=1);

namespace SlimSkeleton\Controller\UserRelatedCrud;

use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\RepositoryInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Chubbyphp\Session\FlashMessage;
use Chubbyphp\Session\SessionInterface;
use Chubbyphp\Validation\ValidatorInterface;
use SlimSkeleton\ErrorHandler\ErrorResponseHandler;
use SlimSkeleton\Model\Traits\OwnedByUserTrait;
use SlimSkeleton\Service\RedirectForPath;
use SlimSkeleton\Service\TwigRender;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class UpdateController
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
     * @var DeserializerInterface
     */
    private $deserializer;

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
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param string                  $type
     * @param AuthenticationInterface $authentication
     * @param AuthorizationInterface  $authorization
     * @param DeserializerInterface   $deserializer
     * @param ErrorResponseHandler    $errorResponseHandler
     * @param RedirectForPath         $redirectForPath
     * @param RepositoryInterface     $repository
     * @param SessionInterface        $session
     * @param TwigRender              $twig
     * @param ValidatorInterface      $validator
     */
    public function __construct(
        string $type,
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        DeserializerInterface $deserializer,
        ErrorResponseHandler $errorResponseHandler,
        RedirectForPath $redirectForPath,
        RepositoryInterface $repository,
        SessionInterface $session,
        TwigRender $twig,
        ValidatorInterface $validator
    ) {
        $this->type = $type;
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->deserializer = $deserializer;
        $this->errorResponseHandler = $errorResponseHandler;
        $this->redirectForPath = $redirectForPath;
        $this->repository = $repository;
        $this->session = $session;
        $this->twig = $twig;
        $this->validator = $validator;
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

        if (!$this->authorization->isGranted($authenticatedUser, sprintf('%s_UPDATE', $typeUpper), $element)) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                403,
                sprintf('%s.error.permissiondenied', $typeLower)
            );
        }

        if ('POST' === $request->getMethod()) {
            /** @var OwnedByUserTrait|ModelInterface $element */
            $element = $this->deserializer->deserializeByObject($request->getParsedBody(), $element);

            $locale = $request->getAttribute('locale');

            if ([] === $errors = $this->validator->validateObject($element)) {
                $this->repository->persist($element);
                $this->session->addFlash(
                    $request,
                    new FlashMessage(FlashMessage::TYPE_SUCCESS, sprintf('%s.flash.update.success', $typeLower))
                );

                return $this->redirectForPath->get($response, 302, sprintf('%s_update', $typeLower), [
                    'locale' => $locale,
                    'id' => $element->getId(),
                ]);
            }

            $errorMessages = $this->twig->getErrorMessages($locale, $errors);

            $this->session->addFlash(
                $request,
                new FlashMessage(FlashMessage::TYPE_DANGER, sprintf('%s.flash.update.failed', $typeLower))
            );
        }

        return $this->twig->render($response, sprintf('@SlimSkeleton/%s/update.html.twig', $typeLower),
            $this->twig->aggregate($request, [
                'errorMessages' => $errorMessages ?? [],
                'element' => prepareForView($element),
            ])
        );
    }
}
