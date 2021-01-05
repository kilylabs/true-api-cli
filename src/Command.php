<?php

namespace Kily\API\TrueAPI\Cli;

use CLIFramework\Application;
use Kily\API\TrueAPI\Cli\Config;
use Kily\API\TrueAPI\Cli\Exception\Exception;

class Command extends Application {

    public function brief()
    {
        return 'TRUE API CLI Tool';
    }

    public function options($opts)
    {

        $opts->add('v|verbose', 'verbose message');
        $opts->add('i|certid:', 'certificate id');
        $opts->add('p|pin:', 'key pin');
        $opts->add('c|config:', 'config file path');
    }

    public function init()
    {
        parent::init();

        $this->getApplication()->getEventService()->bind("execute.before",function() {
            $opts = $this->getApplication()->getOptions();
            if($opts->config) {
                if(!file_exists($opts->config)) {
                    throw new Exception('Unable to load config file');
                }
                $config = Config::getInstance($opts->config);
            }
        });

        $this->command( 'markirovka', '\Kily\API\TrueAPI\Cli\Command\MarkirovkaCommand');
        $this->command( 'suz', '\Kily\API\TrueAPI\Cli\Command\SuzCommand');
    }

    public function runWithReturn($argv) {
        ob_start();
        parent::run($argv);
        $ret = ob_get_clean();
        return $ret;
    }
}
