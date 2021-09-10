<?php

namespace Kily\API\TrueAPI\Cli\Command\Suz\Orders;

use Kily\API\TrueAPI\Cli\Command\Suz\OrdersCommand;
use Kily\API\TrueAPI\Cli\Config;
use Kily\API\TrueAPI\Cli\Exception\Exception;

class ListCommand extends OrdersCommand
{


	public function brief()
	{
		return 'SUZ orders list command';
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

        $opts->add('o|owned','List own cises');
        $opts->add('r|received','List received cises');
        $opts->add('s|sent','List sent cises');

        $opts->add('omsid:','OMS ID')->isa('string');
        $opts->add('clnt:','Client Token')->isa('string');
	}

	public function execute($action=null)
	{
        $opts = $this->getOptions();

        if(!$opts->omsid) {
            if(!$opts->omsid = Config::get('suz_oms_id')) {
                throw new Exception("You need to define --omsid option or store \"suz_oms_id\" value in config");
            }
        }

        if(!$opts->clnt) {
            if(!$opts->clnt = Config::get('suz_client_id')) {
                throw new Exception("You need to define --clnt option or store \"suz_client_id\" value in config");
            }
        }

        $resp = $this->tokenRequest('GET',$opts->{'product-group'}.'/orders',[
            'headers'=>[
                'clientToken'=>$opts->clnt,
            ],
            'query'=>[
                'limit'=>$opts->limit ?: '10',
                'omsId'=>$opts->omsid,
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
