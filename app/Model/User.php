<?php

namespace SlimSkeleton\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\Authentication\UserPasswordInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;

final class User implements \JsonSerializable, UserPasswordInterface, ValidatableModelInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var array|null
     */
    private $_change;

    /**
     * User constructor.
     *
     * @param string|null $id
     * @param array       $roles
     */
    public function __construct(string $id = null, array $roles = ['USER'])
    {
        $this->id = $id ?? (string) Uuid::uuid4();
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function withEmail(string $email): User
    {
        $user = $this->cloneWithChange();
        $user->email = $email;
        $user->username = $email;

        return $user;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function withPassword(string $password): User
    {
        $user = $this->cloneWithChange();
        $user->password = $password;

        return $user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function withRoles(array $roles): User
    {
        $user = $this->cloneWithChange();
        $user->roles = $roles;

        return $user;
    }

    /**
     * @return User
     */
    private function cloneWithChange(): User
    {
        $user = clone $this;
        $user->_change = ['method' => __METHOD__, 'arguments' => func_get_args(), 'old' => $this];

        return $user;
    }

    /**
     * @param array $data
     *
     * @return User|ModelInterface
     */
    public static function fromRow(array $data): ModelInterface
    {
        $user = new self($data['id']);

        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->roles = json_decode($data['roles'], true);

        return $user;
    }

    /**
     * @return array
     */
    public function toRow(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => json_encode($this->roles),
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'roles' => $this->roles,
        ];
    }

    /**
     * @return v|null
     */
    public function getModelValidator()
    {
        return v::create()->addRule(new UniqueModelRule(['username', 'email']));
    }

    /**
     * @return v[]|array
     */
    public function getPropertyValidators(): array
    {
        return [
            'username' => v::notBlank()->email(),
            'email' => v::notBlank()->email(),
            'password' => v::notBlank(),
            'roles' => v::notEmpty(),
        ];
    }
}
