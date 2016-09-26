<?php

namespace SlimSkeleton\Session;

class FlashMessage implements \JsonSerializable
{
    /**
     * @var string
     */
    private $type;

    const TYPE_PRIMARY = 'p';
    const TYPE_SUCCESS = 's';
    const TYPE_INFO  = 'i';
    const TYPE_WARNING = 'w';
    const TYPE_DANGER = 'd';

    /**
     * @var string
     */
    private $message;

    /**
     * @param string $type
     * @param string $message
     */
    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getBootstrapType(): string
    {
        $reflection = new \ReflectionObject($this);
        foreach ($reflection->getConstants() as $const => $value) {
            if (0 === strpos($const, 'TYPE_')) {
                if ($value === $this->type) {
                    return strtolower(substr($const, 5));
                }
            }
        }

        return 'unknown';
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            't' => $this->type,
            'm' => $this->message,
        ];
    }
}
