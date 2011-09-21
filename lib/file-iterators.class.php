<?php

namespace wp2static\iterators;

class FileFilterIterator extends \FilterIterator {
    public function __construct(\Iterator $iterator) {
        parent::__construct($iterator);
    }
 
    public function accept() {
        $fileInfo = parent::current();
         
        // Allow only files
        return ($fileInfo->isFile());
    }
}

class FileExtensionFilterIterator extends \FilterIterator {
    protected $whitelist;
    protected $blacklist;
 
    public function __construct(\Iterator $iterator, array $whitelist = array(), array $blacklist = array()) {
        parent::__construct($iterator);
        
        $this->whitelist = $whitelist;
        $this->blacklist = $blacklist;
    }
 
    public function accept() {
        $fileInfo = parent::current();
         
        // Allow only files
        if(!$fileInfo->isFile()) {
            return false;
        }
        
        $pi = pathinfo($fileInfo->getFilename());
        $ext = strtolower($pi['extension']);
        
        if($this->whitelist && !in_array($ext, $this->whitelist)) {
            return false;
        }
        
        if(in_array($ext, $this->blacklist)) {
            return false;
        }
        
        return true;
    }
}
