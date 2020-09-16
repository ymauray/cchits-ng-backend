<?php

namespace CCHits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Slim middleware to parse a JSON request.
 */
class JsonBodyParserMiddleware implements MiddlewareInterface
{
    /**
     * Parse the request's body if Content-Type is set to json.
     *
     * @param Request        $request the request.
     * @param RequestHandler $handler the handler.
     * 
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $contents = file_get_contents('php://input');
            if (false != $contents) {
                $contents = json_decode($contents, true);
            }
            if (json_last_error() === JSON_ERROR_NONE) {
                $request = $request->withParsedBody(
                    $this->_arrayToObject($contents)
                );
            }
        }

        return $handler->handle($request);
    }

    /**
     * Convert an associative array to an instance of stdClass.
     * 
     * @param array<string, mixed> $array the array to convert
     * 
     * @return \stdClass
     */
    private function _arrayToObject(array $array)
    {
        $obj = new \stdClass();
        return $this->_arrayToObj($array, $obj);
    }

    /**
     * Convert an associative array to an instance of stdClass.
     * 
     * @param array<string, mixed> $array the array to convert
     * @param \stdClass            $obj   the object to store the properties in.
     * 
     * @return \stdClass
     */
    private function _arrayToObj(array $array, \stdClass &$obj): \stdClass 
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $obj->$key = new \stdClass();
                $this->_arrayToObj($value, $obj->$key);
            } else {
                $obj->$key = $value;
            }
        }
        return $obj;
    }
}
