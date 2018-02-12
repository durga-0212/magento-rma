<?php
class Thycart_Shipdesk_Adminhtml_ShiplabelController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$shipmentId = $this->getRequest()->getParam('shipment_id');

		if( !empty($shipmentId) ){
			$shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);

			$getShipmentAllTrackData = $shipment->getAllTracks();

			$pdfFileName = '';
			foreach ($getShipmentAllTrackData as $singleTrack) {
				$pdfFileName = $singleTrack->getDescription();
			}
			
			if( !empty($pdfFileName) ){
				$filePath = Mage::getBaseDir('var'). DS  . 'shiplabel' . DS  . $pdfFileName;
				if (file_exists($filePath)) {
				    header('Content-Description: File Transfer');
				    header('Content-Type: application/octet-stream');
				    header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
				    header('Expires: 0');
				    header('Cache-Control: must-revalidate');
				    header('Pragma: public');
				    header('Content-Length: ' . filesize($filePath));
				    readfile($filePath);
				}
			}
		}
	}
}