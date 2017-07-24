<?php

namespace SlimSkeleton\Model\Traits;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Model\Reference\ModelReferenceInterface;
use Chubbyphp\Security\UserInterface;
use SlimSkeleton\Model\User;

trait OwnedByUserTrait
{
    /**
     * @var ModelReferenceInterface
     */
    private $user;

    /**
     * @param UserInterface $user
     *
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user->setModel($user);

        return $this;
    }

    /**
     * @return User|ModelInterface|null
     */
    public function getUser()
    {
        return $this->user->getModel();
    }

    /**
     * @return string
     */
    public function getOwnedByUserId(): string
    {
        return $this->user->getId();
    }
}
