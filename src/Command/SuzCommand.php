<?php

namespace Kily\API\TrueAPI\Cli\Command;

use GuzzleHttp\Exception\RequestException;

class SuzCommand extends BaseCommand
{
    const PROD_BASE_URL = 'https://suzgrid.crpt.ru/api/v2/';
    const DEMO_BASE_URL = null;

	public function brief()
	{
		return 'Suz-related commands';
	}

    public function getHttpClientOptions() {
        return [
            'base_uri'=>self::PROD_BASE_URL,
        ];
    }

	public function init()
	{
        $this->command('orders','\Kily\API\TrueAPI\Cli\Command\Suz\OrdersCommand');
        $this->command('codes','\Kily\API\TrueAPI\Cli\Command\Suz\CodesCommand');
        $this->command('blocks','\Kily\API\TrueAPI\Cli\Command\Suz\BlocksCommand');
	}

	public function options($opts)
	{
	}


    public function execute($action) {
    }

    public function signedRequest($method,$uri,$options = []) {
        $opts = $this->getOptions();

        $client = $this->getHttpClient();
        $options = array_replace_recursive([
            'headers'=>[
                'X-Signature'=>$this->sign($client,$method,$uri,$options),
            ],
        ],$options);
        try {
            return $client->request($method,$uri,$options);
        } catch(RequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse();
            }
            throw $e;
        }
    }

    protected function sign($client,$method,$uri,$options) { 
        if($method === 'GET') {
            $str = $client->getConfig('base_uri');
            $str .= $uri;
            if(isset($options['query'])) {
                $str .= http_build_query($options['query']);
            }
            return $this->signData($str);
        } elseif($method === 'POST') {
        } else {
            throw new \Exception("BAD THING");
        }
    }
}
