<?php

namespace Kily\API\TrueAPI\Cli\Command\Markirovka\Cises;

use Kily\API\TrueAPI\Cli\Command\BaseCommand;

class ViewCommand extends BaseCommand
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
        $opts->add('p|pretty','Pretty print');
        $opts->add('t|table','Print table');
        $opts->add('c|columns:','Comma-separated list of columns')->isa('string');
	}

	public function execute($id)
	{
        $opts = $this->getOptions();

        $resp = $this->parent->signedRequest('POST','cises/list',[
            'query'=>[
                //'pg'=>$opts->pg ?: 'lp',
                //'limit'=>$opts->limit ?: '10',
                'values'=>$id,
            ],
        ]);

        if($opts->table && (strpos($resp->getStatusCode(),"2") === 0)) {
            $data = json_decode($resp->getBody()->__toString(),true);
            $this->printTable($data??[],$opts->columns);
        } elseif($opts->pretty) {
            echo json_encode(json_decode($resp->getBody()->__toString()),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        } else {
            echo $resp->getBody()->__toString();
        }
	}

}
