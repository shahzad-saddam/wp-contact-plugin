<?php
  // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.
include '../../../../wp-load.php';
  global $wpdb;
  global $wpdb;
    $table_name = $wpdb->prefix . 'ss_earthquake_form';
    $results = $wpdb->get_results("SELECT id, agent_name, registration_num, province, city, address, phone, email, contact_person, competent_person_cell, (CASE WHEN signage <> 0 THEN 'H' ELSE 'E' END) As signage,  signage_reason FROM $table_name", ARRAY_A );

  // function cleanData(&$str)
  // {
  //   $str = preg_replace("/\t/", "\\t", $str);
  //   $str = preg_replace("/\r?\n/", "\\n", $str);
  //   if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  // }

  // // file name for download
  // $filename = "contacts_data_" . date('Ymd') . ".xls";

  // header("Content-Disposition: attachment; filename=\"$filename\"");
  // header("Content-Type: application/vnd.ms-excel;");
  // header('Content-Transfer-Encoding: binary');
  // header('Pragma: public');
  // print "\xEF\xBB\xBF"; 
  // $flag = false;
  // foreach($results as $row) {
  //   if(!$flag) {
  //     // display field/column names as first row
  //    echo "Id"."\t";
  //    echo "Levha Kayıt Numarası"."\t";
  //    echo "İl"."\t";
  //    echo "İlçe"."\t";
  //    echo "Adres"."\t";
  //    echo "Telefon"."\t";
  //    echo "E-Posta"."\t";
  //    echo "Yetkili Kişi"."\t";
  //    echo "Yetkili Kişinin Cep Telefonu"."\t";
  //    echo "abela istiyor musunuz"."\t";
  //    echo "Tabela istememe nedeninizi birkaç cümle ile açıklayınız"."\t" . "\n";
  //   //echo implode("\t", array_keys($row)) . "\n";
  //     $flag = true;
  //   }
  //   array_walk($row, 'cleanData');
  //   //print chr(255) . chr(254) . mb_convert_encoding(implode("\t", array_values($row)), 'UCS-2LE', 'UTF-8');
  //   echo implode("\t", array_values($row)) . "\n";

  // }

  // exit;
require_once(plugin_dir_path( __FILE__ ) .'../Classes/PHPExcel.php');
    // Instantiate a new PHPExcel object
$objPHPExcel = new PHPExcel(); 
// Set the active Excel worksheet to sheet 0
$objPHPExcel->setActiveSheetIndex(0); 
// Initialise the Excel row number
$rowCount = 1;  

//start of printing column names as names of MySQL fields  
$column = 'A';
$iterate = 0;
/*echo "<pre>";
print_r($results); exit;*/
$heads = array('Id', 'Acente Adı', 'Levha Kayıt Numarası', 'İl', 'İlçe', 'Adres', 'Telefon', 'E-Posta', 'Yetkili Kişi', 'Yetkili Kişinin Cep Telefonu', 'abela istiyor musunuz', 'Tabela istememe nedeninizi birkaç cümle ile açıklayınız');
foreach ($results['0'] as $key=>$val)  
{
    $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount,$heads[$iterate]);
    $column++;
    $iterate++;
}
//end of adding column names  

//start while loop to get data  
$rowCount = 2;  
foreach($results as $result)  
{  
    $column = 'A';
    foreach($result as $val)  
    {  
        if(!isset($val))  
            $value = NULL;  
        elseif ($val != "")  
            $value = strip_tags($val);  
        else  
            $value = "";  

        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
        $column++;
    }  
    $rowCount++;
} 


// Redirect output to a client’s web browser (Excel5) 
header('Content-Type: application/vnd.ms-excel'); 
header('Content-Disposition: attachment;filename="Basvuru Listesi.xls"'); 
header('Cache-Control: max-age=0'); 
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter ->setTempDir('reports');
$objWriter->save('php://output');
?>