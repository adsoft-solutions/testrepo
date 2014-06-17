<?php 
error_reporting(E_ALL); 
ini_set('display_errors',1);

include_once "class/AdsoftXml.php";
$xml = new AdsoftXml();


$products = array();

$mysqli = $xml->getConnection();
$result = $mysqli->query("SELECT * FROM jos_virtuemart_orders WHERE xml='0'");


if($result) {
	$x = array();
	$rez = null;
	
	while ($row = $result->fetch_object()){
		foreach ( $row as $key=>$value ) {
			$x[$key] = $value;
		}
		$rez[] = $x;
	}	
 	//	$result->close();
	// $mysqli->next_result();
}


if (is_array($rez)) {

	$x = null;
	$xy = null;
	foreach ( $rez as $key=>$value ) {
		$result2 = $mysqli->query("SELECT * FROM jos_virtuemart_order_userinfos WHERE virtuemart_order_id='".$value['virtuemart_order_id']."'");
		if($result2)
		{
			while ($row = $result2->fetch_object())
			{
				foreach ( $row as $key2=>$value2 )
				{
					$x[$key2] = $value2;
				}

			}
		//	$mysqli->next_result();
			$rez[$key]['info'] = $x;
		}
			
	}
	//header ("Content-Type:text/xml");
	$rezX = '<?xml version="1.0" encoding="utf-8"?>';
	$rezX .= "<Document>";
	$rezX .= "<SenderID>BE0405978157</SenderID>";
	$rezX .= "<ReceiverID>BE0405978157</ReceiverID>";
	$rezX .= "<TestIndicator>P</TestIndicator>";
	$rezX .= "<ExternalVersion>CP1_0_DataModel</ExternalVersion>";
	$rezX .= "<InternalVersion>V1</InternalVersion>";
	$rezX .= "<Orders>";
	
	foreach ( $rez as $key=>$value ) {			
		//getting the products assigned to this order
		$result2_ = $mysqli->query("SELECT * FROM jos_virtuemart_order_items WHERE virtuemart_order_id='".$value['virtuemart_order_id']."'");
		if($result2_)
		{
			while ($row_ = $result2_->fetch_object())
			{
				foreach ( $row_ as $key2_=>$value2_ )
				{
					$xy[$key2_] = $value2_;
				}
				array_push($products, $xy);

			}
		//	$mysqli->next_result();
		}

		$orderDate = explode(" ", $value['created_on']);

		$rezX .= "<Order>";
		$rezX .= "<MessageType>ORD</MessageType>";
		$rezX .= "<MessageFunction>ORI</MessageFunction>";

		if ($value['virtuemart_paymentmethod_id'] == 2)	{
			$paym = 1;
		}
		elseif ($value['virtuemart_paymentmethod_id'] == 3)	{
			$paym = 2;
		}
		else {
			$paym = 0;
		}

		if ($value['virtuemart_shipmentmethod_id'] == 2) {
			$shipm = 1;
		}
		elseif ($value['virtuemart_shipmentmethod_id'] == 4) {
			$shipm = 2;
		}
		else {
			$shipm = 0;
		}

		$rezX .= "<GeneralInformation>".$paym."/".$shipm."</GeneralInformation>";
		$rezX .= "<OrderDate>".$orderDate[0]."</OrderDate>";

		$customer_note = null;
		$customer_note = explode(".", $value['customer_note']);
		//$reference_comment = trim(Unaccent($customer_note[0]));
		$reference_comment = $xml->Unaccent(utf8_encode($customer_note[0]));

		if (!empty($reference_comment) && $reference_comment != '') {
			$rezX .= "<References>";
			$rezX .= "<Reference>";
			$rezX .= "<ReferenceType>POR</ReferenceType>";
			$rezX .= "<ReferenceNumber>".$reference_comment."</ReferenceNumber>";
			$rezX .= "</Reference>";
			$rezX .= "</References>";
		}
		else {
			$rezX .= "<References>";
			$rezX .= "<Reference>";
			$rezX .= "<ReferenceType>POR</ReferenceType>";
			$rezX .= "<ReferenceNumber />";
			$rezX .= "</Reference>";
			$rezX .= "</References>";
		}



		$rezX .= "<Partners>
									<Partner>
										<PartnerType>BY</PartnerType>
										<PartnerID>AD-".$value['info']['virtuemart_user_id']."</PartnerID>
										<PartnerIDType>NAM</PartnerIDType>
										<Name>".$xml->Unaccent($value['info']['company'])."</Name>
										<StreetName>".$xml->Unaccent($value['info']['address_1'])."</StreetName>
										<PostalCode>".$value['info']['zip']."</PostalCode>
										<City>".$value['info']['city']."</City>
										<Country>".$xml->getCountry($value['info']['virtuemart_country_id'])."</Country>
										<CustomerGroup>Adsoft</CustomerGroup>
									</Partner>
									<Partner>
										<PartnerType>DP</PartnerType>
										<PartnerID>PXCL117</PartnerID>
										<PartnerIDType>NAM</PartnerIDType>
										<Name>Aan Tafel - partyline</Name>
										<StreetName>Brusselsesteenweg 810</StreetName>
										<PostalCode>1731</PostalCode>
										<City>Zellik</City>
										<Country>BE</Country>
										<CustomerGroup>Adsoft</CustomerGroup>
									</Partner>
									<Create>true</Create>
									<Update>true</Update>
								</Partners>";
		$rezX .= "<OrderItems>";


			

		foreach ($products as $p) {
			$custom_f_ = null;
			$custom_f_ = json_decode($p['product_attribute'],true);
			$custom_fields_array = $xml->array_flatten($custom_f_);

			$kb = $custom_fields_array['0'];

			if (!empty($kb)) {
				$kies_broodje = strip_tags($kb);
				$kies_broodje = str_replace("Kies broodje", "", $kies_broodje);
			}
			else {
				$kies_broodje = "";
			}
			$kies_b_unaccented = $xml->Unaccent($kies_broodje);
			
			$sup = null;
			$naampersoon = $xml->Unaccent($custom_fields_array['naampersoon']);
			$opmerking = $xml->Unaccent($custom_fields_array['opmerking']);

			if (is_array($custom_fields_array)) {
				foreach ($custom_fields_array as $k_cfa=>$v_cfa){				
					if (strpos($k_cfa, 'custom_checkbox') !== false && strpos($v_cfa, 'empty12345') === false)	{					
						$sup[] = $xml->Unaccent($v_cfa);
					}
				}
			}
			$rezX .= "<OrderItem>";
			$rezX .= "<SupplierArticleNumber>".$p['order_item_sku']."</SupplierArticleNumber>";
			$rezX .= "<LongDescription>".$xml->Unaccent($p['order_item_name']) . $kies_b_unaccented ? " ". $kies_b_unaccented : "" . "</LongDescription>";
			$rezX .= "<RequestedDeliveryDate>".$xml->getDeliveryDate($value['virtuemart_order_id'],'delivery_date')."</RequestedDeliveryDate>";

			$rezX .= "<Quantities>";
			$rezX .= "<Quantity>";
			$rezX .= "<QuantityType>ORD</QuantityType>";
			$rezX .= "<Amount>".$p['product_quantity']."</Amount>";
			$rezX .= "<UnitOfMeasure>PCE</UnitOfMeasure>";
			$rezX .= "</Quantity>";
			$rezX .= "</Quantities>";
			$rezX .= "<ExtraElements>";
				
				
			if (!empty($naampersoon)) {
				$rezX .= "<ExtraElement>";
				$rezX .= "<Name>Info1</Name>";
				$rezX .= "<Datatype>AMemo</Datatype>";
				$rezX .= "<Value>".trim($naampersoon)."</Value>";
				$rezX .= "</ExtraElement>";
			}
			else {
				$rezX .= "<ExtraElement>";
				$rezX .= "<Name>Info1</Name>";
				$rezX .= "<Datatype>AMemo</Datatype>";
				$rezX .= "<Value />";
				$rezX .= "</ExtraElement>";
			}
				
			if (!empty($opmerking))	{
				$rezX .= "<ExtraElement>";
				$rezX .= "<Name>Info2</Name>";
				$rezX .= "<Datatype>AMemo</Datatype>";
				$rezX .= "<Value>".trim($opmerking)."</Value>";
				$rezX .= "</ExtraElement>";
			}
			else {
				$rezX .= "<ExtraElement>";
				$rezX .= "<Name>Info2</Name>";
				$rezX .= "<Datatype>AMemo</Datatype>";
				$rezX .= "<Value />";
				$rezX .= "</ExtraElement>";
			}				

			$rezX .= "</ExtraElements>";
			$rezX .= "</OrderItem>";

			if (is_array($sup)) {
				foreach ($sup as $supplement)	{	
					$rezX .= "<OrderItem>";
					$rezX .= "<SupplierArticleNumber>".trim($supplement)."</SupplierArticleNumber>";
					$rezX .= "<LongDescription>".trim($supplement)."</LongDescription>";
					$rezX .= "<Quantities>";
					$rezX .= "<Quantity>";
					$rezX .= "<QuantityType>ORD</QuantityType>";
					$rezX .= "<Amount>".$p['product_quantity']."</Amount>";
					$rezX .= "<UnitOfMeasure>PCE</UnitOfMeasure>";
					$rezX .= "</Quantity>";
					$rezX .= "</Quantities>";
					$rezX .= "</OrderItem>";
				}
			}				

		}

		unset($products);
		$products = array();

		$rezX .= "</OrderItems>";
		//extra elements - delivery hour
		$orderHour = null;
		$orderHour = trim($xml->getDeliveryDate($value['virtuemart_order_id'],'delivery_time'));
		$orderHour = str_replace(":", "", $orderHour);
		if (strlen($orderHour) < 4) {
			$orderHour = "0".$orderHour;
		}

		$rezX .= "<ExtraElements>";
		$rezX .= "<ExtraElement>";
		$rezX .= "<Name>Hour</Name>";
		$rezX .= "<Datatype>AString</Datatype>";
		$rezX .= "<Value>".$orderHour."</Value>";
		$rezX .= "</ExtraElement>";
		$rezX .= "</ExtraElements>";

		$rezX .= "</Order>";
		
		$resultX2 = $mysqli->query("UPDATE jos_virtuemart_orders SET xml='1' WHERE virtuemart_order_id='".$value['virtuemart_order_id']."' LIMIT 1");
	}
	
	$rezX .= "</Orders>";
	$rezX .= "</Document>";
	
	if (isset($xml->_saveFile) && !empty($xml->_saveFile)) {
		file_put_contents($xml->_saveFile, $rezX);
	}
	if (isset($xml->_saveBackupFile) && !empty($xml->_saveBackupFile)) {
		file_put_contents($xml->_saveBackupFile, $rezX);
	}

}



?>