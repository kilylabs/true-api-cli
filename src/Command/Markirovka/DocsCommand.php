<?php

namespace Kily\API\TrueAPI\Cli\Command\Markirovka;

use Kily\API\TrueAPI\Cli\Command\BaseCommand;
use CLIFramework\Command\HelpCommand;

class DocsCommand extends BaseCommand
{


	public function brief()
	{
		return 'awesome help brief.';
	}

	public function init()
	{
        $this->command('list','\Kily\API\TrueAPI\Cli\Command\Markirovka\Docs\ListCommand');
        $this->command('view','\Kily\API\TrueAPI\Cli\Command\Markirovka\Docs\ViewCommand');
        $this->command('create','\Kily\API\TrueAPI\Cli\Command\Markirovka\Docs\CreateCommand');
	}

	public function options($opts)
	{
	}

	public function execute($arg1)
	{
	}

    public function signedRequest($method,$uri,$options = []) {
        return $this->parent->signedRequest($method,$uri,$options);
    }

}
