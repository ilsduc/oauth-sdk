<?php
require './GenericProvider.php';

class OAuthSDK
{
  /**
  *@var ProviderInterface[]
  */
  private $providers = [];

  public function __construct( $credentials )
  {
    //
    foreach ($credentials as $provider => $confs) {
      $this->providers[] = new GenericProvider($provider, $confs);
    }
  }
  //
  public function getConnectionsLinks()
  {
    $connectionsLinks = [];
    // loop through providers
    $state = $this->generateState();
    foreach ($this->providers as $provider) {
      $connectionsLinks[$provider->getProviderName()] = $provider->getAuthorizationUrl($state);
    }
    return $connectionsLinks;
  }

  public function getAccessTokenUrl($providerName)
  {
    foreach ($this->providers as $provider) {
      if ($provider->getProviderName() === $providerName)
      {
        return $provider->getAccessTokenUrl();
      }
    }
  }

  public function generateState()
  {
    $state = uniqid('state');
    $_SESSION['state'] = $state;
    return $state;
  }

  public function getUserInfos($providerName)
  {
    foreach ($this->providers as $provider) {
      if ($provider->getProviderName() === $providerName)
      {
        return $provider->getUserInfos();
      }
    }
  }

}
