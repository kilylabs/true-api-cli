<?php

namespace Kily\API\TrueAPI\Cli\Command;

use GuzzleHttp\Exception\RequestException;

class KmtCommand extends BaseCommand
{
    const PROD_BASE_URL = 'https://xn--80aqu.xn----7sbabas4ajkhfocclk9d3cvfsa.xn--p1ai/v3/';
    const DEMO_BASE_URL = 'https://api.integrators.nk.crpt.tech/v3/';

	public function brief()
	{
		return 'Kmt-related commands';
	}

    public function getHttpClientOptions() {
        return [
            'base_uri'=>self::PROD_BASE_URL,
        ];
    }

	public function init()
	{
        $this->command('products','\Kily\API\TrueAPI\Cli\Command\Kmt\ProductsCommand');
	}

	public function options($opts)
	{
	}


    public function execute($action)
    {
    }

    public function getSignedRequestOptions() {
        return [
            'headers'=>[
                'Authorization'=>'Bearer '.$this->getAuthToken()
            ],
        ];
    }

}
