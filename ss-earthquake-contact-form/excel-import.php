<?php
	require_once(plugin_dir_path( __FILE__ ) .'Classes/PHPExcel.php');
	$objPHPExcel = new PHPExcel();
    $errors = array();
    $success = 0;
	if(isset($_POST['excel']) && empty($_POST['excel'])) {
		if ($_FILES["file"]["error"] > 0) {
		 $errors[] = $_FILES["file"]["error"] . "<br>";
		} else if($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
		  	$objReader = new PHPExcel_Reader_Excel2007();
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load( $_FILES["file"]["tmp_name"]);
			$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
			$array_data = array();
			foreach($rowIterator as $row){
			    $cellIterator = $row->getCellIterator();
			    $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
			    if(1 == $row->getRowIndex ()) continue;//skip first row
			    $rowIndex = $row->getRowIndex ();
			    $array_data[$rowIndex] = array('A'=>'', 'B'=>'','C'=>'','D'=>'', 'E'=>'', 'F'=>'');
			     
			    foreach ($cellIterator as $cell) {
			        if('A' == $cell->getColumn()){
			            $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
			        } else if('B' == $cell->getColumn()){
			            $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
			        } else if('C' == $cell->getColumn()){
			            $array_data[$rowIndex][$cell->getColumn()] = PHPExcel_Style_NumberFormat::toFormattedString($cell->getCalculatedValue(), 'YYYY-MM-DD');
			        } else if('D' == $cell->getColumn()){
			            $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
			        } else if('E' == $cell->getColumn()){
			            $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
			        } else if('F' == $cell->getColumn()){
			            $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
			        }
			    }
			}

			foreach($array_data as $array_item) {


				global $wpdb;
	            $table_name = $wpdb->prefix . 'ss_earthquake_form';
	            $agent_id = $array_item['A'];
	            $agent_name = $array_item['D'];
	            $registration_num = $array_item['C'];
	            $phone = $array_item['E'];
	            $email = $array_item['F'];
	            $result = $wpdb->get_results("SELECT * from $table_name where `agent_id` = '$agent_id'");
	            
	            if(count($result) == 0) {
	            	if($wpdb->query("INSERT INTO $table_name (`agent_id`, `agent_name`, `registration_num`, `phone`, `email`) VALUES('$agent_id', '$agent_name', '$registration_num', '$phone', '$email') ")){
	            	}	    
	            } else {
	                $id = $result['0']->id;
	                if($wpdb->query("UPDATE `wp_ss_earthquake_form` SET `agent_name`= '$agent_name', `registration_num` = '$registration_num', `phone` = '$phone' `email` = '$email',  WHERE `id` = '$id'")){
                        $success += 1;
	                } else {
	                    $errors[] = "Agent id $agent_id is not updated successfuly!";
	                }
	            }


			}
		} else {
            $errors[] = "Please Upload a valid File type (.xlsx)";
        }

	}
?>

<?php if(isset($errors) && (count($errors) > 0)): ?>

    <div class="flash danger">
        <ul>
            <?php foreach($errors as $error): ?>
                <li><?=$error?></li>
            <?php endforeach; ?>
        </ul>
    </div>

<?php elseif(isset($success) && ($success > 0)): ?>
    <div class="flash info">
        <span class="close"><?=$success?> Records are Inserted/Updated</span>

    </div>
<?php endif; ?>

<div class="wrap">
    <h2>Excel Import for Agents Records</h2>
	<form action="" method="post" enctype="multipart/form-data">
		<input type="file" name="file" />
		<input type="hidden" name="excel" value="" />
		<input type="submit" name="submit" value="submit" />
	</form>
</div>


<style>
    form {
        background: #fff;
        height: 96px;
        text-align: center;
        line-height: 89px;
    }
    .flash.danger {
        display: block;
        padding: 20px;
        height: 19px;
        background: red;
        border-radius: 6px;
    }
    .flash ul li {
        color: #fff;
        list-style: none;
    }
    .flash.info {
        display: block;
        padding: 20px;
        height: 19px;
        background: #4A8A32;
        border-radius: 6px;
    }
</style>