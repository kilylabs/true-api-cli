<?php

namespace Kily\API\TrueAPI\Cli\Command\Markirovka;

use Kily\API\TrueAPI\Cli\Command\BaseCommand;

class DocsCommand extends BaseCommand
{

	public function brief()
	{
		return 'awesome help brief.';
	}

	public function init()
	{
	}

	public function options($opts)
	{
		// command options

	}

    public function arguments($args)
    {
        $args->add('subcommand')
            ->desc('Subcommand')
            ->validValues(['list']);
    }

	public function execute($subcommand,$pg=null,$limit=10)
	{
        if($subcommand == 'list') {
            $res = $this->parent->signedRequest('GET', 'doc/listV2', [
                'query'=>[
                    'pg'=>$pg ?: 'lp',
                    'limit'=>$limit,
                ],
            ]);
            echo $res->getBody()->__toString(),"\n";
        }
	}

}
