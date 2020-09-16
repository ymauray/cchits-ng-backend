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
     * @param Request       $request  the request.
     * @param Response      $response the response.
     * @param array<string> $args     the args.
     * 
     * @return Response
     */
    public function __invoke(
        Request $request, Response $response, array $args = array()
    ): Response {
        \session_start();
        $parsedBody = $request->getParsedBody();
        $action = $parsedBody->action; // @phpstan-ignore-line
        $data = $parsedBody->data; // @phpstan-ignore-line

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
