<?php

namespace SlimSkeleton\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Security\Authentication\UserPasswordInterface;
use SlimSkeleton\Model\Traits\IdTrait;
use Ramsey\Uuid\Uuid;

final class User implements UserPasswordInterface, \JsonSerializable
{
    use IdTrait;

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
     * @param string|null $id
     *
     * @return User
     */
    public static function create(string $id = null): User
    {
        $user = new self();
        $user->id = $id ?? (string) Uuid::uuid4();

        return $user;
    }

    private function __construct()
    {
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
    public function setEmail(string $email): User
    {
        $this->email = $email;
        $this->username = $email;

        return $this;
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
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
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
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return User|ModelInterface
     */
    public static function fromPersistence(array $data): ModelInterface
    {
        $user = new self();

        $user->id = $data['id'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->roles = json_decode($data['roles'], true);

        return $user;
    }

    /**
     * @return array
     */
    public function toPersistence(): array
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
}
