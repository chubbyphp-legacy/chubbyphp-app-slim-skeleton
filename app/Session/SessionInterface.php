<?php

namespace SlimSkeleton\Session;

use Psr\Http\Message\ServerRequestInterface as Request;

interface SessionInterface
{
    /**
     * @param Request $request
     * @param string  $key
     * @param bool    $remove
     *
     * @return mixed
     */
    public function get(Request $request, string $key, $remove = false);

    /**
     * @param Request $request
     * @param string  $key
     *
     * @return bool
     */
    public function has(Request $request, string $key): bool;

    /**
     * @param Request $request
     * @param string  $key
     * @param string  $value
     */
    public function set(Request $request, string $key, $value);

    /**
     * @param Request $request
     * @param string  $key
     */
    public function remove(Request $request, string $key);
}
