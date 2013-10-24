<?php

namespace LX\OAuthBundle\Client;

use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Guzzle\Service\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\PreconditionRequiredHttpException;

/**
 * This class handles oauth request
 * - symfony session
 * - Guzzle http client
 * 
 * @author Alix Chaysinh <alix.chaysinh@gmail.com>
 * @since  2013-09-23
 */
class ApiClient
{
    /**
     * @var SessionInterface $session user session
     */
    protected $session;

    /**
     * @var string $attributesNamespace namespace of variables in session
     */
    protected $attributesNamespace;

    /**
     * @var Client $httpClient http client (Guzzle)
     */
    protected $httpClient;


    public function __construct(SessionInterface $session, Client $httpClient, $attributesNamespace = 'oauth/client_operator')
    {
        // Session
        $this->session = $session;
        $this->attributesNamespace = $attributesNamespace;

        // Http client (guzzle)
        $this->httpClient = $httpClient;
    }

    /**
     * Get variable in session
     *
     * @param string $name         Session variable name to append to prefix
     * @param mixed  $defaultValue Default value
     *
     * @return mixed
     */
    public function get($name, $defaultValue = null)
    {

        return $this->session->get($this->attributesNamespace.'/'.$name, $defaultValue);
    }

    /**
     * Set variable in session
     *
     * @param string $name  Session variable name to append to prefix
     * @param mixed  $value Session variable value
     *
     * @return void
     */
    public function set($name, $value)
    {
        $this->session->set($this->attributesNamespace.'/'.$name, $value);
    }

    /**
     * Magic method for getter/setter
     *
     * @param strong $method Method name
     * @param array $args   Method arguments
     *
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($method, $args)
    {
        $threeFirst = substr($method, 0, 3);

        if ('get' === $threeFirst) {

            // Getter ?
            $snakedProperty = $this->httpClient->getInflector()->snake(substr($method, 3));

            return $this->get($snakedProperty);

        } elseif ('set' === $threeFirst) {

            // Setter ?
            $snakedProperty = $this->httpClient->getInflector()->snake(substr($method, 3));

            return $this->set($snakedProperty, $args[0]);

        }

        throw new \BadMethodCallException(sprintf('Unknown method %s::%s', get_class($this), $method));
    }

    /**
     * Return attributes namespace
     *
     * @return string
     */
    public function getAttributesNamespace()
    {

        return $this->attributesNamespace;
    }


    /**
     * The oauth Api client is ready to call apis if access token is received
     *
     * @return bool
     */
    public function isReady()
    {

        return (bool) $this->getAccessToken();
    }


    /**
     * Clear attribute in namespace
     *
     * @return void
     */
    public function clear()
    {
        $this->session->remove($this->getAttributesNamespace());
    }

    /**
     * Get a request token via http request to webservice
     *
     * @param string $url Url
     *
     * @return string request token
     */
    public function fetchRequestToken($url)
    {
        // Check params
        $this->checkParams(array('consumer_key', 'consumer_secret', 'callback'));

        // Send oauth request
        $response = $this->sendOAuthRequest('post', $url, array(
            'consumer_key'    => $this->get('consumer_key'),
            'consumer_secret' => $this->get('consumer_secret'),
            'callback'        => $this->get('callback'),
        ));
        parse_str($response->getBody(true), $requestToken);

        if (isset($requestToken['oauth_token'])) {
            $this->set('request_token', $requestToken['oauth_token']);
        }

        if (isset($requestToken['oauth_token_secret'])) {
            $this->set('request_token_secret', $requestToken['oauth_token_secret']);
        }

        return $this->getRequestToken();
    }


    /**
     * Extract and store verifier from request
     *
     * @param Request $request request
     *
     * @return bool
     */
    public function receiveVerifier(Request $request)
    {
        $storeVerifier = !$this->get('verifier') && $request->query->get('oauth_verifier');

        if ($storeVerifier) {
            $this->set('verifier', $request->query->get('oauth_verifier'));
        }

        return $storeVerifier;
    }

