<?php

class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Details
    extends Mage_Adminhtml_Block_Widget_Form
{
    
    
    public function getRmaData($field)
    {
        $model = Mage::registry('rma_data');
        if ($model) {
            return $model->getData($field);
        } else {
            return null;
        }
    }
    
    
    /**
     * Get order link (href address)
     *
     * @return string
     */
    public function getOrderLink()
    {
        $order_id=$this->getRmaData('order_id');
        $url = Mage::getBaseUrl().'admin/sales_order/view/order_id/'.$order_id;
        
        return $url;
    }

    
    /**
     * Get Link to Customer's Page
     *
     * Gets address for link to customer's page.
     * Returns null for guest-checkout orders
     *
     * @return string|null
     */
    public function getCustomerLink()
    {
        if ($this->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return $this->getUrl('*/customer/edit', array('id' => $this->getOrder()->getCustomerId()));
    }

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->escapeHtml($this->getOrder()->getCustomerEmail());
    }

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getCustomerContactEmail()
    {
        return $this->escapeHtml($this->getRmaData('customer_custom_email'));
    }

}
