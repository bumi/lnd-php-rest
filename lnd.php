<?php
//require dirname(__FILE__).'/../lnd-php-rest/vendor/autoload.php';
class LND {

  private $apiVersion = 'v1';
  private $baseURI = '';
  private $tlsCertificatePath = '';
  private $macaroonHex;

  public function setHost($host) {
    $this->baseURI = 'https://' . $host . '/' . $this->apiVersion . '/';
  }
  public function setMacarronHex($macaroonHex) {
    $this->macaroonHex = $macaroonHex;
  }
  public function setTlsCertificatePath($path) {
    $this->tlsCertificatePath = $path;
  }

  public function getInfo() {
    return $this->request('GET', 'getinfo');
  }

  private function request($method, $path, $body = null) {
    $headers = [
      'Grpc-Metadata-macaroon' => $this->macaroonHex,
      'Content-Type' => 'application/json'
    ];

    $request = new GuzzleHttp\Psr7\Request($method, $path, $headers, $body);
    $response = $this->client()->send($request);
    if ($response->getStatusCode() == 200) {
      $body = $response->getBody()->getContents();
      return json_decode($body);
    } else {
      // raise exception
    }
  }

  private function client() {
    $options = ['base_uri' => $this->baseURI];
    if ($this->tlsCertificatePath) {
      $options['verify'] = $this->tlsCertificatePath;
    }
    $client = new GuzzleHttp\Client($options);
    return $client;
  }
}

?>
