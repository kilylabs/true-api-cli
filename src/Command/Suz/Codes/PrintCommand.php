<?php

namespace Kily\API\TrueAPI\Cli\Command\Suz\Codes;

use Kily\API\TrueAPI\Cli\Command\Suz\CodesCommand;
use Kily\API\TrueAPI\Cli\Config;
use Kily\API\TrueAPI\Cli\Exception\Exception;

class PrintCommand extends CodesCommand
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
        $opts->add('q|qnt:','Quantity')->isa('number');
        $opts->add('yes','Say Yes to print');
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
            throw new Exception("You need to define --order_id option");
        }

        if(!$qnt = $opts->qnt) {
            throw new Exception("You need to define --qnt or -q option");
        }

        if(!$gtin = $opts->gtin) {
            throw new Exception("You need to define --gtin option");
        }

        if(!$yes = $opts->yes) {
            $yes = $this->ask("Codes will be printed only once. It would be imposible to print it again - it is API limitation. Are you sure? ",['Yes','No']);
            if($yes === 'Yes') $yes = true;
            else $yes = false;
        }
        if(!$yes) {
            echo 'Aborting...',"\n";
            return;
        }

        $resp = $this->tokenRequest('GET',$opts->{'product-group'}.'/codes',[
            'headers'=>[
                'clientToken'=>$clnt,
            ],
            'query'=>[
                'gtin'=>$gtin,
                'omsId'=>$omsid,
                'orderId'=>$order_id,
                'quantity'=>$qnt,
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
