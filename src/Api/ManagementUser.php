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

class ManagementUser extends AbstractUserApi
{
    private KindeClientSDK $kindeClient;

    private Configuration $kindeConfig;

    public function __construct(App $app)
    {
        $container = $app->getContainer();
        $kindeConfig = $container->get('kinde');
        $this->kindeClient = new KindeClientSDK($kindeConfig['HOST'], $kindeConfig['REDIRECT_URL'], $kindeConfig['CLIENT_ID'], $kindeConfig['CLIENT_SECRET'], GrantType::clientCredentials, $kindeConfig['LOGOUT_REDIRECT_URL'], "", [
            'audience' => $kindeConfig['HOST'] . '/api'
        ]);
        $this->kindeConfig = new Configuration();
        $this->kindeConfig->setHost($kindeConfig['HOST']);
    }
    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $renderer = new PhpRenderer('../templates');
        return $renderer->render($response, "create-user.php");
    }

    public function save(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $body = $request->getParsedBody();

        $create_user_request = new CreateUserRequest([
            'profile' => [
                'given_name' => $body['given_name'],
                'family_name' => $body['family_name']
            ],
            'identities' => [
                [
                    'type' => 'email',
                    'details' => [
                        'email' => $body['email']
                    ]
                ]
            ]
        ]);

        $token = $this->kindeClient->login();
        $this->kindeConfig->setAccessToken($token->access_token);

        $apiInstance = new UsersApi($this->kindeConfig);
        $result = $apiInstance->createUser($create_user_request);

        $renderer = new PhpRenderer('../templates');
        return $renderer->render($response, "create-user.php", ['result' => $result]);
    }
}
