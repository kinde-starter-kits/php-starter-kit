<?php

namespace OpenAPIServer\Api;

use Kinde\KindeSDK\Api\UsersApi;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use Kinde\KindeSDK\KindeClientSDK;
use Kinde\KindeSDK\Configuration;
use Kinde\KindeSDK\Model\CreateUserRequest;
use Kinde\KindeSDK\Sdk\Enums\GrantType;
use Slim\App;

class Playground extends AbstractUserApi
{
    private KindeClientSDK $kindeClient;

    private Configuration $kindeConfig;

    public function __construct(App $app)
    {
        $container = $app->getContainer();
        $kindeConfig = $container->get('kinde');
        $this->kindeClient = new KindeClientSDK($kindeConfig['HOST'], $kindeConfig['REDIRECT_URL'], $kindeConfig['CLIENT_ID'], $kindeConfig['CLIENT_SECRET'], GrantType::PKCE, $kindeConfig['LOGOUT_REDIRECT_URL'], "", [
            'audience' => $kindeConfig['HOST'] . '/api'
        ]);
        $this->kindeConfig = new Configuration();
        $this->kindeConfig->setHost($kindeConfig['HOST']);
    }
    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        if (!$this->kindeClient->isAuthenticated) {
            $response = $response->withStatus(302);
            return $response->withHeader('Location', '/');
        }
        $renderer = new PhpRenderer('../templates');
        return $renderer->render($response, "playground.php", ['client' => $this->kindeClient]);
    }

    public function playground(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $result = "";
        try {
            $body = $request->getParsedBody();

            $type = $body['type'];
            $flagType = $body['flag_type'];
            $value = $body['value'];
            $default = $body['default'];
            switch ($type) {
                case 'flag':
                    $result = $this->kindeClient->getFlag($value, ['defaultValue' => $default], $flagType)['value'];
                    break;
                case 'flag_boolean':
                    $result = $this->kindeClient->getBooleanFlag($value,  $default == 'true' ? 1 : 0)['value'];
                    $result = $result == 1 ? 'true' : 'false';
                    break;
                case 'flag_integer':
                    $result = $this->kindeClient->getIntegerFlag($value, (int) $default)['value'];
                    break;
                case 'flag_string':
                    $result = $this->kindeClient->getStringFlag($value, $default)['value'];
                    break;
                default:
                    break;
            }
        } catch (\Throwable $th) {
            //throw $th;
            $result = $th->getMessage();
        }

        $response->withStatus(200)->getBody()->write((string) $result);
        return $response;
    }
}
