<?php
class Thycart_Shipdesk_Block_Adminhtml_Price extends Mage_Adminhtml_Block_Template
{
    public function getRates()
    {
        $rateData = json_decode($this->getRateData(),true);
        return $rateData;
    }
}
