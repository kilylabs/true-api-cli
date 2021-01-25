<?php

namespace Kily\API\TrueAPI\Cli\Command\Markirovka\Cises;

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
        $opts->add('l|limit:','Limit number of codes listed')->isa('number');
        $opts->add('p|pretty','Pretty print');
        $opts->add('t|table','Print table');
        $opts->add('c|columns:','Comma-separated list of columns')->isa('string');

        $opts->add('o|owned','List own cises');
        $opts->add('r|received','List received cises');
        $opts->add('s|sent','List sent cises');
	}

	public function execute($action=null)
	{
        $opts = $this->getOptions();

        //$resp = $this->parent->signedRequest('GET','https://ismp.crpt.ru/api/v3/facade/identifytools/listV2',[ - NOT WORKS
        //$resp = $this->parent->signedRequest('GET','cises/listV2',[ - NOT WORKS
        //$resp = $this->parent->signedRequest('POST','https://ismp.crpt.ru/api/v4/facade/cis/cis_list',[ - NOT WORKS
        if($opts->owned) {
            $resp = $this->parent->signedRequest('GET','https://ismp.crpt.ru/api/v3/facade/identifytools/listV2',[
                'query'=>[
                    'pg'=>$opts->{'product-group'} ?: 'lp',
                    'limit'=>$opts->limit ?: '10',
                ],
            ]);
        } elseif($opts->received) {
            $resp = $this->parent->signedRequest('GET','https://ismp.crpt.ru/api/v3/facade/agent/received/list',[
                'query'=>[
                    'pg'=>$opts->{'product-group'} ?: 'lp',
                    'limit'=>$opts->limit ?: '10',
                ],
            ]);
        } elseif($opts->sent) {
            $resp = $this->parent->signedRequest('GET','https://ismp.crpt.ru/api/v3/facade/agent/given/list',[
                'query'=>[
                    'pg'=>$opts->{'product-group'} ?: 'lp',
                    'limit'=>$opts->limit ?: '10',
                ],
            ]);
        } else {
            throw new \Kily\API\TrueAPI\Cli\Exception\Exception("You need to supply -o, -r or -s options to list appropriate cises");
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
