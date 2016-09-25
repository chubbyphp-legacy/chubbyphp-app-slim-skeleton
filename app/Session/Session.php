<?php

namespace SlimSkeleton\Session;

use Psr\Http\Message\ServerRequestInterface as Request;
use PSR7Session\Http\SessionMiddleware;
use PSR7Session\Session\LazySession;

class Session implements SessionInterface
{
    /**
     * @param Request $request
     * @param string  $key
     * @param bool    $remove
     *
     * @return mixed
     */
    public function get(Request $request, string $key, $remove = false)
    {
        $data = json_decode($this->getSession($request)->get($key), true);

        if (true === $remove) {
            $this->remove($request, $key);
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param string  $key
     *
     * @return bool
     */
    public function has(Request $request, string $key): bool
    {
        return $this->getSession($request)->has($key);
    }

    /**
     * @param Request $request
     * @param string  $key
     * @param mixed   $value
     */
    public function set(Request $request, string $key, $value)
    {
        $this->getSession($request)->set($key, json_encode($value));
    }

    /**
     * @param Request $request
     * @param string  $key
     */
    public function remove(Request $request, string $key)
    {
        $this->getSession($request)->remove($key);
    }

    /**
     * @param Request $request
     *
     * @return LazySession
     */
    private function getSession(Request $request): LazySession
    {
        return $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
    }
}
