<?php

namespace Kily\API\TrueAPI\Cli\Command;

use Kily\API\TrueAPI\Cli\Exception\AuthException;
use Kily\API\TrueAPI\Cli\Config;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use CLIFramework\Command;
use CLIFramework\Component\Table\Table;
use CPStore;
use CPSigner;
use CPSignedData;

class BaseCommand extends Command
{

    protected $_client;
    protected $_auth_token;

    public function getHttpClient() {
        if(!$this->_client) {
            $this->_client = new HttpClient(
                array_merge($this->getHttpClientDefaultOptions(),$this->getHttpClientOptions())
            );
        }
        return $this->_client;
    }

    public function getHttpClientDefaultOptions() {
        $opts = $this->getApplication()->getOptions();
        return [
            'debug'=>$opts->verbose ? true : false,
            'timeout'=>10,
        ];
    }

    public function getHttpClientOptions() {
        return [];
    }

    public function getSignedRequestOptions() {
        return [
            'headers'=>[
                'Authorization'=>'Bearer '.$this->getAuthToken()
            ],
        ];
    }

    public function signedRequest($method,$uri,$options = []) {
        if(!$this->getAuthToken()) {
            $this->auth($method,$uri,$options);
        }
        try {
            return $this->signedRequestInternal($method,$uri,$options);
        } catch(RequestException $e) {
            if ($e->hasResponse()) {
                $resp = $e->getResponse();
                if($resp->getStatusCode() == 401) {
                    if($this->auth($method,$uri,$options)) {
                        return $this->signedRequestInternal($method,$uri,$options);
                    }
                } else {
                    return $resp;
                }
            }
            throw $e;
        }
    }

    protected function signedRequestInternal($method,$uri,$options) {
        $client = $this->getHttpClient();
        $options = array_merge($this->getSignedRequestOptions(),$options);
        $request = new Request($method,$uri);
        return $client->send($request,$options);
    }

    public function printTable($list,$columns=null) {
        if(!$list) return '';
        $headers = [];
        if($columns) {
            $columns = explode(',',$columns);
        }
        $table = new Table;
        foreach($list as $item) {
            if(!$headers) {
                $headers = array_keys($item);
                if($columns) {
                    $headers = array_filter($headers, function($el) use ($columns) {
                        return in_array($el, $columns);
                    });
                    $headers = array_values($headers);
                }
                $table->setHeaders($headers);
            }
            if($columns) {
                $item = array_filter($item, function($el) use ($columns) {
                    return in_array($el, $columns);
                },ARRAY_FILTER_USE_KEY);
                $item = array_values($item);
            }
            $table->addRow($item);
        }
        echo $table->render();
    }

    protected function auth($method,$uri,$options) {
        $client = $this->getHttpClient();
        $res = $client->request('GET', 'auth/key');
        $out = json_decode($res->getBody()->__toString());
        $content = $out->data;

        $sm = $this->signData($content);

        $res = $client->request('POST', 'auth/simpleSignIn', [
            'json'=>[
                'uuid'=>$out->uuid,
                'data'=>$sm,
            ],
        ]);
        $json = json_decode($res->getBody()->__toString());
        $this->setAuthToken($json->token);

        return true;
    }

    protected function signData($content) {
        $opts = $this->getApplication()->getOptions();
        $store = new CPStore();
        $store->Open(CURRENT_USER_STORE,"my",STORE_OPEN_READ_ONLY);
        $certs = $store->get_Certificates();
        $last_e = $cert = $sm = null;
        $signer = new CPSigner();
        $certids = range(1,$certs->Count());
        if($opts->certid) {
            $certids = [$opts->certid];
        }
        foreach($certids as $certid) {
            $cert = $certs->Item($certid);
            if($cert->IsValid()) {
                $signer->set_Certificate($cert);

                if($opts->pin) {
                    $signer->set_KeyPin($opts->pin);
                }

                $sd = new CPSignedData();
                $sd->set_Content($content);

                try {
                    $sm = $sd->SignCades($signer, CADES_BES , false, ENCODE_BASE64);
                } catch(\Exception $e) {
                    $last_e = $e;
                    continue;
                }
            }
        }

        if(!$cert) {
            throw new AuthException("None of certificates found");
        } elseif(!$sm) {
            throw new AuthException('Error trying sign auth data. Make sure your certificate is accesible. '.$last_e->__toString(),0,$last_e);
        }

        return preg_replace("/[\r\n]/","",$sm);
    }

    protected function setAuthToken($token) {
        $this->_auth_token = $token;
        Config::set("markirovka_auth_token",$token);
    }

    protected function getAuthToken() {
        if(!$this->_auth_token) {
            $this->_auth_token = Config::get("markirovka_auth_token");
        }
        return $this->_auth_token;
    }

}
