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
     * @param $request the request.
     * @param $handler the handler.
     * 
     * @return a Response object.
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true);
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
     * @param $array the array to convert
     * 
     * @return \stdClass;
     */
    private function _arrayToObject(array $array)
    {
        $obj = new \stdClass();
        return $this->_arrayToObj($array, $obj);
    }

    /**
     * Convert an associative array to an instance of stdClass.
     * 
     * @param $array the array to convert
     * @param $obj   the object to store the properties in.
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
