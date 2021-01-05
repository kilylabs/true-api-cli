<?php

namespace Kily\API\TrueAPI\Cli\Command\Suz;

use Kily\API\TrueAPI\Cli\Command\SuzCommand;

class OrdersCommand extends SuzCommand
{

	public function brief()
	{
		return 'SUZ orders-related commands';
	}

	public function init()
	{
        $this->command('list','\Kily\API\TrueAPI\Cli\Command\Suz\Orders\ListCommand');
        //$this->command('view','\Kily\API\TrueAPI\Cli\Command\Suz\Orders\ViewCommand');
	}

	public function options($opts)
	{
	}

	public function execute($action)
	{
	}

}
