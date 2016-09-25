<?php

namespace SlimSkeleton\Model;

interface UserInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return string
     */
    public function getPassword(): string;
}
