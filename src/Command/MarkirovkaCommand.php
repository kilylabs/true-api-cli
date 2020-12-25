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
        $this->commandGroup('Markirovka subcommands', [
            'docs'=> '\Kily\API\TrueAPI\Cli\Command\Markirovka\DocsCommand',
        ]);
	}

    /*
	public function options($opts)
	{
		// command options

	}

    public function arguments($args)
    {
        $args->add('subcommand')
            ->desc('Subcommand')
            ->validValues(['docs']);
    }

	public function execute($subcommand)
	{
		//$logger->info('execute');
		//$logger->error('error');
		//$input = $this->ask('Please type something');

        echo $subcommand,"\n";
	}
     */

}