    /**
     * Get a request token via http request to webservice
     *
     * @param string $url Url
     *
     * @return string access token
     */
    public function fetchAccessToken($url)
    {
        // Check params
        $this->checkParams(array('consumer_key', 'consumer_secret', 'request_token', 'request_token_secret', 'verifier'));

        // Send oauth request
        $response = $this->sendOAuthRequest('post', $url, array(
            'consumer_key'    => $this->get('consumer_key'),
            'consumer_secret' => $this->get('consumer_secret'),
            'token'           => $this->get('request_token'),
            'token_secret'    => $this->get('request_token_secret'),
            'verifier' => $this->get('verifier')
        ));

        // Parse response and store tokens
        parse_str($response->getBody(true), $accessToken);

        if (isset($accessToken['oauth_token'])) {
            $this->set('access_token', $accessToken['oauth_token']);
        }

        if (isset($accessToken['oauth_token_secret'])) {
            $this->set('access_token_secret', $accessToken['oauth_token_secret']);
        }

        return $this->getAccessToken();
    }


    /**
     * Call a webservice, returns the response as a string
     *
     * @param string $method      Http method
     * @param string $url         Url
     * @param array  $oauthParams Oauth request parameters
     * @param array  $params      Request parameters
     * @param array  $headers     Request parameters
     * @param array  $options     Request parameters
     *
     * @throws PreconditionRequiredHttpException
     * @return Response
     */
    public function call($method, $url, array $oauthParams = array(), $params = array(), $headers = null, array $options = array())
    {
        // Check Api client
        if (false === $this->isReady()) {
            throw new PreconditionRequiredHttpException('Api client has no access token stored');
        }

        // Check http method
        $this->checkHttpMethod($method);

        // Check params
        $oauthParams = $this->getDefaultOauthPluginParams() + $oauthParams;
        $this->checkParams(array('consumer_key', 'consumer_secret', 'access_token', 'access_token_secret'));

        return $this->sendOAuthRequest($method, $url, $oauthParams, $params, $headers, $options);
    }

    /**
     * Send an http oauth request
     *
     * @param string $method      Http method
     * @param string $url         Url
     * @param array  $oauthParams Extra Oauth request parameters
     * @param array  $params      Request parameters
     * @param array  $headers     Request parameters
     * @param array  $options     Request parameters
     *
     * @return Response
     */
    protected function sendOAuthRequest($method, $url, array $oauthParams = array(), $params = array(), $headers = null, $options = array())
    {
        $oauthParams = $oauthParams ?: $this->getDefaultOauthPluginParams();

        $this->httpClient->addSubscriber(new OauthPlugin($oauthParams));

        $request = $this->httpClient->createRequest($method, $url, $headers, $params, $options);

        return $request->send();
    }

    /**
     * Returns default params for oauth plugin
     *
     * @return array
     */
    protected function getDefaultOauthPluginParams()
    {
        return array(
            'consumer_key'    => $this->get('consumer_key'),
            'consumer_secret' => $this->get('consumer_secret'),
            'token'           => $this->get('access_token'),
            'token_secret'    => $this->get('access_token_secret'),
        );
    }

    /**
     * Check asked Http method
     *
     * @param string $httpMethod Http method
     *
     * @throws PreconditionRequiredHttpException
     * @return void
     */
    protected function checkHttpMethod(&$httpMethod)
    {
        $httpMethod = strtoupper($httpMethod);
        if (!in_array($httpMethod, array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS', 'PATCH'))) {
            throw new PreconditionRequiredHttpException($httpMethod.' is not an http method');
        }
    }


    /**
     * Check if client has required params stored
     *
     * @param array $required Array of required fields
     *
     * @throws PreconditionRequiredHttpException
     * @return void
     */
    protected function checkParams(array $required = array())
    {
        foreach ($required as $requiredParam) {
            if (null === $this->get($requiredParam)) {
                throw new PreconditionRequiredHttpException('Missing api client parameter: '. $requiredParam);
            }
        }
    }

}