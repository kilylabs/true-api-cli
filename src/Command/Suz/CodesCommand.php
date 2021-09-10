<?php

namespace Kily\API\TrueAPI\Cli\Command\Suz;

use Kily\API\TrueAPI\Cli\Command\SuzCommand;

class CodesCommand extends SuzCommand
{

	public function brief()
	{
		return 'CODES orders-related commands';
	}

	public function init()
	{
        $this->command('print','\Kily\API\TrueAPI\Cli\Command\Suz\Codes\PrintCommand');
        $this->command('retry','\Kily\API\TrueAPI\Cli\Command\Suz\Codes\RetryCommand');
        //$this->command('view','\Kily\API\TrueAPI\Cli\Command\Suz\Orders\ViewCommand');
	}

	public function options($opts)
	{
	}

	public function execute($action)
	{
	}

}
