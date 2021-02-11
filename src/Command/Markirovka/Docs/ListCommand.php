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
        $opts->add('g|product-group:','Product group')->isa('string');
        $opts->add('l|limit:','Limit number of documents listed')->isa('number');
        $opts->add('p|pretty','Pretty print');
        $opts->add('t|table','Print table');
        $opts->add('c|columns:','Comma-separated list of columns')->isa('string');

        $opts->add('n|number:','Document number')->isa('string');
        $opts->add('datef:','List documents from date')->isa('string');
        $opts->add('datet:','List documents till date')->isa('string');
        $opts->add('docf:','Document format')->isa('string');
        $opts->add('docs:','Document status')->isa('string');
        $opts->add('doct:','Document type')->isa('string');
        $opts->add('if','Document input type')->isa('string');
        $opts->add('od','Document order direction')->isa('string');
        $opts->add('oc','Document column')->isa('string');
        $opts->add('ocv','Document column start value')->isa('string');
        $opts->add('pd','Document page direction')->isa('string');
        $opts->add('inn','Participant inn')->isa('string');
	}

	public function execute($action=null)
	{
        $opts = $this->getOptions();

        $resp = $this->parent->signedRequest('GET','doc/listV2',[
            'query'=>array_filter([
                'pg'=>$opts->{'product-group'} ?: 'lp',
                'limit'=>$opts->limit ?: '10',
                'number'=>$opts->number ?: '',
                'dateFrom'=>$opts->datef ? date('Y-m-d\TH:i:s.v\Z',strtotime($opts->datef)) : null,
                'dateTo'=>$opts->datet ? date('Y-m-d\TH:i:s.v\Z',strtotime($opts->datet)) : null,
                'documentFormat'=>$opts->docf ?: null,
                'documentStatus'=>$opts->docs ?: null,
                'documentType'=>$opts->doct ?: null,
                'inputFormat'=>$opts->if ?: null,
                'order'=>$opts->od ?: null,
                'orderColumn'=>$opts->oc ?: null,
                'orderColumnValue'=>$opts->ocv ?: null,
                'pageDir'=>$opts->pd ?: null,
                'participantInn'=>$opts->inn ?: null,
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
