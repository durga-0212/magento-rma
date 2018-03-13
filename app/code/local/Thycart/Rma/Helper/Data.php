<?php
require_once('phpmailer/class.phpmailer.php');
class Thycart_Rma_Helper_Data extends Mage_Core_Helper_Abstract
{     
    public function getAttributeOptionValues($attribute_code) 
    {   
        if(empty($attribute_code))
        {
            return;
        }
        try
        {
            $attribute_data=Mage::getModel('rma/rma_eav_attribute')->getAttributeCollection();
            return $attribute_data[$attribute_code];
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }

    public function orderShipment($orderId=0)
    {
        $shipmentIds = array();
        if(empty($orderId))
        {
            return $shipmentIds;
        }
        try
        {
            $orderObject = Mage::getModel('sales/order')->load($orderId);
            $shipmentIds = $orderObject->getShipmentsCollection()->getAllIds();
            return $shipmentIds;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }
    
    public function getTrackingNumber()
    {
        $digits_needed = 8;
        $random_number = ''; // set up a blank string
        $count = 0; 
        try
        {
            $carriers=$this->getEnabledshippingmethods();         
            $random_number .= $carriers;        
            while ($count < $digits_needed) 
            {
                $random_digit = mt_rand(0, 9);                     
                $random_number .= $random_digit;
                $count++;
            }          
            return $random_number;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }
    
    public function getEnabledshippingmethods()
    {   
        try
        {
            $methods = Mage::getSingleton('shipping/config')->getAllCarriers();    
            foreach ($methods as $code => $carrier) 
            {            
                $carriers[$code] = $carrier->getConfigData('title');           
            }        
            $arr=array();
            foreach($carriers as $key=> $value)
            {
                $arr[$key]=$value.'_'.$key.'_';           
            }
            $k = array_rand($arr);        
            $v = $arr[$k];        
            return $v;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }
    
    public function getTrackingResponse($shipData=array()) 
    {   
        if(empty($shipData))
        {
            return;
        }
        try
        {
            $ordershipdata=Mage::getModel('rma/order')->getshipmentData($shipData);
            return $ordershipdata;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }
    
    public function sendMail($to, $recepientName, $subject, $productName, $message, $link='')
    {   
        if(empty($to) || empty($recepientName) || empty($subject) || empty($productName) || empty($message))
        {   
            return false;
        }
        if(PHP_MAILER)
        { 
            try
            {                
                $mail = new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPDebug = 1; 
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Host = "smtp.gmail.com";
                $mail->Port = 465;
                $mail->IsHTML(true);
                $mail->Username = Mage::getStoreConfig('rma_email/email_group/sender_email');
                $mail->Password = 'rma@1234';
                $mail->SetFrom(Mage::getStoreConfig('rma_email/email_group/sender_email'));
                $mail->Subject = $subject;
                $mail->Body = Mage::helper('rma')->getEmailBody($productName, $message ,$link);
                $mail->AddAddress($to);
            }
            catch (Exception $e)
            {
                echo 'tested';
                echo $e->getMessage();
                return false;
            }
            
            if(!$mail->Send()) 
            {
               return false;
            }   
        }
        if(MAGENTO_MODEL)
        {
            $mail = Mage::getModel('core/email');
            $mail->setToName($recepientName);
            $mail->setToEmail($to);
            $mail->setBody(Mage::helper('rma')->getEmailBody($productName, $message ,$link));
            $mail->setSubject($subject);
            $mail->setFromEmail(Mage::getStoreConfig('rma_email/email_group/sender_email'));
            $mail->setType('html');
            try
            {
                if(!$mail->send())
                {
                    return false;
                }
            }
            catch (Exception $e)
            {
                Mage::getSingleton('core/session')->addError('Unable to send.');
                return false;
            }   
        }
        if(ZEND_FUNCTION)
        {
            $sender_email = Mage::getStoreConfig('rma_email/email_group/sender_email');
            $sender_name  = Mage::getStoreConfig('rma_email/email_group/sender_name');

            $mail = new Zend_Mail();
            $mail->setBodyHtml(Mage::helper('rma')->getEmailBody($productName, $message ,$link)); 
            $mail->setFrom(Mage::getStoreConfig('rma_email/email_group/sender_email'),Mage::getStoreConfig('rma_email/email_group/sender_name'));
            $mail->addTo($to, 'customer');
            //$mail->addCc($cc, $ccname);    //can set cc
            //$mail->addBCc($bcc, $bccname);    //can set bcc
            $mail->setSubject($subject);
            try
            {
                if(!$mail->send())
                {
                    return false;
                }
            }
            catch(Exception $ex)
            {
                echo $ex->getMessage();
                return;
            }
        }
        if(Mage::getStoreConfig('rma_email/email_group/transactional_email'))
        {
            $templateId     = 1;     
            $senderName     = Mage::getStoreConfig('rma_email/email_group/sender_name');
            $senderEmail    = Mage::getStoreConfig('rma_email/email_group/sender_email');        
            $sender         = array('name' => $senderName,
                                    'email' => $senderEmail
                                   );              
            $storeId = Mage::app()->getStore()->getId();
            $vars    = array('message' => $message,
                             //'orderid'=> $orderid,
                             'productDetails'=>$productName
                            );
            
            if(!empty($link))
            {
                $vars['link'] = $link;
            }
            try
            {
                $translate  = Mage::getSingleton('core/translate');
                Mage::getModel('core/email_template')
                        ->sendTransactional($templateId, $sender, $to, $recepientName, $vars, $storeId);    
                $translate->setTranslateInline(true);   
            }
            catch (Exception $e)
            {
                echo $e->getMessage();
                return false;
            }
        }     
        return true;
    }

    public function getEmailBody($productName, $message , $link='')
    { 
        $body ='';
        $emailTemplateVars = array();
        if(empty($productName))
        {
            return false;
        }
        if(!empty($link))
        {
            $emailTemplateVars['link'] = $link;
        }
        $emailTemplateVars['product_details'] = $productName;
        $emailTemplateVars['message'] = $message;
        try
        {
            $emailTemplate  = Mage::getModel('core/email_template')
                            ->loadDefault('rma_template');
            $body = $emailTemplate->getProcessedTemplate($emailTemplateVars);
        }
        catch(Exception $e)
        {
           echo $e->getMessage(); 
           return;
        }
        
       return $body;
    }
    public function updateInventory($productId,$qtyApproved)
    {
        if(empty($productId) || empty($qtyApproved))
        {
            return;
        }
        try
        {
            $inventoryModel = Mage::getModel('cataloginventory/stock_item')->load($productId);
            $originalQty = $inventoryModel->getQty();
            $updatedQty = $originalQty+$qtyApproved;
            $inventoryModel->addData(array('qty'=>$updatedQty));
            $successInventory = $inventoryModel->save();
            return $successInventory;            
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }

    public function encryptBankDetail($data)
    {
        if(empty($data))
        {
            return false;
        }
        $key        = hash('sha256',KEY);
        $iv         = substr(hash('sha256', IV), 0, 16);
        $encrypted  = openssl_encrypt($data, ENCRYPTMETHOD, $key, 0, $iv);
        $encrypted  = base64_encode($encrypted);
        return $encrypted;
    }

    public function decryptBankDetail($data)
    {
        if(empty($data))
        {
            return false;
        }
        $key        = hash('sha256',KEY);
        $iv         = substr(hash('sha256', IV), 0, 16);
        $decrypted  = openssl_decrypt(base64_decode($data),ENCRYPTMETHOD , $key, 0, $iv);
        return $decrypted;
    }
}
