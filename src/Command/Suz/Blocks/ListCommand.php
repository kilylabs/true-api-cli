<?php

namespace Kily\API\TrueAPI\Cli\Command\Suz\Blocks;

use Kily\API\TrueAPI\Cli\Command\Suz\BlocksCommand;
use Kily\API\TrueAPI\Cli\Config;
use Kily\API\TrueAPI\Cli\Exception\Exception;

class ListCommand extends BlocksCommand
{

	public function brief()
	{
		return 'SUZ codes list command';
	}

	public function init()
	{
	}

	public function options($opts)
	{
        $opts->add('g|product-group:','Product group')->isa('string')->defaultValue("lp");
        $opts->add('l|limit:','Limit number of codes listed')->isa('number');
        $opts->add('p|pretty','Pretty print');
        $opts->add('t|table','Print table');
        $opts->add('c|columns:','Comma-separated list of columns')->isa('string');

        $opts->add('omsid:','OMS ID')->isa('string');
        $opts->add('clnt:','Client Token')->isa('string');
        $opts->add('order:','Order ID')->isa('string');
        $opts->add('gtin:','GTIN')->isa('string');
	}

	public function execute($action=null)
	{
        $opts = $this->getOptions();

        if(!$omsid = $opts->omsid) {
            if(!$omsid = Config::get('suz_oms_id')) {
                throw new Exception("You need to define --omsid option or store \"suz_oms_id\" value in config");
            }
        }

        if(!$clnt = $opts->clnt) {
            if(!$clnt = Config::get('suz_client_id')) {
                throw new Exception("You need to define --clnt option or store \"suz_client_id\" value in config");
            }
        }

        if(!$order_id = $opts->order) {
            throw new Exception("You need to define --order option");
        }

        if(!$gtin = $opts->gtin) {
            throw new Exception("You need to define --gtin option");
        }

        $resp = $this->signedRequest('GET',$opts->{'product-group'}.'/codes/blocks',[
            'headers'=>[
                'clientToken'=>$clnt,
            ],
            'query'=>[
                'gtin'=>$gtin,
                'omsId'=>$omsid,
                'orderId'=>$order_id,
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
