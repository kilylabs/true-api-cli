<?php

namespace Kily\API\TrueAPI\Cli\Command;

use Kily\Api\TrueAPI\Cli\Exception\AuthException;
use GuzzleHttp\Client as HttpClient;
use CLIFramework\Command;
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
        $client = $this->getHttpClient();
        if(!$this->getAuthToken()) {
            $this->auth();
        }
        $options = array_merge($this->getSignedRequestOptions(),$options);
        return $client->request($method,$uri,$options);
    }

    protected function auth() {
        $opts = $this->getApplication()->getOptions();
        $client = $this->getHttpClient();
        $res = $client->request('GET', 'auth/key');
        $out = json_decode($res->getBody()->__toString());
        $content = $out->data;

        $store = new CPStore();
        $store->Open(CURRENT_USER_STORE,"my",STORE_OPEN_READ_ONLY);
        $certs = $store->get_Certificates();
        $cert = null;
        if(!$opts->certid) {
            foreach(range(1,$certs->Count()) as $certid) {
                $cert = $certs->Item($certid);
                if($cert->IsValid()) {
                    break;
                }
                $cert = null;
            }
        } else {
            $cert = $certs->Item($opts->certid);
        }

        if(!$cert) {
            throw new AuthException("No valid certificate found");
        }

        $signer = new CPSigner();
        $signer->set_Certificate($cert);

        $sd = new CPSignedData();
        $sd->set_Content($content);

        $sm = $sd->SignCades($signer, CADES_BES , false, ENCODE_BASE64);
        $sm = preg_replace("/[\r\n]/","",$sm);

        $res = $client->request('POST', 'auth/simpleSignIn', [
            'json'=>[
                'uuid'=>$out->uuid,
                'data'=>$sm,
            ],
        ]);
        $json = json_decode($res->getBody()->__toString());
        $this->setAuthToken($json->token);
    }

    protected function setAuthToken($token) {
        $this->_auth_token = $token;
    }

    protected function getAuthToken() {
        return $this->_auth_token;
    }

}
