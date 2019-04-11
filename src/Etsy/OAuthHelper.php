<?php
namespace Etsy;

/**
* 
*/
class OAuthHelper
{
    /** @var EtsyClient */
    private $client;
    private $_request_token;
    private $access_token = array();

    function __construct($client)
    {
        $this->client = $client;
    }

    public function requestPermissionUrl(array $extra = array())
    {
        $this->setRequestToken($this->client->getRequestToken($extra));

        return $this->getRequestToken()['login_url'];
    }
    public function authorize(){
        $this->client->authorize($this->getRequestToken()['oauth_token'], $this->getRequestToken()['oauth_token_secret']);
    }

    public function getAccessToken($verifier)
    {
        $this->authorize();

        $this->access_token = $this->client->getAccessToken($verifier);

        return $this->getAuth();
    }

    public function getAuth()
    {
        $auth = array();
        $auth['consumer_key'] = $this->client->getConsumerKey();
        $auth['consumer_secret'] = $this->client->getConsumerSecret();
        $auth['token_secret'] = $this->getRequestToken()['oauth_token'];
        $auth['token'] = $this->getRequestToken()['oauth_token_secret'];
        $auth['access_token'] = $this->access_token['oauth_token'];
        $auth['access_token_secret'] = $this->access_token['oauth_token_secret'];

        return $auth;
    }
    public function getRequestToken(){
        if(is_null($this->_request_token)){
            if(isset($_SESSION['etsy_request_token'])){
                $this->_request_token = $_SESSION['etsy_request_token'];
            }
            else{
                $this->_request_token =[];
            }
        }
        return $this->_request_token;
    }
    public function setRequestToken($token){
        $this->_request_token = $token;
        $_SESSION['etsy_request_token'] = $token;
    }
}