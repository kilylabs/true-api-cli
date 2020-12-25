<?php

namespace Kily\API\TrueAPI\Cli;

use CLIFramework\Application;

class Command extends Application {

    public function options($opts)
    {
        $opts->add('v|verbose', 'verbose message');
    }

    public function init()
    {
        parent::init();
        $this->command( 'markirovka', '\Kily\API\TrueAPI\Cli\Command\MarkirovkaCommand');
    }

    /*
    protected function _loadCommandDefinition() {
        $this->command('markirovka', 'WhatEver\MyCommand\BarCommand');
        $this->option()->require()->describedAs('Command to run. Can be: markirovka, catalog or suz')->must(function($cmd) {
            return in_array($cmd,['markirovka','catalog','suz']);
        });

        $method = "_load".ucfirst(strtolower($this[0])).'Definition';
        $this->$method();
    }

    protected function _loadMarkirovkaDefinition() {
        $this->option('doc')->aka('d')->boolean()->describedAs('Document-related commands');
        if($this['doc']) return $this->$method = "_load".ucfirst(strtolower($this[0])).'DocDefinition';
    }

    protected function _loadMarkirovkaDocDefinition() {
        $this->option('list')->aka('l')->boolean()->describedAs('List all documents');

        if($this['list']) {
            echo 'here';
        }
    }

    protected function _loadCatalogDefinition() {
        throw new \Exception("Not implemented yet");
    }

    protected function _loadSuzDefinition() {
        throw new \Exception("Not implemented yet");
    }
     */
}
