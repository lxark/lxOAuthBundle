<?php

namespace LX\OAuthBundle\Tests\Client;

use LX\OAuthBundle\Client\ApiClient;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\PreconditionRequiredHttpException;

require_once __DIR__ . '/../../../../../app/tests/AbstractTest.php';

/**
 * Oauth api client unit test
 *
 * @author Alix Chaysinh <alix.chaysinh@gmail.com>
 * @since  2013-10-16
 */
class ApiClientTest extends \AbstractTest
{
    /**
     * Test __call function
     *
     * @author Alix Chaysinh <alix.chaysinh@gmail.com>
     * @since  2013-09-23
     */
    public function test__call()
    {
        $ac      = $this->getApiClient();
        $session = $this->_container->get('session');

        $this->assertNull($this->getSessionAttribute($session, $ac, 'yahoo'));
        $this->assertNull($ac->getYahoo());
        $ac->setYahoo('Yihaa !');
        $this->assertEquals('Yihaa !', $this->getSessionAttribute($session, $ac, 'yahoo'));
        $this->assertEquals('Yihaa !', $ac->getYahoo());

        $ac->setYahoo(null);
        $this->assertNull($this->getSessionAttribute($session, $ac, 'yahoo'));
    }

    /**
     * Test fetchRequestToken function
     *
     * @author Alix Chaysinh <alix.chaysinh@gmail.com>
     * @since  2013-09-24
     */
    public function testFetchRequestToken()
    {
        $ac      = $this->getApiClient();
        $session = $this->_container->get('session');

        $this->assertNull($this->getSessionAttribute($session, $ac, 'request_token'));
        $this->assertNull($this->getSessionAttribute($session, $ac, 'access_token'));

        // Check missing parameters
        $this->catchRequestTokenMissingParameterException('consumer_key', $ac);
        $ac->setConsumerKey('key');

        $this->catchRequestTokenMissingParameterException('consumer_secret', $ac);
        $ac->setConsumerSecret('secret');

        $this->catchRequestTokenMissingParameterException('callback', $ac);
        $ac->setCallback('http://whatever');


        $requestToken = $ac->fetchRequestToken('http://term.ie/oauth/example/request_token.php');

        $this->assertEquals('requestkey', $requestToken);
        $this->assertEquals('requestkey', $ac->getRequestToken());
        $this->assertEquals('requestkey', $this->getSessionAttribute($session, $ac, 'request_token'));
        $this->assertEquals('requestsecret', $ac->getRequestTokenSecret());
        $this->assertEquals('requestsecret', $this->getSessionAttribute($session, $ac, 'request_token_secret'));
    }

    /**
     * Test fetchAccessToken function
     *
     * @author Alix Chaysinh <alix.chaysinh@gmail.com>
     * @since  2013-09-24
     */
    public function testFetchAccessToken()
    {
        $ac      = $this->getApiClient();
        $session = $this->_container->get('session');

        $this->assertNull($this->getSessionAttribute($session, $ac, 'access_token'));
        $this->assertNull($this->getSessionAttribute($session, $ac, 'access_token_secret'));

        // Missing parameters
        $this->catchAccessTokenMissingParameterException('consumer_key', $ac);
        $ac->setConsumerKey('key');

        $this->catchAccessTokenMissingParameterException('consumer_secret', $ac);
        $ac->setConsumerSecret('secret');

        $this->catchAccessTokenMissingParameterException('request_token', $ac);
        $ac->setRequestToken('requestkey');

        $this->catchAccessTokenMissingParameterException('request_token_secret', $ac);
        $ac->setRequestTokenSecret('requestsecret');

        $this->catchAccessTokenMissingParameterException('verifier', $ac);
        $ac->setVerifier('verifier');

        $accessToken = $ac->fetchAccessToken('http://term.ie/oauth/example/access_token.php');

        $this->assertEquals('accesskey', $accessToken);
        $this->assertEquals('accesskey', $ac->getAccessToken());
        $this->assertEquals('accesskey', $this->getSessionAttribute($session, $ac, 'access_token'));
        $this->assertEquals('accesssecret', $ac->getAccessTokenSecret());
        $this->assertEquals('accesssecret', $this->getSessionAttribute($session, $ac, 'access_token_secret'));
    }

    /**
     * Test call function
     *
     * @author Alix Chaysinh <alix.chaysinh@gmail.com>
     * @since  2013-09-24
     */
    public function testCall()
    {
        $ac = $this->getApiClient();

        $ac->setConsumerKey('key');
        $ac->setConsumerSecret('secret');
        $ac->setAccessToken('accesskey');

        try {
            $ac->call('post', 'http://term.ie/oauth/example/echo_api.php');
        } catch (PreconditionRequiredHttpException $e) {;
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $ac->setAccessTokenSecret('accesssecret');

        $response = $ac->call(
            'post',
            'http://term.ie/oauth/example/echo_api.php',
            array(),
            array(
                'method' => 'foo',
                'bar'    => 'baz'
            )
        );

        parse_str($response->getBody(true), $responseParams);

        $this->assertEquals('foo', $responseParams['method']);
        $this->assertEquals('baz', $responseParams['bar']);
    }


    /**
     * Run callable and catch missing parameter exception
     *
     * @param string    $parameter
     * @param ClientApi $ac
     *
     * @return void
     */
    protected function catchRequestTokenMissingParameterException($parameter, $ac)
    {
        try {
            $requestToken = $ac->fetchRequestToken('http://term.ie/oauth/example/request_token.php');
            $this->fail($parameter.' should be missing');
        } catch (PreconditionRequiredHttpException $e) {
            $this->assertRegExp('/Missing.*'.$parameter.'/', $e->getMessage());
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Run callable and catch missing parameter exception
     *
     * @param string   $parameter
     * @param ClientApi $ac
     *
     * @return void
     */
    protected function catchAccessTokenMissingParameterException($parameter, $ac)
    {
        try {
            $accessToken = $ac->fetchAccessToken('http://term.ie/oauth/example/access_token.php');
            $this->fail($parameter.' should be missing');
        } catch (PreconditionRequiredHttpException $e) {
            $this->assertRegExp('/Missing.*'.$parameter.'/', $e->getMessage());
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Return test api client
     *
     * @return ApiClient
     */
    protected function getApiClient()
    {

        return new ApiClient(
            $this->_container->get('session'),
            $this->_container->get('guzzle.client'),
            'test/oauth_api_client'
        );
    }

    /**
     * Get session variable via client operator attributes namespace
     *
     * @param SessionInterface $session Session
     * @param ApiClient        $ac      Api client
     * @param string           $name    Name
     *
     * @return AttributeBag
     */
    protected function getSessionAttribute(SessionInterface $session, ApiClient $ac, $name)
    {

        return $session->get($ac->getAttributesNamespace().'/'.$name);
    }
}
