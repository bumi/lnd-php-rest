<?php
require 'vendor/autoload.php';

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

  public function addInvoice($invoice) {
    return $this->request('POST', 'invoices', json_encode($invoice));
  }

  public function getInvoice($r_hash_str) {
    return $this->request('GET', 'invoice/' . bin2hex(base64_decode($r_hash_str)));
  }

  public function isInvoicePaid($r_hash_str) {
    $invoice = $this->getInvoice($r_hash_str);
    return $invoice->{'settled'};
  }

  private function request($method, $path, $body = null) {
    $headers = [
      'Grpc-Metadata-macaroon' => $this->macaroonHex,
      'Content-Type' => 'application/json'
    ];

    $request = new GuzzleHttp\Psr7\Request($method, $path, $headers, $body);
    $response = $this->client()->send($request);
    if ($response->getStatusCode() == 200) {
      $responseBody = $response->getBody()->getContents();
      return json_decode($responseBody);
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
