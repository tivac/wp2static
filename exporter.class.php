<?php
namespace wp2static;

require('lib/storage/local-storage.class.php');
require('lib/rolling-curl.class.php');
require('lib/timer.class.php');
require('lib/export.php');

use wp2static\storage;

const TIMEOUT = 30;
const ACTION_PREFIX = 'wp2static_';

class Exporter {
    
    //our storage vars
    private $storage;
    private $rc;
    private $urls;
    
    //cached WP options
    private $wpopts;
    private $base;
    
    //per-export options
    private $output;
    
    //global timer
    private $_timer;
    
    function __construct() {
        //from the db
        $this->site = trailingslashit(home_url());
        $this->wpopts = get_option(NAME);
        $this->base = get_bloginfo('url');
        
        //init some objects we'll need
        $this->rc = new \RollingCurl(array($this, '_saveUrl'));
        $this->rc->options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => TIMEOUT,
            CURLOPT_TIMEOUT => TIMEOUT
        );
        $this->rc->headers = array(
            'X-Requested-With' => 'WP2Static-Exporter'
        );
        $this->rc->window_size = $this->wpopts['window_size'];
        
        $this->urls = array();
        
        $this->exportAll();
    }
    
    //grab every URL we can get our hands on
    public function exportAll() {
        //figure out which storage engine to use
        $storage = '\\wp2static\\storage\\' . ucfirst($this->wpopts['storage']);
        
        $this->_timer = new Timer(true);
        
        $this->_echo("=== EXPORT ALL ===");
        
        //init storage
        $this->storage = new $storage($this->wpopts['base_loc']);
        $this->storage->connect();
        
        //allow running forever
        set_time_limit(0);
        
        do_action(ACTION_PREFIX . 'exporting', $this);
        
        $this->_collectUrls();
        $this->saveUrls();
        
        $this->_echo("=== DONE EXPORTING ALL ===");
        
        do_action(ACTION_PREFIX . 'done_exporting', $this);
    }
    
    //stick a URL onto the pile
    public function addUrl($url) {
        $url = apply_filters(ACTION_PREFIX . 'url_filter', $url);
        
        if(is_array($url)) {
            foreach($url as $u => $v) {
                $this->urls[$u] = true;
            }
        } else {
            $this->urls[$url] = true;
        }
    }
    
    //grab all the URLs
    public function saveUrls() {
        $save_timer = new Timer(true);
        
        $this->_echo("=== RETRIEVING " . count($this->urls) . " URLS ===");
        
        $this->rc->get(array_keys($this->urls));
        $this->rc->execute();
        
        $this->_echo("=== URL RETRIEVAL TOOK " . $save_timer->stop(true) . " SECONDS ===");
    }
    
    //called from rolling curl, so has to be public
    public function _saveUrl($resp, $info, $req) {
        $this->_echo("Got {$info['url']}, " . strlen($resp . " bytes") . " bytes in {$info['total_time']} sec");
        
        if($resp && strlen($resp)) {
            //remove http://wordpress.url from the URL to get a usable file path
            $name = str_replace($this->base, '', $info['url']);
            
            $resp = apply_filters(ACTION_PREFIX . 'response_content', $resp, $info);
            $name = apply_filters(ACTION_PREFIX . 'response_name', $name, $info);
            
            $this->storage->write(array(
                'name'    => $name,
                'content' => $resp
            ));
        }
    }
    
    private function _collectUrls() {
        $this->_echo("=== COLLECTING URLS ===");
        $url_timer = new Timer(true);
        
        //call into our export lib
        $urls = export();
        
        foreach($urls as $u) {
            $this->addUrl($u);
        }
        
        ksort($this->urls);
        
        $this->_echo("=== URL COLLECTION TOOK " . $url_timer->stop(true) . " SECONDS ===");
    }
    
    private function _echo($str) {
        $content = str_pad($this->_timer->get(0), 4, "0", STR_PAD_LEFT) . " " . $str;
        
        if($this->wpopts['enable_logging']) {
            error_log($content);
        }
        
        echo $content . "<br />";
        
        //really, REALLY flush the buffer
        ob_flush();
        flush();
    }
}
