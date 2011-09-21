<?php
namespace wp2static;

class Timer {
    
    private $_start;
    private $_stop;
    
    function __construct($start = false) {
        if($start) {
            $this->start();
        }
    }
    
    public function start() {
        $this->_start = microtime(true);
    }
    
    public function stop($output = false) {
        $this->_stop = microtime(true);
        
        if($output) {
            return $this->get();
        }
    }
    
    public function get($res = 2) {
        $stop = $this->_stop ? $this->_stop : microtime(true);
        
        return round($stop - $this->_start, $res);
    }
}
