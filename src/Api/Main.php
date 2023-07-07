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
use Slim\Logger;

class Main extends AbstractUserApi
{
    private ?UserProfile $userProfile;

    private KindeClientSDK $kindeClient;

    private Configuration $kindeConfig;

    private Logger $logger;

    public function __construct(App $app)
    {
        $container = $app->getContainer();
        $kindeConfig = $container->get('kinde');
        $this->kindeClient = new KindeClientSDK($kindeConfig['HOST'], $kindeConfig['REDIRECT_URL'], $kindeConfig['CLIENT_ID'], $kindeConfig['CLIENT_SECRET'], GrantType::PKCE, $kindeConfig['LOGOUT_REDIRECT_URL'], "openid profile email offline", [], "http");
        $this->kindeConfig = new Configuration();
        $this->kindeConfig->setHost($kindeConfig['HOST']);
        $this->userProfile = null;

        $this->logger = new Logger();
    }
    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $renderer = new PhpRenderer('../templates');
        if ($this->kindeClient->isAuthenticated) {
            return $this->getProfile($response);
        }
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
        $renderer = new PhpRenderer('../templates');
        $apiInstance = new OAuthApi($this->kindeConfig);
        try {
            $this->userProfile = $apiInstance->getUser();
            $this->logger->info("getPermissions - orgCode " . $this->kindeClient->getPermissions()['orgCode']);
            $this->logger->info("getPermissions - permissions " . join(", ", $this->kindeClient->getPermissions()['permissions']));
            $this->logger->info("getPermission - orgCode " . $this->kindeClient->getPermission('read:profile')['orgCode']);
            $this->logger->info("getPermission - isGranted " . $this->kindeClient->getPermission('read:profile')['isGranted']);
            $this->logger->info("getClaimIss " . $this->kindeClient->getClaim('iss')['value']);

            return $renderer->render($response, "home.php", ['isAuthenticated' => $this->kindeClient->isAuthenticated, 'shortName' => $this->getShortName(), 'fullName' => $this->getFullName()]);
        } catch (Exception $e) {
            $this->logger->error("Exception when calling KindeUserApi->getUserProfile: {$e->getMessage()}");
            return $renderer->render($response, "home.php", ['isAuthenticated' => $this->kindeClient->isAuthenticated, 'shortName' => '', 'fullName' => '']);
        }
    }
}
