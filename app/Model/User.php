<?php

namespace SlimSkeleton\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\Authentication\UserPasswordInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as RespectValidator;

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
     * User constructor.
     *
     * @param string|null $id
     * @param array       $roles
     */
    public function __construct(string $id = null, array $roles = ['USER'])
    {
        $this->id = $id ?? Uuid::uuid4();
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
     */
    public function setEmail(string $email)
    {
        $this->username = $email;
        $this->email = $email;
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
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
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
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
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
     * @return RespectValidator|null
     */
    public function getModelValidator()
    {
        return RespectValidator::create()->addRule(new UniqueModelRule(['username', 'email']));
    }

    /**
     * @return RespectValidator[]|array
     */
    public function getPropertyValidators(): array
    {
        return [
            'username' => RespectValidator::notBlank()->email(),
            'email' => RespectValidator::notBlank()->email(),
            'password' => RespectValidator::notBlank(),
            'roles' => RespectValidator::notEmpty(),
        ];
    }
}
