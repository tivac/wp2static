<?php

namespace wp2static\storage;

class InvalidBaseException extends \Exception {}

abstract class Storage {

    public $connected = false;
    protected $base;
    
    function __construct($base) {
        $this->base = $base;
    }
    
    public function connect() {
        $this->connected = true;
    }
    
    public function disconnect() {
        $this->connected = false;
    }
    
    abstract public function write($item);
    abstract public function delete($name);
    abstract public function copy($source, $dest);
    
    abstract protected function _createDirs($path);
}
