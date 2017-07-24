<?php

namespace SlimSkeleton\Model\Traits;

trait IdTrait
{
    /**
     * @var string
     */
    private $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
