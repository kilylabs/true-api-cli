<?php

namespace Kily\API\TrueAPI\Cli\Command\Suz;

use Kily\API\TrueAPI\Cli\Command\SuzCommand;

class BlocksCommand extends SuzCommand
{

	public function brief()
	{
		return 'BLOCKS orders-related commands';
	}

	public function init()
	{
        $this->command('list','\Kily\API\TrueAPI\Cli\Command\Suz\Blocks\ListCommand');
        //$this->command('view','\Kily\API\TrueAPI\Cli\Command\Suz\Orders\ViewCommand');
	}

	public function options($opts)
	{
	}

	public function execute($action)
	{
	}

}
