<?php 
include '../../../../wp-load.php';
	global $wpdb;

	$province = isset($_POST['province'])? $_POST['province']: '';
	$table_name =  $wpdb->prefix . 'ss_province';
	
	if($province == ''){
		
		$results = $wpdb->get_results( "SELECT province FROM `$table_name` GROUP BY province ORDER BY id ASC" );
		$dorpdown1 .= "<option value=>Seçiniz</option>";
		foreach($results as $result){
			$dorpdown1 .= "<option value=$result->province>$result->province</option>";
		}
		print_r($dorpdown1);
	} else {

		$results = $wpdb->get_results( "SELECT city FROM `$table_name` WHERE `province`= '$province' ORDER BY id ASC" );
		$dorpdown2 .= "<option value=>Seçiniz</option>";
		foreach($results as $result){
			$dorpdown2 .= "<option value=$result->city>$result->city</option>";
		}
		print_r($dorpdown2);

	}

?>