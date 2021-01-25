<?php

namespace Kily\API\TrueAPI\Cli\Command\Markirovka\Products;

use Kily\API\TrueAPI\Cli\Command\BaseCommand;
use Kily\API\TrueAPI\Cli\Exception\Exception;

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
        $opts->add('g|product-group:','Product group')->isa('string');
        $opts->add('l|limit:','Limit number of documents listed')->isa('number');

        $opts->add('p|pretty','Pretty print');
        $opts->add('t|table','Print table');
        $opts->add('c|columns:','Comma-separated list of columns')->isa('string');

        $opts->add('by-gtin','List gtins');
        $opts->add('gtin','Comma-separated list of gtins');
	}

	public function execute($action=null)
	{
        $opts = $this->getOptions();

        if($opts->{'by-gtin'}) {
            if(!$gtin = $opts->gtin) {
                throw new Exception("You need to define --gtin option");
            }
            $resp = $this->parent->signedRequest('GET','https://ismp.crpt.ru/api/v4/product/info',[
                'query'=>[
                    'includeSubaccount'=>true,
                    'create'=>false,
                    'gtins'=>$gtin
                    //'pg'=>$opts->pg ?: 'lp',
                    //'limit'=>$opts->limit ?: '10',
                    //'cis'=>$opts->cis ?: null,
                ],
            ]);
        } else {
            $resp = $this->parent->signedRequest('GET','products/listV2',[
                'query'=>[
                    //'pg'=>$opts->pg ?: 'lp',
                    //'limit'=>$opts->limit ?: '10',
                    //'cis'=>$opts->cis ?: null,
                ],
            ]);
        }

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
