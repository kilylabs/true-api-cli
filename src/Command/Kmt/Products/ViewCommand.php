<?php

namespace Kily\API\TrueAPI\Cli\Command\Kmt\Products;

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
        $opts->add('pg|product-group:','Product group')->isa('string');
        $opts->add('l|limit:','Limit number of documents listed')->isa('number');
        $opts->add('p|pretty','Pretty print');
	}

	public function execute($id)
	{
        $opts = $this->getOptions();

        $resp = $this->parent->signedRequest('GET','product',[
            'query'=>[
                'gtin'=>$id,
                //'pg'=>$opts->pg ?: 'lp',
                //'cis'=>$opts->cis ?: null,
            ],
        ]);

        if($opts->table) {
            $data = json_decode($resp->getBody()->__toString(),true);
            $this->printTable($data??[],$opts->columns);
        } elseif($opts->pretty) {
            echo json_encode(json_decode($resp->getBody()->__toString()),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        } else {
            echo $resp->getBody()->__toString();
        }
	}

}
