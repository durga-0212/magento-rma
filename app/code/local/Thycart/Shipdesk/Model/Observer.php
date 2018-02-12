<?php
class Thycart_Shipdesk_Model_Observer
{
	public function getRates($observer) 
	{
		$block = $observer->getBlock();
	  	$order_id = $observer->getEvent()->getData('order_id');
	  	$sales = Mage::getModel('sales/order')->load($order_id);

	  	//Parameteres For Get Rate URL - Start
	  	$isCOD = (strtolower($sales->getPayment()->getMethod()) == 'msp_cashondelivery' ? 'Yes' : 'No');
	  	$fromCountry = urlencode(strtoupper(Mage::getStoreConfig('shipdesksettings/address/ship_country')));
	  	$flag = $sales->getShippingAddress()->getData('country_id');
	  	$toCountry = urlencode(( !empty($flag) ? strtoupper($sales->getShippingAddress()->getData('country_id')) : 'IN' ));
	  	$fromPin = Mage::getStoreConfig('shipdesksettings/address/ship_zip');
	  	$toPin = $sales->getShippingAddress()->getData('postcode');
	  	$weight = (int)$sales->getWeight();

	  	if( empty($weight) ){
	  		$weight = 1;
	  	}
	  	//Convert into KG - Start
	  	$weight = ( $weight/1000 );
	  	//Convert into KG - End

	  	//Parameteres For Get Rate URL - End

	  	$getRates = Mage::getStoreConfig('shipdesksettings/urloptions/get_rate')."?unit=KG&need_cod=".$isCOD."&country=".$fromCountry."&to_country=".$toCountry."&pin=".$fromPin."&to_pin=".$toPin."&weight=".$weight;

	  	Mage::log('shipdesk quick rate URL: '.$getRates);
		try{   
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$getRates);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			$shippingData = curl_exec($ch);
			Mage::log('shipdesk quick rate: '.$shippingData);
			curl_close($ch);
			Mage::app()->getLayout()->getBlock('shipment_pricing')->setRateData($shippingData);
			return $this;
		}catch (Exception $e){
			Mage::logException('shipdesk: '.$e);
			throw $e;
		}
	}
	  
	public function createShipment($observer)
	{
	  	$orderId = $observer->getEvent()->getData('order_id');
	  	$data = $observer->getEvent()->getData('data');
	  	$sales = Mage::getModel('sales/order')->load($orderId);

	  	//Parameteres For Create Shipment URL - Start
	  	$merchantId = Mage::getStoreConfig('shipdesksettings/basicsettings/merchant_id');

	  	$shipId = urlencode($sales->getData('increment_id'));
	  	
	  	$fromPin = Mage::getStoreConfig('shipdesksettings/address/ship_zip');
	  	$fromStreet = urlencode(Mage::getStoreConfig('shipdesksettings/address/ship_street_address_line1').' '.Mage::getStoreConfig('shipdesksettings/address/ship_street_address_line2'));
	  	$fromCity = urlencode(strtoupper(Mage::getStoreConfig('shipdesksettings/address/ship_city')));
	  	$fromState = urlencode(strtoupper(Mage::getStoreConfig('shipdesksettings/address/ship_state')));
	  	$fromCountry = urlencode(strtoupper(Mage::getStoreConfig('shipdesksettings/address/ship_country')));

	  	$address = $sales->getShippingAddress()->getData();
	  	$company = urlencode($address['company']);
	  	$country = urlencode($address['country_id']);
	  	$street = urlencode($address['street']);
	  	$city = urlencode($address['city']);
	  	$state = urlencode($address['region']);
	  	$pin = urlencode($address['postcode']);
	  	$phone = urlencode($address['telephone']);
	  	$name = urlencode($address['firstname'].$address['lastname']);
	  	$email = $sales->getCustomerEmail();

	  	$payment = $sales->getPayment();
	  	$cod = ( strtolower($payment->getMethod()) == 'msp_cashondelivery' ) ? 'Yes' : 'No';
	  	
	  	$items = $sales->getAllItems();
	  	$shipItem = array();
	  	$i = 0;
	  	foreach($items as $item){
	  		$shipItem[$i] = $item->getData();
	  		$i++;
	  	}
	  	
	  	$productName = urlencode(substr($shipItem[0]['name'],0,25));
	  	$quantity = $shipItem[0]['qty_ordered'];
	  	$price = $shipItem[0]['price'];
	  	$weight = (int)$shipItem[0]['weight'];
	  	if( empty($weight) ){
	  		$weight = 1;
	  	}

	  	//Convert into KG - Start
	  	$weight = ( $weight/1000 );
	  	//Convert into KG - End

	  	$shipName = urlencode(Mage::getStoreConfig('shipdesksettings/address/ship_name'));
	  	$shipCompany = urlencode(Mage::getStoreConfig('shipdesksettings/address/ship_company'));
	  	$shipPhone = urlencode(Mage::getStoreConfig('shipdesksettings/address/ship_phone'));
	  	$shipEmail = urlencode(Mage::getStoreConfig('shipdesksettings/address/ship_email'));

	  	date_default_timezone_set('Asia/Kolkata');
	  	$pickupDate = ( date('H') < 15 ) ? date('Y-m-d') : date('Y-m-d', strtotime('+1 day'));

	  	$shippingAmount = $sales->getShippingAmount();
	  	$shippingAmount = ( !empty($shippingAmount) ? $shippingAmount : 0 );

	  	$cashOnDeliveryAmount = ( $cod == 'Yes' ) ? $sales->getMspCashondeliveryInclTax() : 0;
	  	$cashOnDeliveryAmount = ( !empty($cashOnDeliveryAmount) ? $cashOnDeliveryAmount : 0 );

	  	$taxAmount = (int)$sales->getTaxAmount();
	  	$taxAmount = ( !empty($taxAmount) ? $taxAmount : 0 );

	  	$logisticCost = 0;
	  	$logisticCost = $shippingAmount + $cashOnDeliveryAmount;

	  	//Parameteres For Create Shipment URL - End
	  	
		$createShipmentApi = Mage::getStoreConfig('shipdesksettings/urloptions/create_shipment')."?id=".$merchantId."&name=".$shipName."&description=".$productName."&company=".$shipCompany."&country=".$fromCountry."&street_lines=".$fromStreet."&city=".$fromCity."&state=".$fromState."&pin=".$fromPin."&phone=".$shipPhone."&email=".$shipEmail."&pick_up=Yes&pickup_date=".$pickupDate."&need_cod=".$cod."&to_name=".$name."&to_company=".$company."&to_country=".$country."&to_street_lines=".$street."&to_city=".$city."&to_state=".$state."&to_pin=".$pin."&to_phone=".$phone."&to_email=".$email."&ship_id=".$shipId."&quantity=".$quantity."&quantity_unit=PCS&weight=".$weight."&weight_unit=KG&price=".$price."&custom_value=INR&made_in=IN&logistic_cost=".$logisticCost."&tax_type=VAT&tax_amount=".$taxAmount."&reference_id=".$shipId;

		Mage::log('shipdesk create shipment URL: '.$createShipmentApi);
  		try{   
			$ch1 = curl_init();
			curl_setopt($ch1,CURLOPT_URL,$createShipmentApi);
			curl_setopt($ch1,CURLOPT_RETURNTRANSFER,true);
			$createShipmentData = curl_exec($ch1);
			Mage::log('shipdesk create shipment: '.$createShipmentData);
			curl_close($ch1);
			if( count($shipItem) > 1 ){
		    	for( $i=1; $i<count($shipItem); $i++ ){
		    		$ch2 = curl_init();

		    		//Parameteres For Add Item in Shipment URL - Start
		    		$name = urlencode(substr($shipItem[$i]['name'],0,25));
				  	$productName = urlencode(substr($shipItem[$i]['name'],0,25));
				  	$quantity = $shipItem[$i]['qty_ordered'];
				  	$price = $shipItem[$i]['price'];
				  	$weight = (int)$shipItem[$i]['weight'];
				  	if( empty($weight) ){
				  		$weight = 1;
				  	}

				  	$weight = ( $weight/1000 );

				  	//Parameteres For Add Item in Shipment URL - End

				  	$addShipmentApi = Mage::getStoreConfig('shipdesksettings/urloptions/add_item')."?ship_id=".$shipId."&name=".$name."&description=".$productName."&quantity=".$quantity."&quantity_unit=PCS&weight=".$weight."&weight_unit=KG&price=".$price."&custom_value=INR&made_in=IN";
				  	curl_setopt($ch2,CURLOPT_URL,$addShipmentApi);
				  	curl_setopt($ch2,CURLOPT_RETURNTRANSFER,true);
				  	$addShipmentData = curl_exec($ch2);
				  	Mage::log('shipdesk add item:'.$addShipmentData);
				  	curl_close($ch2);
				}
			}
			
			$responseData = json_decode($createShipmentData, true);

			$trackIdResponse = array();

			if( !empty($responseData) ){
				if( !empty($responseData['ship_id']) ){
					$rate = '';

					$shipData = $responseData['ship'];
					foreach ($shipData as $key => $value) {
						if( $value['Service'] == $data['shipping_service'] ){
							$rate = ( $value['Rate'] != 'Service Not Available!' ? $value['Rate'] : '' );
						}
					}

					if( $rate != '' ){
						$trackingIdShipmentApi = Mage::getStoreConfig('shipdesksettings/urloptions/place_shipment')."?ship_id=".$responseData['ship_id']."&service_type=".urlencode($data['shipping_service'])."&rates=".$rate;

						Mage::log('shipdesk ship label URL: '.$trackingIdShipmentApi);

						$ch3 = curl_init();
						curl_setopt($ch3,CURLOPT_URL,$trackingIdShipmentApi);
						curl_setopt($ch3,CURLOPT_RETURNTRANSFER,true);
						$trackIdResponse = curl_exec($ch3);
						Mage::log('shipdesk ship label:'.$trackIdResponse);
						
						curl_close($ch3);

						$trackIdResponse = json_decode($trackIdResponse,true);
						
						if( !empty($trackIdResponse) ){
							if( !empty($trackIdResponse['tracking_number']) ){
								$href = '';
								libxml_use_internal_errors(true);
								$dom = new DOMDocument();
								$dom->loadHTML($trackIdResponse['tracking_id']);
								foreach( $dom->getElementsByTagName('a') as $node ){
									$href = $node->getAttribute( 'href');
								}

								unset($dom);

								$varFolderPath = Mage::getBaseDir('var');
								if( !file_exists($varFolderPath . DS  . 'shiplabel') ){
									mkdir($varFolderPath . DS  . 'shiplabel', 0777, true);
								}

								$pdfFileName = md5(( rand(10000, 99999). $responseData['ship_id'] ));
								$ch = curl_init($trackIdResponse['path']);
								$fp = fopen($varFolderPath . DS  . 'shiplabel' . DS  . $pdfFileName .'.pdf', 'wb');
								curl_setopt($ch, CURLOPT_FILE, $fp);
								curl_setopt($ch, CURLOPT_HEADER, 0);
								curl_exec($ch);
								curl_close($ch);
								fclose($fp);
								
								$trackIdResponse['isSuccess'] = true;
								$trackIdResponse['track_number'] = $trackIdResponse['tracking_number'];
								$trackIdResponse['track_url'] = $href;
								$trackIdResponse['carrier_code'] = $data['shipping_service'];
								$trackIdResponse['title'] = $data['shipping_service'];
								$trackIdResponse['description'] = $pdfFileName.'.pdf';
							}elseif( !empty($trackIdResponse['meta']) ){
								if( !empty($trackIdResponse['data']['inputs']) ){
									$trackIdResponse['isSuccess'] = false;
									$trackIdResponse['errorMessage'] = 'Ship Label: '.$trackIdResponse['data']['inputs'];
								}
							}
						}else{
							$trackIdResponse['isSuccess'] = false;
							$trackIdResponse['errorMessage'] = 'Something went wrong with create ship label. Please contact site administrator.';
						}
					}
				}elseif( !empty($responseData['meta']) ){
					if( !empty($responseData['data']['inputs']) ){
						$trackIdResponse['isSuccess'] = false;
						$trackIdResponse['errorMessage'] = 'Create Shipment: '.$responseData['data']['inputs'];
					}
				}
			}else{
				$trackIdResponse['isSuccess'] = false;
				$trackIdResponse['errorMessage'] = 'Something went wrong with create shipment. Please contact site administrator.';
			}

			Mage::register('trackingDetails', $trackIdResponse);
			return $this;
		}catch (Exception $e){
			Mage::logException('shipdesk: '.$e);
			throw $e;
		}
  	}
}
?>