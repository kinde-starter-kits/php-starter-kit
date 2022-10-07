<?php

namespace OpenAPIServer\Api;

use Exception;
use Kinde\KindeSDK\Api\UserApi as KindeUserApi;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use Kinde\KindeSDK\KindeClientSDK;
use Kinde\KindeSDK\Configuration;
use Kinde\KindeSDK\Sdk\Enums\GrantType;

class UserApi extends AbstractUserApi
{
    private $kindeClient;

    private $kindeConfig;
    
    public function __construct(\Slim\App $app)
    {
        $container = $app->getContainer();
        $kindeConfig = $container->get('kinde');
        $this->kindeClient = new KindeClientSDK($kindeConfig['HOST'], $kindeConfig['REDIRECT_URL'], $kindeConfig['CLIENT_ID'], $kindeConfig['CLIENT_SECRET'], GrantType::PKCE);
        $this->kindeConfig = new Configuration();
        $this->kindeConfig->setHost($kindeConfig['HOST']);
    }
    public function getUserProfile(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $renderer = new PhpRenderer('../templates');
        return $renderer->render($response, "login.php");
    }

    public function login(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->kindeClient->login();
        $response->getBody()->write('redirecting...');
        return $response;
    }

    public function register(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->kindeClient->register();
        $response->getBody()->write('redirecting...');
        return $response;
    }

    public function callback(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $token = $this->kindeClient->getToken();
        $renderer = new PhpRenderer('../templates');
        return $renderer->render($response, "callback.php");
    }

    public function getProfile(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $apiInstance = new KindeUserApi();
        try {
            $result = $apiInstance->getUserProfile();
            $renderer = new PhpRenderer('../templates');
            return $renderer->render($response, "profile.php", ['content' => $result]);
            print_r($result);
        } catch (Exception $e) {
            echo 'Exception when calling KindeUserApi->getUserProfile: ', $e->getMessage(), PHP_EOL;
        }
    }

    public function logout(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->kindeClient->logout();
        $response->getBody()->write('redirecting...');
        return $response;
    }
}