<?php

namespace Kily\API\TrueAPI\Cli\Command\Markirovka\Receipts;

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
        $opts->add('g|product-group:','Product group')->isa('string');
        $opts->add('l|limit:','Limit number of documents listed')->isa('number');
        $opts->add('p|pretty','Pretty print');
        $opts->add('t|table','Print table');
        $opts->add('c|columns:','Comma-separated list of columns')->isa('string');

        $opts->add('datef:','List documents from date')->isa('string');
        $opts->add('datet:','List documents till date')->isa('string');
        $opts->add('od','Document order direction')->isa('string');
        $opts->add('pd','Document page direction')->isa('string');
        $opts->add('inn','Sender inn')->isa('string');
	}

	public function execute($action=null)
	{
        $opts = $this->getOptions();

        $resp = $this->parent->signedRequest('GET','receipt/listV2',[
            'query'=>array_filter([
                'pg'=>$opts->{'product-group'} ?: 'lp',
                'limit'=>$opts->limit ?: '10',
                'dateFrom'=>$opts->datef ? date('Y-m-d\TH:i:s.v\Z',strtotime($opts->datef)) : null,
                'dateTo'=>$opts->datet ? date('Y-m-d\TH:i:s.v\Z',strtotime($opts->datet)) : null,
                'order'=>$opts->od ?: null,
                'pageDir'=>$opts->pd ?: null,
                'senderInn'=>$opts->inn ?: null,
            ],function($el){return !is_null($el);}),
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
