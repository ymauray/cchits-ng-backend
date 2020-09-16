<?php

namespace CCHits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Base class for all controllers.
 */
abstract class AbstractController
{
    /**
     * Invocable.
     * 
     * @param $request  the request.
     * @param $response the response.
     * @param $args     the args.
     * 
     * @return an instance of Response.
     */
    public function __invoke(
        Request $request, Response $response, array $args = array()
    ): Response {
        \session_start();
        $parsedBody = $request->getParsedBody();
        $action = $parsedBody->action;
        $data = $parsedBody->data;

        $class = new \ReflectionClass($this);

        try {
            $method = $class->getMethod($action);
        }
        catch (\ReflectionException $e) {
            // Do nothing.
        }

        if (isset($method)) {
            $response = $method->invoke($this, $request, $response, $data, $args);
            return $response;
        } else {
            return $response;
        }
    }
}