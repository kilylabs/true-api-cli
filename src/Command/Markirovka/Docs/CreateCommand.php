<?php

namespace Kily\API\TrueAPI\Cli\Command\Markirovka\Docs;

use Kily\API\TrueAPI\Cli\Command\BaseCommand;
use Kily\API\TrueAPI\Cli\Exception\Exception;

class CreateCommand extends BaseCommand
{


	public function brief()
	{
		return 'Create command description';
	}

	public function init()
	{
	}

	public function options($opts)
	{
        $opts->add('pg|product-group:','Product group')->isa('string');

        $opts->add('t|type:','Document type to send')->isa('string');
        $opts->add('f|file:','File to send')->isa('file');
	}

	public function execute($action=null)
	{
        $opts = $this->getOptions();

        if(!$type = $opts->type) {
            $list = array_map(function($file) {
                return pathinfo($file,PATHINFO_FILENAME);
            },array_filter(scandir(__DIR__.'/templates'),function($file) {
                return !in_array($file,['.','..']);
            }));
            $type = $this->ask("Choose valid type: ",$list);
        }

        if($opts->file) {
            $data = file_get_contents($opts->file);
        } else {
            $editor = getenv("EDITOR") ?: 'vi';
            $tmpfile = tempnam(sys_get_temp_dir(),"trueapi").'.json';

            $tpl_file = __DIR__.'/templates/'.$type.'.json';
            file_put_contents($tmpfile,file_get_contents($tpl_file));
            $ret = null;
            system("$editor $tmpfile > `tty`",$ret);

            //var_dump($ret);
            //die();
            if($ret !== 0 || (md5_file($tpl_file) === md5_file($tmpfile)) ) {
                echo 'not sending',"\n";
                unlink($tmpfile);
                return;
            }  else {
                $data = file_get_contents($tmpfile);
            }

            $data = preg_replace('/#.*$/Um','',$data);
            $data = implode("\n",array_filter(explode("\n",$data),'trim'));

        }
        //die();

        if(!$decoded = @json_decode($data)) {
            if(!$opts->file) {
                file_put_contents($tmpfile,$data);
                throw new Exception("Data is not json. Temp file is ".$tmpfile);
            } else {
                throw new Exception("Data in file ".$opts->file." is not json");
            }
        }
        $data = json_encode($decoded);

        $json = [
            'signature'=>$this->signData($data),
            'product_document'=>base64_encode($data),
            'document_format'=>'MANUAL',
            'type'=>$type,
        ];

        $resp = $this->parent->signedRequest('POST','lk/documents/create',[
            'query'=>[
                'pg'=>$opts->pg ?: 'lp',
            ],
            'json'=>$json,
        ]);

        echo $resp->getBody()->__toString()."\n";
	}

}
