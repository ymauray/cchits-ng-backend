<?php

namespace CCHits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Auth_OpenID_FileStore;

use CCHits\Services\OpenIDService;

/**
 * Controller for the /oauth endpoint
 */
class OAuthController extends AbstractController
{
    private OpenIDService $_openIdService;

    /**
     * Constructor.
     * 
     * @param $openIdService The OpenIDService injected by the DI container.
     */
    public function __construct(OpenIDService $openIdService)
    {
        $this->_openIdService = $openIdService;
    }

    /**
     * Get the OAuth service endpoint.
     * 
     * @param $request  the request
     * @param $response the response
     * @param $data     the data
     * @param $args     the args
     * 
     * @return Response
     */
    public function getEndpoint(
        Request $request, Response $response, \stdClass $data, array $args
    ): Response {

        if (false === \session_id()) {
            \session_start();
        }

        switch ($data->provider) {
        case 'launchpad':
            $endpoint = 'https://login.launchpad.net/+openid';
            break;
        case 'google':
            $endpoint = 'https://www.google.ch';
            break;
        case 'yahoo':
            $endpoint = 'https://www.yahoo.com';
            break;
        default:
            $endpoint = 'https://www.arcantel.ch';
            break;
        }

        $url = $auth = $this->_openIdService->begin($endpoint);

        $response->getBody()->write(json_encode(['url' => $url]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}