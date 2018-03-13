<?php
$installer = $this;
$installer->startSetup();

$attribute = array(
    array(       
        'attribute_code'=>'resolution',
        'scope'=>3
    ),
    array(       
        'attribute_code'=>'delivery_status',
        'scope'=>3
    ),
    array(        
        'attribute_code'=>'item_status',
        'scope'=>3
    )
);
$resolution = array(
    array(
        'value'=>'cancel'
    ),
    array( 
        'value'=>'refund'
    )
);

$delivery = array(
    array(
        'value'=>'not delivered'
    ),
    array( 
        'value'=>'delivered'
    )
);

$item = array(
    array(
        'value'=>'canceled'
    ),
    array(
        'value'=>'complete'
    ),
    array(
        'value'=>'payment request'
    ),
    array(
        'value'=>'return received'
    ),
    array(
        'value'=>'processing'
    ),
    array(
        'value'=>'pending'
    ),
);

foreach($attribute as $attr)
{   
    $model = Mage::getModel('rma/rma_eav_attribute')
        ->setData($attr)
        ->save(); 
    $id = $model->getId();
    
    if($attr['attribute_code'] == 'resolution')
    {
        foreach($resolution as $resolutionArray)
        {
            $resolutionArray['attribute_id'] = $id;
            Mage::getModel('rma/rma_eav_attributeoption')
                ->setData($resolutionArray)
                ->save();
        }
        
    }
    elseif($attr['attribute_code'] == 'delivery_status')
    {
        foreach($delivery as $deliveryStatus)
        {
            $deliveryStatus['attribute_id'] = $id;
            Mage::getModel('rma/rma_eav_attributeoption')
                ->setData($deliveryStatus)
                ->save();
        }
    }
    else 
    {
        foreach($item as $itemStatus)
        {
            $itemStatus['attribute_id'] = $id;
            Mage::getModel('rma/rma_eav_attributeoption')
                ->setData($itemStatus)
                ->save();
        }
    }  
}

$installer->endSetup();
?>