<?php

namespace SlimSkeleton\Model;

final class User implements ModelInterface, UserInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $id
     * @param string $email
     * @param string $password
     */
    public function __construct(string $id, string $email, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
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
     * @param array $data
     * @return User|ModelInterface
     */
    public static function fromRow(array $data): ModelInterface
    {
        $user = new self($data['id'], $data['email'], $data['password']);

        return $user;
    }

    /**
     * @return array
     */
    public function toRow(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email
        ];
    }
}
