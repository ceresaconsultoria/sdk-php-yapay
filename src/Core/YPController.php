<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Yapay\Core;

use Yapay\Entity\YPToken;

/**
 * Description of MSController
 *
 * @author weslley
 */
class YPController extends YPHttp{
    protected YPToken $token;
    
    public function __construct(array $config = []) {        
        parent::__construct($config);
    }
    
    public function setToken(YPToken $token){
        $this->token = $token;
        return $this;
    }
    
    public function getToken() : YPToken{
        return $this->token;
    }
}
