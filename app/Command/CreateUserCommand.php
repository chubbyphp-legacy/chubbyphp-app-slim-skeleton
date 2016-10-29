<?php

namespace SlimSkeleton\Command;

use Chubbyphp\Security\Authentication\PasswordManager;
use Chubbyphp\Security\Authentication\PasswordManagerInterface;
use SlimSkeleton\Model\User;
use SlimSkeleton\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
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
     * @param PasswordManager $passwordManager
     * @param UserRepository  $userRepository
     */
    public function __construct(PasswordManager $passwordManager, UserRepository $userRepository)
    {
        $this->passwordManager = $passwordManager;
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('slim-skeleton:user:create')
            ->setDescription('Create a new user')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email address of the user.'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password of the user.'),
                new InputArgument('roles', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The roles of the user.'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');

        $user = (new User())
            ->withEmail($email)
            ->withPassword($this->passwordManager->hash($password))
            ->withRoles($roles)
        ;

        $this->userRepository->insert($user);

        $output->writeln(
            sprintf(
                'User with email "%s", password "%s" and roles "%s" created',
                $email,
                $password,
                implode(', ', $roles)
            )
        );
    }
}
