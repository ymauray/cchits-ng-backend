<?php
require_once 'vendor/autoload.php';

use DI\Container;
use DI\Bridge\Slim\Bridge;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use CCHits\JsonBodyParserMiddleware;
use CCHits\OAuthController;

/*
$c = new Container();

AppFactory::setContainer($c);
*/

$app = Bridge::create();

$app->add(new JsonBodyParserMiddleware());

$app->post('/oauth', OAuthController::class);

$app->run();
