<?php

declare(strict_types=1);

namespace SlimSkeleton\Validation;

use Chubbyphp\Model\ResolverInterface;
use Chubbyphp\Validation\Constraint\ConstraintInterface;
use Chubbyphp\Validation\Constraint\EmailConstraint;
use Chubbyphp\Validation\Constraint\NotBlankConstraint;
use Chubbyphp\Validation\Constraint\NotNullConstraint;
use Chubbyphp\Validation\Mapping\PropertyMapping;
use Chubbyphp\Validation\Mapping\PropertyMappingInterface;
use Chubbyphp\Validation\Mapping\ObjectMappingInterface;
use Chubbyphp\ValidationModel\Constraint\UniqueModelConstraint;
use SlimSkeleton\Model\User;

final class UserMapping implements ObjectMappingInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return User::class;
    }

    /**
     * @return ConstraintInterface[]
     */
    public function getConstraints(): array
    {
        return [new UniqueModelConstraint($this->resolver, ['username', 'email'])];
    }

    /**
     * @return PropertyMappingInterface[]
     */
    public function getPropertyMappings(): array
    {
        return [
            new PropertyMapping('username', [new NotNullConstraint(), new EmailConstraint()]),
            new PropertyMapping('email', [new NotNullConstraint(), new EmailConstraint()]),
            new PropertyMapping('password', [new NotNullConstraint(), new NotBlankConstraint()]),
            new PropertyMapping('roles', [new NotBlankConstraint()]),
        ];
    }
}
