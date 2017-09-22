<?php


use Freddiegar\Authentication\Exceptions\AuthenticationException;
use Freddiegar\Authentication\Services\Authentication;

class AuthenticationFunctionalityTest extends BaseTestCase
{
    public function testAuthenticationLoginNullError()
    {
        $isOk = false;
        try {
            new Authentication(null, 'tranKey');
        } catch (AuthenticationException $exception) {
            $isOk = true;
        } finally {
            $this->assertEquals(true, $isOk);
        }
    }

    public function testAuthenticationOK()
    {
        $authentication = new Authentication('login', 'tranKey', [
            'created' => '2016-10-26T21:37:00+00:00',
            'nonce' => 'ifYEPnAcJbpDVR1t',
        ]);

        $response = $authentication->toArray();

        $this->assertEquals('login', $response['username'], 'Username matches');
        $this->assertEquals('ENMIkOGbz9wUj7gOa/ptcw+bMnE=', $response['password'], 'Password matches');
        $this->assertEquals('ifYEPnAcJbpDVR1t', $response['nonce'], 'Nonce matches');
        $this->assertEquals('2016-10-26T21:37:00+00:00', $response['created'], 'Created matches');
    }
}