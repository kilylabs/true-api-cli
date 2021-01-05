<?php

namespace Kily\API\TrueAPI\Cli;

use Noodlehaus\Config as JSONConfig;
use Noodlehaus\Writer\Json;
use malkusch\lock\mutex\FlockMutex;
use Kily\API\TrueAPI\Cli\Exception\Exception;


class Config {

    const APP_NAME = 'true-cli';
    const CONFIG_FILE = 'config.json';

    private static $_instance;
    private static $config_file;

    public static function getInstance($config_file) {
        if(!self::$_instance) {
            if(!$config_file) {
                $xdg = new \XdgBaseDir\Xdg();
                $config_file = $xdg->getConfigDirs()[0] ?? null;
                $config_file .= '/'.self::APP_NAME.'/'.self::CONFIG_FILE;
                if(!file_exists($config_file)) {
                    @mkdir(dirname($config_file),0775,true);
                    @touch($config_file);
                    @file_put_contents($config_file,json_encode(new \stdClass));
                }
            }
            self::$config_file = $config_file;
            try {
                self::$_instance = JSONConfig::load(self::$config_file);
            } catch(\Noodlehaus\Exception\ParseException $e) {
                throw new Exception('Bad config syntax: '.$config_file.'. Is it in JSON format?'); 
            }
        }
        return self::$_instance;
    }

    public static function get($k) {
        $config_file = self::$config_file;
        $conf = self::getInstance($config_file);
        return $conf->get($k);
    }

    public static function set($k,$v) {
        $config_file = self::$config_file;
        $mutex = new FlockMutex(fopen($config_file, "rw"));
        $conf = self::getInstance($config_file);
        $mutex->synchronized(function () use ($conf,$config_file,$k,$v) {
            $conf->set($k,$v);
            $conf->toFile($config_file,new Json);
        });
    }


}
