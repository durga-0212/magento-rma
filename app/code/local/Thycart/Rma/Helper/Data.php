<?php
require_once('phpmailer/class.phpmailer.php');
class Thycart_Rma_Helper_Data extends Mage_Core_Helper_Abstract
{     
    public function getAttributeOptionValues($attribute_code) 
    {
        $attribute_data=Mage::getModel('rma/rma_eav_attribute')->getAttributeCollection();
        return $attribute_data[$attribute_code];
    }
    
    public function orderInvoices($orderId=0)
    {
        $invoiceIds = array();
        if(empty($orderId))
        {
            return $invoiceIds;
        }

        $orderObject = Mage::getModel('sales/order')->load($orderId);
        $invoiceIds = $orderObject->getInvoiceCollection()->getAllIds();
        return $invoiceIds;
    }

    public function orderShipment($orderId=0)
    {
        $shipmentIds = array();
        if(empty($orderId))
        {
            return $shipmentIds;
        }

        $orderObject = Mage::getModel('sales/order')->load($orderId);
        $shipmentIds = $orderObject->getShipmentsCollection()->getAllIds();
        return $shipmentIds;
    }
    
    public function getTrackingNumber()
    {
        $digits_needed = 8;
        $random_number = ''; // set up a blank string
        $count = 0;  
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
    
    public function getEnabledshippingmethods()
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
    
    public function getTrackingResponse($shipData=array()) 
    {    
        $ordershipdata=Mage::getModel('rma/order')->getshipmentData($shipData);
        return $ordershipdata;    
    }
    
    public function sendMail($to, $recepientName, $subject, $orderid, $productName, $message, $link='')
    {
        if(empty($to) || empty($recepientName) || empty($subject) ||empty($orderid) || empty($productName) || empty($message))
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
                $mail->Password = 'shukla0912';
                $mail->SetFrom(Mage::getStoreConfig('rma_email/email_group/sender_email'));
                $mail->Subject = $subject;
                $body = $message;
                $mail->Body = Mage::helper('rma')->getEmailBody($orderid, $productName, $message ,$link);
                $mail->AddAddress($to);
            }
            catch (Exception $e)
            {
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
            $mail->setBody(Mage::helper('rma')->getEmailBody($orderid, $productName, $message ,$link));
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
            $sender_email = 'ritesh.shukla@adapty.com';
            $sender_name = "sender name";

            $mail = new Zend_Mail();
            $mail->setBodyHtml(Mage::helper('rma')->getEmailBody($orderid, $productName, $message ,$link)); 
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
                die("Error sending mail to $to,$error_msg");
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
                             'orderid'=> $orderid,
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

    public function getEmailBody($orderid, $productName, $message , $link='')
    { 
        $body ='';
        $emailTemplateVars = array();
        if(empty($orderid) || empty($productName))
        {
            return false;
        }
        if(!empty($link))
        {
            $emailTemplateVars['link'] = $link;
        }
        $emailTemplateVars['product_details'] = $productName;
        $emailTemplateVars['orderid'] = $orderid;
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
        $inventoryModel = Mage::getModel('cataloginventory/stock_item')->load($productId);
        $backOrders = $inventoryModel->getBackorders();
        $originalQty = $inventoryModel->getQty();
        $updatedQty = $originalQty+$qtyApproved;
        if($backOrders == 0 || $originalQty>=0)
        {
            $inventoryModel->addData(array('qty'=>$updatedQty));
            $successInventory = $inventoryModel->save();
            return $successInventory;
        }
    }
    
}
