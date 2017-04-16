<?php

namespace App\Containers\User\UI\API\Tests\Functional;

use App\Containers\User\Tests\TestCase;
use Illuminate\Support\Facades\DB;

/**
 * Class ProxyLoginTest
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class ProxyLoginTest extends TestCase
{

    protected $endpoint; // testing multiple endpoints form the tests

    protected $access = [
        'permissions' => '',
        'roles'       => '',
    ];

    public function testClientWebAdminProxyLogin_()
    {
        $endpoint = 'post@v1/clients/web/admin/login';

        // create data to be used for creating the testing user and to be sent with the post request
        $data = [
            'email'    => 'testing@mail.com',
            'password' => 'testingpass',
        ];

        $this->getTestingUser($data);

        $clientId = '100';
        $clientSecret = 'XXp8x4QK7d3J9R7OVRXWrhc19XPRroHTTKIbY8XX';

        // create client
        DB::table('oauth_clients')->insert([
            [
                'id'                     => $clientId,
                'secret'                 => $clientSecret,
                'name'                   => 'Testing',
                'redirect'               => 'http://localhost',
                'password_client'        => '1',
                'personal_access_client' => '0',
                'revoked'                => '0',
            ]
        ]);

        // make the clients credentials available as env variables
        putenv('CLIENT_WEB_ADMIN_ID=' . $clientId);
        putenv('CLIENT_WEB_ADMIN_SECRET=' . $clientSecret);

        $response = $this->endpoint($endpoint)->makeCall($data);

        $response->assertStatus(200);

        $this->assertResponseContainKeyValue([
            'token_type' => 'Bearer',
        ]);

        $this->assertResponseContainKeys(['expires_in', 'access_token', 'refresh_token']);
    }

}
