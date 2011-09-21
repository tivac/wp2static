<?php

namespace wp2static\storage;

require("storage.abstract.php");

class Local extends Storage {
    
    function __construct($base) {
        if(!file_exists($base)) {
            $result = mkdir($base, 0777, true);
            
            if(!$result) {
                throw new InvalidBaseException("Unable to create {$base}");
            }
        }
        
        parent::__construct($base);
    }
    
    public function write($i) {
        if(!$i['name'] || !$i['content']) return false;
        
        $path = $this->_mergePath($i['name']);
        
        $this->_createDirs($path);
        
        return file_put_contents($path, $i['content']);
    }
    
    public function delete($name) {
        $name = $this->_mergePath($name);
        
        return unlink($name);
    }
    
    public function copy($source, $dest) {
        $dest = $this->_mergePath($dest);
        
        $this->_createDirs($dest);
        
        return copy($source, $dest);
    }
    
    //combine base path + filename, optionally slashing the difference
    protected function _mergePath($path) {
        return (($path[0] != '/') ? trailingslashit($this->base) : $this->base) . $path;
    }
    
    protected function _createDirs($path) {
        $dir = dirname($path);
        
        if(!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
