<?php

namespace OpenAPIServer\Api;

use Exception;
use Kinde\KindeSDK\Api\OAuthApi;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use Kinde\KindeSDK\KindeClientSDK;
use Kinde\KindeSDK\Configuration;
use Kinde\KindeSDK\Model\UserProfile;
use Kinde\KindeSDK\Sdk\Enums\GrantType;
use Slim\App;

class Main extends AbstractUserApi
{
    private ?UserProfile $userProfile;

    private KindeClientSDK $kindeClient;

    private Configuration $kindeConfig;

    public function __construct(App $app)
    {
        $container = $app->getContainer();
        $kindeConfig = $container->get('kinde');
        $this->kindeClient = new KindeClientSDK($kindeConfig['HOST'], $kindeConfig['REDIRECT_URL'], $kindeConfig['CLIENT_ID'], $kindeConfig['CLIENT_SECRET'], GrantType::PKCE, $kindeConfig['LOGOUT_REDIRECT_URL']);
        $this->kindeConfig = new Configuration();
        $this->kindeConfig->setHost($kindeConfig['HOST']);
        $this->userProfile = null;
    }
    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $renderer = new PhpRenderer('../templates');
        return $renderer->render($response, "home.php");
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
        try {
            if (!$this->kindeClient->isAuthenticated) {
                $token = $this->kindeClient->getToken();
                if ($token) {
                    return $this->getProfile($response);
                }
            }
            $response = $response->withStatus(302);
            return $response->withHeader('Location', '/');
        } catch (Exception $e) {
            echo 'Exception when calling kindeClient->getToken: ', $e->getMessage(), PHP_EOL;
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

    public function getShortName()
    {
        if ($this->kindeClient->isAuthenticated && !empty($this->userProfile)) {
            return strtoupper(substr($this->userProfile->getFirstName(), 0, 1) . substr($this->userProfile->getLastName(), 0, 1));
        };
        return '';
    }

    public function getFullName()
    {
        if ($this->kindeClient->isAuthenticated && !empty($this->userProfile)) {
            return $this->userProfile->getFirstName() . ' ' . $this->userProfile->getLastName();
        };
        return '';
    }

    private function getProfile(
        ResponseInterface $response
    ) {
        $apiInstance = new OAuthApi($this->kindeConfig);
        try {
            $this->userProfile = $apiInstance->getUser();
            $renderer = new PhpRenderer('../templates');
            return $renderer->render($response, "home.php", ['isAuthenticated' => $this->kindeClient->isAuthenticated, 'shortName' => $this->getShortName(), 'fullName' => $this->getFullName()]);
        } catch (Exception $e) {
            echo 'Exception when calling KindeUserApi->getUserProfile: ', $e->getMessage(), PHP_EOL;
        }
    }
}
