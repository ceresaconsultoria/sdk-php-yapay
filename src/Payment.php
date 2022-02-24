<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Yapay;

use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Yapay\Core\YPController;
use Yapay\Exceptions\YPException;

/**
 * Description of Webhook
 *
 * @author weslley
 */
class Payment extends YPController{
    
    public function execute(array $data){        
        try{
            $response = $this->http->post("api/v3/transactions/payment", array(
                "json" => $data
            ));

            $body = (string)$response->getBody();
                        
            return json_decode($body);
            
        } catch (ServerException $ex) {
            
            $body = (string)$ex->getResponse()->getBody();
            
            $bodyDecoded = json_decode($body);
            
            if(isset($bodyDecoded->errorMsg)){
                
                throw YPException::fromObjectMessage($bodyDecoded->errorMsg, $bodyDecoded->code, $ex->getPrevious());
                
            }
            
            
        } catch (ClientException $ex) {
            
            $body = (string)$ex->getResponse()->getBody();
            
            $bodyDecoded = json_decode($body);
            
            if(isset($bodyDecoded->errorMsg)){
                
                throw YPException::fromObjectMessage($bodyDecoded->errorMsg, $bodyDecoded->code, $ex->getPrevious());
                
            }
            
        } catch (BadResponseException $ex) {
            
            $body = (string)$ex->getResponse()->getBody();
            
            $bodyDecoded = json_decode($body);
            
            if(isset($bodyDecoded->errorMsg)){
                
                throw YPException::fromObjectMessage($bodyDecoded->errorMsg, $bodyDecoded->code, $ex->getPrevious());
                
            }
            
        } catch (Exception $ex) {
                 
            throw new YPException($ex);
        
        }
        
    }
    
}
