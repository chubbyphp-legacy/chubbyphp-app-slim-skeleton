<?php

namespace SlimSkeleton\Command;

use Chubbyphp\Security\Authentication\PasswordManagerInterface;
use Chubbyphp\Validation\ValidatorInterface;
use SlimSkeleton\Model\User;
use SlimSkeleton\Repository\UserRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateUserCommand
{
    /**
     * @var PasswordManagerInterface
     */
    private $passwordManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param PasswordManagerInterface $passwordManager
     * @param UserRepository           $userRepository
     * @param ValidatorInterface       $validator
     */
    public function __construct(
        PasswordManagerInterface $passwordManager,
        UserRepository $userRepository,
        ValidatorInterface $validator
    ) {
        $this->passwordManager = $passwordManager;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    public function __invoke(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');

        $user = (new User())
            ->withEmail($email)
            ->withPassword($this->passwordManager->hash($password))
            ->withRoles($roles)
        ;

        $errors = $this->validator->validateModel($user);
        if ([] !== $errors) {
            foreach ($errors as $field => $errorsPerField) {
                foreach ($errorsPerField as $errorPerField) {
                    $output->writeln(sprintf('<error>%s: %s</error>', $field, $errorPerField));
                }
            }

            return 1;
        }

        $this->userRepository->insert($user);

        $output->writeln(
            sprintf(
                '<info>User with email "%s", password "%s" and roles "%s" created</info>',
                $email,
                $password,
                implode(', ', $roles)
            )
        );
    }
}
