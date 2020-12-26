<?php

namespace Kily\API\TrueAPI\Cli\Command\Markirovka\Docs;

use Kily\API\TrueAPI\Cli\Command\BaseCommand;

class ListCommand extends BaseCommand
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
        $opts->add('pg|product-group:','Product group')->isa('string');
        $opts->add('l|limit:','Limit number of documents listed')->isa('number');
        $opts->add('p|pretty','Pretty print');
        $opts->add('t|table','Print table');
        $opts->add('c|columns:','Comma-separated list of columns')->isa('string');
	}

	public function execute($action=null)
	{
        $opts = $this->getOptions();

        $resp = $this->parent->signedRequest('GET','doc/listV2',[
            'query'=>[
                'pg'=>$opts->pg ?: 'lp',
                'limit'=>$opts->limit ?: '10',
            ],
        ]);

        if($opts->table) {
            $data = json_decode($resp->getBody()->__toString(),true);
            $this->printTable($data['results']??[],$opts->columns);
        } elseif($opts->pretty) {
            echo json_encode(json_decode($resp->getBody()->__toString()),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        } else {
            echo $resp->getBody()->__toString();
        }
	}

}
