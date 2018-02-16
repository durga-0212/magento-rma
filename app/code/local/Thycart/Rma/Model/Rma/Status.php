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
    /**
     * Get state label based on the code
     *
     * @param string $state
     * @return string
     */
    public function getItemLabel($state)
    {
        switch ($state) {
            case self::STATE_PENDING:            return Mage::helper('rma')->__('Pending');
            case self::STATE_AUTHORIZED:         return Mage::helper('rma')->__('Authorized');
            case self::STATE_PARTIAL_AUTHORIZED: return Mage::helper('rma')->__('Partially Authorized');
            case self::STATE_RECEIVED:           return Mage::helper('rma')->__('Return Received');
            case self::STATE_RECEIVED_ON_ITEM:   return Mage::helper('rma')->__('Return Partially Received');
            case self::STATE_APPROVED:           return Mage::helper('rma')->__('Approved');
            case self::STATE_APPROVED_ON_ITEM:   return Mage::helper('rma')->__('Partially Approved');
            case self::STATE_REJECTED:           return Mage::helper('rma')->__('Rejected');
            case self::STATE_REJECTED_ON_ITEM:   return Mage::helper('rma')->__('Partially Rejected');
            case self::STATE_DENIED:             return Mage::helper('rma')->__('Denied');
            case self::STATE_CLOSED:             return Mage::helper('rma')->__('Closed');
            case self::STATE_PROCESSED_CLOSED:   return Mage::helper('rma')->__('Processed and Closed');
            default: return $state;
        }
    }

    /**
     * Get RMA status by array of items status
     *
     * First function creates correspondence between RMA Item statuses and numbers
     * I.e. pending <=> 0, authorized <=> 1, and so on
     * Then it converts array with unique item statuses to "bitmask number"
     * according to mentioned before numbers as a bits
     * For Example if all item statuses are "pending", "authorized", "rejected",
     * then "bitmask number" = 2^0 + 2^1 + 2^5 = 35
     * Then function builds correspondence between these numbers and RMA's statuses
     * and returns it
     *
     * @param array $itemStatusArray Array of RMA items status
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getStatusByItems($itemStatusArray)
    {
        if (!is_array($itemStatusArray) || empty($itemStatusArray)) {
            Mage::throwException(Mage::helper('rma')->__('Wrong RMA item status.'));
        }

        $itemStatusArray = array_unique($itemStatusArray);

        $itemStatusModel = Mage::getModel('rma/item_attribute_source_status');

        foreach ($itemStatusArray as $status) {
            if (!$itemStatusModel->checkStatus($status)) {
                Mage::throwException(Mage::helper('rma')->__('Wrong RMA item status.'));
            }
        }

        $itemStatusToBits = array(
            Enterprise_Rma_Model_Item_Attribute_Source_Status::STATE_PENDING => 1,
            Enterprise_Rma_Model_Item_Attribute_Source_Status::STATE_AUTHORIZED => 2,
            Enterprise_Rma_Model_Item_Attribute_Source_Status::STATE_DENIED => 4,
            Enterprise_Rma_Model_Item_Attribute_Source_Status::STATE_RECEIVED => 8,
            Enterprise_Rma_Model_Item_Attribute_Source_Status::STATE_APPROVED => 16,
            Enterprise_Rma_Model_Item_Attribute_Source_Status::STATE_REJECTED => 32,
        );
        $rmaBitMaskStatus = 0;
        foreach ($itemStatusArray as $status) {
            $rmaBitMaskStatus += $itemStatusToBits[$status];
        }

        if ($rmaBitMaskStatus == 1) {
            return self::STATE_PENDING;
        } elseif ($rmaBitMaskStatus == 2) {
            return self::STATE_AUTHORIZED;
        } elseif ($rmaBitMaskStatus == 4) {
            return self::STATE_CLOSED;
        } elseif ($rmaBitMaskStatus == 5) {
            return self::STATE_PENDING;
        } elseif (($rmaBitMaskStatus > 2) && ($rmaBitMaskStatus < 8)) {
            return self::STATE_PARTIAL_AUTHORIZED;
        } elseif ($rmaBitMaskStatus == 8) {
            return self::STATE_RECEIVED;
        } elseif (($rmaBitMaskStatus >= 9) && ($rmaBitMaskStatus <= 15)) {
            return self::STATE_RECEIVED_ON_ITEM;
        } elseif ($rmaBitMaskStatus == 16) {
            return self::STATE_PROCESSED_CLOSED;
        } elseif ($rmaBitMaskStatus == 20) {
            return self::STATE_PROCESSED_CLOSED;
        } elseif (($rmaBitMaskStatus >= 17) && ($rmaBitMaskStatus <= 31)) {
            return self::STATE_APPROVED_ON_ITEM;
        } elseif ($rmaBitMaskStatus == 32) {
            return self::STATE_CLOSED;
        } elseif ($rmaBitMaskStatus == 36) {
            return self::STATE_CLOSED;
        } elseif (($rmaBitMaskStatus >= 33) && ($rmaBitMaskStatus <= 47)) {
            return self::STATE_REJECTED_ON_ITEM;
        } elseif ($rmaBitMaskStatus == 48) {
            return self::STATE_PROCESSED_CLOSED;
        } elseif ($rmaBitMaskStatus == 52) {
            return self::STATE_PROCESSED_CLOSED;
        } elseif (($rmaBitMaskStatus > 48)) {
            return self::STATE_APPROVED_ON_ITEM;
        } else {
            return self::STATE_PENDING;
        }
    }

    /**
     * Get available states keys for entities
     *
     * @return array
     */
    protected function _getAvailableValues()
    {
        return array(
            self::STATE_PENDING,
            self::STATE_AUTHORIZED,
            self::STATE_PARTIAL_AUTHORIZED,
            self::STATE_RECEIVED,
            self::STATE_RECEIVED_ON_ITEM,
            self::STATE_APPROVED_ON_ITEM,
            self::STATE_REJECTED_ON_ITEM,
            self::STATE_CLOSED,
            self::STATE_PROCESSED_CLOSED,
        );
    }

    /**
     * Get button disabled status
     *
     * @param string $status
     * @return bool
     */
    public function getButtonDisabledStatus($status)
    {
        if (
            in_array(
                $status,
                array(
                    self::STATE_PARTIAL_AUTHORIZED,
                    self::STATE_RECEIVED,
                    self::STATE_RECEIVED_ON_ITEM,
                    self::STATE_APPROVED_ON_ITEM,
                    self::STATE_REJECTED_ON_ITEM,
                    self::STATE_CLOSED,
                    self::STATE_PROCESSED_CLOSED,
                )
            )
        ) {
           return true;
        }
        return false;
    }
}
