<?php 
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
    
    public function sendMail($from,$to,$subject,$body,$link='')
    {
        require_once('phpmailer/class.phpmailer.php');
        $mail = new PHPMailer(); // create a new object
        $mail->IsSMTP(); // enable SMTP
        $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = $from;
        $mail->Password = "";
        $mail->SetFrom($from);
        $mail->Subject = $subject;
        $mail->Body = $body.'<br>'.$link;
        $mail->AddAddress($to);

        if(!$mail->Send()) 
        {
           return false;
        } 
        else 
        {
           return true;
        }
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
?>
