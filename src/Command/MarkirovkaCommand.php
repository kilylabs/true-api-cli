<?php

namespace Kily\API\TrueAPI\Cli\Command;

class MarkirovkaCommand extends BaseCommand
{
    const PROD_BASE_URL = 'https://markirovka.crpt.ru/api/v3/true-api/';
    const DEMO_BASE_URL = 'https://int01.gismt.crpt.tech/api/v3/true-api/';

	public function brief()
	{
		return 'Markirovka-related commands';
	}

    public function getHttpClientOptions() {
        return [
            'base_uri'=>self::PROD_BASE_URL,
        ];
    }

	public function init()
	{
        parent::init();
        $this->command('docs','\Kily\API\TrueAPI\Cli\Command\Markirovka\DocsCommand');
        $this->command('products','\Kily\API\TrueAPI\Cli\Command\Markirovka\ProductsCommand');
        $this->command('cises','\Kily\API\TrueAPI\Cli\Command\Markirovka\CisesCommand');
	}

}
