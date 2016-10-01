<?php

namespace SlimSkeleton\Model;

use Chubbyphp\Model\ModelInterface;
use Chubbyphp\Validation\Rules\UniqueModelRule;
use Chubbyphp\Validation\ValidatableModelInterface;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as RespectValidator;

final class User implements \JsonSerializable, ModelInterface, UserInterface, ValidatableModelInterface
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
     * @param string|null $id
     */
    public function __construct(string $id = null)
    {
        $this->id = $id ?? Uuid::uuid4();
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
     *
     * @return User|ModelInterface
     */
    public static function fromRow(array $data): ModelInterface
    {
        $user = new self($data['id']);

        $user->setEmail($data['email']);
        $user->setPassword($data['password']);

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
            'password' => $this->password,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
        ];
    }

    /**
     * @return RespectValidator|null
     */
    public function getModelValidator()
    {
        return RespectValidator::create()->addRule(new UniqueModelRule(['email']));
    }

    /**
     * @return RespectValidator[]|array
     */
    public function getPropertyValidators(): array
    {
        return [
            'email' => RespectValidator::notBlank()->email(),
            'password' => RespectValidator::notBlank(),
        ];
    }
}
