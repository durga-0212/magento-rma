<?php

class Thycart_Rma_Model_Rma_Status extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Status constants
     */
    const STATE_PENDING         = 'pending';
    const STATE_PROCESSING      = 'processing';
    const STATE_RETURN_RECEIVED = 'return received';
    const STATE_CANCELED        = 'canceled';
    const STATE_PAYMENT_REQUEST = 'payment request';
    const STATE_COMPLETE        = 'complete';
    const STATE_CLOSED          = 'closed';
    const STATE_REJECTED        = 'rejected';
   
}
