<?php

declare(strict_types=1);

namespace SlimSkeleton\Deserialization;

use Chubbyphp\Deserialization\Deserializer\PropertyDeserializerCallback;
use Chubbyphp\Deserialization\Mapping\ObjectMappingInterface;
use Chubbyphp\Deserialization\Mapping\PropertyMapping;
use Chubbyphp\Deserialization\Mapping\PropertyMappingInterface;
use Chubbyphp\Security\Authentication\PasswordManagerInterface;
use Chubbyphp\Security\Authorization\RoleHierarchyResolverInterface;
use SlimSkeleton\Model\User;

final class UserMapping implements ObjectMappingInterface
{
    /**
     * @var PasswordManagerInterface
     */
    private $passwordManager;

    /**
     * @var RoleHierarchyResolverInterface
     */
    private $roleHierarchyResolver;

    /**
     * @param PasswordManagerInterface       $passwordManager
     * @param RoleHierarchyResolverInterface $roleHierarchyResolver
     */
    public function __construct(
        PasswordManagerInterface $passwordManager,
        RoleHierarchyResolverInterface $roleHierarchyResolver
    ) {
        $this->passwordManager = $passwordManager;
        $this->roleHierarchyResolver = $roleHierarchyResolver;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return User::class;
    }

    /**
     * @return callable
     */
    public function getFactory(): callable
    {
        return [User::class, 'create'];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping(
                'email',
                new PropertyDeserializerCallback(
                    function ($path, $newEmail, $oldEmail, $user) {
                        $this->userOrException($user);

                        $reflectionProperty = new \ReflectionProperty(get_class($user), 'username');
                        $reflectionProperty->setAccessible(true);
                        $reflectionProperty->setValue($user, $newEmail);

                        return $newEmail;
                    }
                )
            ),
            new PropertyMapping(
                'password',
                new PropertyDeserializerCallback(
                    function ($path, $newPlainPassword, $oldPassword) {
                        if (!$newPlainPassword) {
                            return $oldPassword;
                        }

                        return $this->passwordManager->hash($newPlainPassword);
                    }
                )
            ),
            new PropertyMapping(
                'roles',
                new PropertyDeserializerCallback(
                    function ($path, $serializedRoles) {
                        $possibleRoles = $this->roleHierarchyResolver->resolve(['ADMIN']);

                        foreach ($serializedRoles as $i => $role) {
                            if (!in_array($role, $possibleRoles, true)) {
                                unset($serializedRoles[$i]);
                            }
                        }

                        return $serializedRoles;
                    }
                )
            ),
        ];
    }

    /**
     * @param User $user
     */
    private function userOrException($user)
    {
        if (!$user instanceof User) {
            throw new \RuntimeException(
                sprintf(
                    'Object needs to implement: %s, given: %s',
                    User::class,
                    is_object($user) ? get_class($user) : gettype($user)
                )
            );
        }
    }
}
