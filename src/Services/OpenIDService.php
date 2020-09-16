<?php

namespace CCHits\Services;

use Auth_OpenID_FileStore;
use Auth_OpenID_Consumer;
use Auth_OpenID_AX_FetchRequest;
use Auth_OpenID_AX_AttrInfo;
use Auth_OpenID_SRegRequest;
use Auth_OpenID_AuthRequest;

use CCHits\Config\Config;

/**
 * OpenID service.
 */
class OpenIDService
{
    private Auth_OpenID_Consumer $_consumer;

    /**
     * Constructor.
     * 
     * @param Config $config Injected instance of Configuration.
     */
    public function __construct(Config $config)
    {
        $store = new Auth_OpenID_FileStore($config->openIdStore);
        $this->_consumer = new Auth_OpenID_Consumer($store);
    }

    /**
     * Begin OpenID authentication process.
     * 
     * @param string $endpoint The oAuth endpoint.
     *
     * @return string
     */
    public function begin(string $endpoint) : string 
    {
        /* @var $auth Auth_OpenID_AuthRequest */
        $auth = $this->_consumer->begin($endpoint);
        if (null == $auth) {
            // Fail properly...
        }

        $ax = new Auth_OpenID_AX_FetchRequest();
        $ax->add(
            Auth_OpenID_AX_AttrInfo::make(
                'http://axschema.org/contact/email', 1, true, 'email'
            )
        );
        $auth->addExtension($ax);

        /* @phpstan-ignore-next-line */
        $sreg_request = Auth_OpenID_SRegRequest::build(array(), ['email']);
        /* @phpstan-ignore-next-line */
        $auth->addExtension($sreg_request);

        $url = $auth->redirectURL(
            'http://localhost:4200/admin', 
            'http://localhost:4200/admin' . '?return=1'
        );

        return $url;
    }
}
