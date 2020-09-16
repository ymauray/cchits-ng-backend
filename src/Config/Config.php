<?php

namespace CCHits\Config;

/**
 * Configuration.
 */
class Config
{
    public $openIdStore;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->openIdStore = dirname(__FILE__) . '/../../OPENID_STORE';
    }
}
