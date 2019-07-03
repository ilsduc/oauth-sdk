<?php
require './ProviderInterface.php';

class GenericProvider implements ProviderInterface
{
  private $provider = null;

  //
  public function __construct($providerName, $credentials)
  {
    $confs = yaml_parse_file('./providers.yml');
    $this->provider = $confs[$providerName];
    $this->provider['name'] = $providerName;
    $this->provider['client_id'] = $credentials['client_id'];
    $this->provider['client_secret'] = $credentials['client_secret'];
  }
  //
  public function getProviderName()
  {
    return $this->provider['name'];
  }
  //
  public function getAuthorizationUrl($state)
  {
    $queryString = 'client_id='.$this->provider['client_id'];
    $queryString .= '&redirect_uri='.$this->provider['redirect_uri'].'?provider='.$this->provider['name'];
    $queryString .= '&scope='.$this->provider['scope'];
    $queryString .= '&response_type='.$this->provider['response_type'];
    $queryString .= '&state='.$state;
    return $this->provider['authorization_url'].$queryString;
  }
  //
  public function getAccessTokenUrl()
  {
    $code = $_GET['code'];
    //
    $queryString = 'client_id='.$this->provider['client_id'];
    $queryString .= '&redirect_uri='.$this->provider['redirect_uri'].'?provider='.$this->provider['name'];
    $queryString .= '&client_secret='.$this->provider['client_secret'];
    $queryString .= '&code='.$code;
    return $this->provider['access_token_url'] . $queryString;
  }
  //
  public function getUserInfos()
  {
    $queryString = 'access_token='. $_SESSION['access_token'];
    $response = (array) json_decode(file_get_contents($this->provider['ressources_owner_url'] . $queryString));
    //
    $result = [
      'id' => $response[$this->provider['mapping']['id']],
      'name' => $response[$this->provider['mapping']['name']],
      'email' => $response[$this->provider['mapping']['email']],
    ];
    //
    return $result;
  }
  //
  public function getProvider()
  {
    return $this->provider;
  }
}
