<?php

namespace Kily\API\TrueAPI\Cli;

use CLIFramework\Application;

class Command extends Application {

    public function brief()
    {
        return 'TRUE API CLI Tool';
    }

    public function options($opts)
    {
        $opts->add('v|verbose', 'verbose message');
        $opts->add('i|certid:', 'certificate id');
    }

    public function init()
    {
        parent::init();
        $this->command( 'markirovka', '\Kily\API\TrueAPI\Cli\Command\MarkirovkaCommand');
    }

}
