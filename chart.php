<html>
   <head>
      <title>simedtrieste Charts</title>
      <script type = "text/javascript" src = "https://www.gstatic.com/charts/loader.js"></script>
      <script type = "text/javascript">
         google.charts.load('current', {packages: ['corechart','line']});  
	

      </script>
   </head>
   <?php session_start(); 
    STATIC $counter = 0;
     if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
  
   ?>
   
	<!--?php include 'ajaximage.php';?-->
	
	<?php session_start(); 
	

	$session_id='1'; // User session id

	$path = "uploads/";

	$valid_formats = array("csv");

	if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {
	  $configFile = $_FILES['configFile']['name'];
	  //echo 'configFile'. $configFile;
	  $dataCsvFile = $_FILES['dataCsvFile']['name'];
	 // echo 'dataCsvFile: '.$dataCsvFile.'<br>';
	  //echo 'configFile: '.$configFile.'<br>';
	  //echo 'size'.$size;
	  if(strlen($configFile)) {
		$size = $_FILES['configFile']['size'];
		//echo 'configFile size: '.$size.'<br>';
		list($txt, $ext) = explode(".", $configFile);
		if(in_array($ext, $valid_formats)) {
		  if($size<(1024*1024)) // Image size max 1 MB
		  {
			$actual_image_name = time().'.'.$_SESSION['username'].".config.".$ext;
			echo 'actual_image_name: '.$actual_image_name.'<br>';
			$tmp = $_FILES['configFile']['tmp_name'];
			echo 'tmp: '.$tmp.'<br>';
			
			echo 'path:'.$path.$actual_image_name.'<br>';
			

			if(move_uploaded_file($tmp, $path.$actual_image_name)) {
			  //echo "<img src='uploads/".$actual_image_name."' class='preview'>";
				echo "Uploaded file: ". $actual_image_name." is ok".'<br>';
				$_SESSION["configFile"] = $actual_image_name;
				?>
				 <script type = "text/javascript">
					parent.window.location.reload();
				</script>	
				<?php
			}
			else
			  echo "failed".'<br>';
		  }
		  else
			echo "Image file size max 1 MB";
		}
		else
		  echo "Invalid file format..";
	  }
	  
	  if(strlen($dataCsvFile)) {
		list($txt, $ext) = explode(".", $dataCsvFile);

		if(in_array($ext,$valid_formats)) {
		  $size = $_FILES['dataCsvFile']['size'];
		  //echo 'dataCsvFile size: '.$size.'<br>';
		  if($size<(1024*1024)) // Image size max 1 MB
		  {
			$actual_image_name = time().'.'.$_SESSION['username'].".data.".$ext;
			
			//$actual_image_name = $dataCsvFile;
			
			$tmp = $_FILES['dataCsvFile']['tmp_name'];

			if(move_uploaded_file($tmp, $path.$actual_image_name)) {
			  //echo "<img src='uploads/".$actual_image_name."' class='preview'>";
				echo "Uploaded file: ". $actual_image_name." is ok".'<br>';
				$_SESSION["dataCsvFile"] = $actual_image_name;
				?>
				 <script type = "text/javascript">
					parent.window.location.reload();
				</script>	
				<?php				
			}
			else
			  echo "failed";
		  }
		  else
			echo "Image file size max 1 MB";
		}
		else
		  echo "Invalid file format..";
	  }
	  //else
	//	echo "Please select image..!";
	  exit;
	}	  
	
	   ?>
      
   <body>
	
	  <div id = "chartId" style = "width: 550px; height: 800px; margin: 0 auto"> 

	  <!-- notification message -->
		<?php if (isset($_SESSION['success'])) : ?>
		  <div class="error success" >
			<h3>
			  <?php 
				echo $_SESSION['success']; 
				unset($_SESSION['success']);
			  ?>
			</h3>
		  </div>
		<?php endif ?>

		<!-- logged in user information -->
		<?php  if (isset($_SESSION['username'])) : ?>
			<p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
			<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
			
		<?php endif ?>
		
		
		<p> <a href="sendmail.php" style="color: red;">Sendmail</a> </p>


		<form id="imageform1" method="post" enctype="multipart/form-data" action='chart.php'>

		  Set config <input type="file" name="configFile" id="configFile" > current: <?php echo $_SESSION["configFile"] ?> </input>
		  
		</form>

		<form id="imageform2" method="post" enctype="multipart/form-data" action='chart.php'>

		  Set data <input type="file" name="dataCsvFile" id="dataCsvFile" > current: <?php echo $_SESSION["dataCsvFile"] ?> </input>
		  
		</form>

		
		<div id='preview'>
		</div>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		 <script src="http://malsup.github.com/jquery.form.js"></script> 
		<script type="text/javascript">
		  $(document).ready(function()
		  {
			$('#configFile').live('change', function()
			{
			  $("#preview").html('');
			  $("#preview").html('<img src="loader.gif" alt="Uploading...."/>');
			  $("#imageform1").ajaxForm(
			  {
				target: '#preview'
			  }).submit();
			});

			$('#dataCsvFile').live('change', function()
			{
			  $("#preview").html('');
			  $("#preview").html('<img src="loader.gif" alt="Uploading...."/>');
			  $("#imageform2").ajaxForm(
			  {
				target: '#preview'
			  }).submit();
			});			
		  });
		</script>
		 <div id = "chartId2" style = "width: 550px; height: 400px; margin: 0 auto">  
			
		<?php
	
 
	$fileHandle;
	//echo 'configFile: '.$_SESSION["configFile"].'<br>';
	if($_SESSION["configFile"] == '') {return; }
	$ret = file_exists('uploads/'.$_SESSION["configFile"]);		
	if($ret == 0) {
		unset($_SESSION["configFile"]);
		$_SESSION["configFile"] = '';

		if($_SESSION["dataCsvFile"] == '') {return; }
		$fileHandle1;
		$ret = file_exists('uploads/'.$_SESSION["dataCsvFile"]);		
		if($ret == 0) {
			unset($_SESSION["dataCsvFile"]);
			$_SESSION["dataCsvFile"] = '';
			return;
		}
	
		return;
	}
	$fileHandle = fopen('uploads/'.$_SESSION["configFile"], "r");
	
	$monthConfig;
	$ek;
	$bk;
	$wk;
	//check config 
	$row = fgetcsv($fileHandle, 0, ",");

	if( $row[0] != 'Month' || $row[1] != 'Room' || $row[2] != 'b(k)' || $row[3] !='e(k)' || $row[4] != 'w(k)') {
		echo 'Sorry the config file is not valid';
		return;
	}
	
	//Loop through the CSV rows.
	$index = 0;
	while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
		//Print out my column data.
		//echo 'Month: ' . $row[0] . '<br>';
		//	echo 'Room: ' . $row[1] . '<br>';
		//echo 'b(k): ' . $row[2] . '<br>';
		//echo 'e(k): ' . $row[3] . '<br>';
		//echo '<br>';
		if($index != 0) {
			$monthConfig[$index] = $row[0];
			$ek[$index] = $row[2];
			$bk[$index] = $row[3];
			$wk[$index] = $row[4];
		}
		//echo 'b(k): ' . $row[2] . '<br>';
		$index++;
	}
	fclose($fileHandle);
	//echo 'monthConfig len: ' .count($monthConfig);
	//echo 'ek len: ' .count($ek);
	//echo 'ek len: ' .count($ek);
	//echo 'wk len: ' .count($wk);
	//Open the file.
	//echo 'dataCsvFile: '.$_SESSION["dataCsvFile"].'<br>';
	if($_SESSION["dataCsvFile"] == '') {return; }
	$fileHandle1;
	$ret = file_exists('uploads/'.$_SESSION["dataCsvFile"]);		
	if($ret == 0) {
		unset($_SESSION["dataCsvFile"]);
		$_SESSION["dataCsvFile"] = '';
		return;
	}
	$fileHandle1 = fopen('uploads/'.$_SESSION["dataCsvFile"], "r");
	//Loop through the CSV rows.
	$cur_short_date = '';
	$old_short_date = '';
		
	$avg_temper = 0;
	$count = 1;
	$avg_temper_per_date_array;
	$avg_date_array;
	$index = 0;
	$dyear;
	$month;
	$temperatureEachMinute = []; //per day
	$room = [1,2,3,4,5];
	$temperatureFullEachMinute = [];
	$minutes = [];
	
		//check config 
	$row = fgetcsv($fileHandle1, 0, ",");

	if( $row[4] != '') {
		echo 'Sorry the data file is not valid'.strstr($row[0], 'Timestamp');
		return;
	}

	$minIdxPerDate = 0;
	$nextDay = 0;
	$minIdx = 0;
	$range = [];
	while (($row = fgetcsv($fileHandle1, 0, ",")) !== FALSE) {
		//Print out my column data.
		//echo 'Timestamp for sample frequency every 1 min: ' . $row[0] . '<br>';
		//if($idx++ == 0) continue;
		$old_short_date = $cur_short_date;
		$date_arr= explode(" ", $row[0]);
		$cur_short_date= $date_arr[0];
		$time= $date_arr[1];
		$tmpTime= explode(":", $time);
		//echo 'cur_short_date: ' . $cur_short_date . '<br>';
		//echo '$tmpTime[1]: ' . $tmpTime[1] . '<br>';
		if(strcmp($cur_short_date, $old_short_date) == 0) {
			$avg_temper +=  $row[1];
			$count++;
			//echo 'avg_temper: ' . $avg_temper . '<br>';
		}
		else if($old_short_date > 0){
			$avg_temper_per_date_array[$index] = $avg_temper/$count ;
			$avg_date_array[$index] = $old_short_date;
			$range[$index] = $minIdx;
			$count = 1;
			$avg_temper  = 0;
			$nextDay++;
			$minIdxPerDate = 0;
		}
		$temperatureEachMinute[$nextDay][$minIdxPerDate] = $row[1];
		$temperatureFullEachMinute[$room[0]][$minIdx] = $row[1];
		$minutes[$nextDay][$minIdxPerDate] = $tmpTime[1];
		//echo 'Temperature_Celsius: '.$row[1].'</br>';
		$minIdxPerDate++;
		$minIdx++;
	}
	echo '$temperatureEachMinute[0].len: ' . count($temperatureEachMinute[0]) . '<br>';
	echo '$tmpTime.len: ' . count($minutes[0]) . '<br>';

	
	fclose($fileHandle1);
	if($avg_temper > 0) {
		$index++;
		$avg_temper_per_date_array[$index] = $avg_temper/$count ;
		$avg_date_array[$index] = $old_short_date;
		$range[$index] = $minIdx - 1;
	}
	//echo 'data1: ' . $avg_temper_per_date_array[0] . '<br>';	
	//echo 'data2: ' . $avg_temper_per_date_array[1] . '<br>';	
	
	//echo 'date1: ' . $avg_date_array[0] . '<br>';	
	//echo 'date2: ' . $avg_date_array[1] . '<br>';
	
	date_default_timezone_set('America/Los_Angeles');

	//echo $avg_date_array[0];
	$dyear[0] = date("z", strtotime($avg_date_array[0]));
	$dyear[1] = date("z", strtotime($avg_date_array[1]));
	
	//echo 'dyear0: ' . $dyear[0] . '<br>';	
	//echo 'dyear1: ' . $dyear[1] . '<br>';

	
	for($i = 0; $i < count($avg_date_array); $i++) {
		$date_php = date_parse($avg_date_array[$i]);
		if($month != '') {
			if (!in_array($date_php['month'], $month)) {
				$month[$i] = $date_php['month'];
				//echo $month[$i].'|';			
			}
		}
		else {
				$month[$i] = $date_php['month'];
				//echo $month[$i].'|';		
		}
	}
	if(count($month) == 1) {
		$month[1] = $month[0];
	}
	if(count($dyear) == 1) {
		$dyear[1] = $dyear[0];
	}	
	if(count($avg_temper_per_date_array) == 1) {
		$avg_temper_per_date_array[1] = $avg_temper_per_date_array[0];
	}	
	?>
      </div>
	  
	   <div id = "chartId3" style = "width: 550px; height: 400px; margin: 0 auto">
	   
	   </div>
	   
	   <div id = "chartId4" style = "width: 550px; height: 400px; margin: 0 auto">
	   
	   </div>
	   
	   	   
	   <div id = "chartId5" style = "width: 550px; height: 400px; margin: 0 auto">
	   
	   </div>
	   
	   	   
	   <div id = "chartId6" style = "width: 550px; height: 400px; margin: 0 auto">
	   
	   </div>
	   
	  <!--div>
			<p> <a href="chart.php?p=1" style="color: red;">Show Next Date</a> </p>
	  </div-->
</div>	  

		<script type="text/javascript">
		  google.charts.setOnLoadCallback(function() { drawChart(); });
		  
		</script>

      <script language = "JavaScript">

 
         function drawChart() {
            // Define the chart to be drawn.
            var data = new google.visualization.DataTable();
			//var dayYearEst = [54,57];
			//var dayYearEst= js_array($dyear);

			var dayYearEst = new Array(<?php echo implode(',', $dyear); ?>);
			var rangeDate = new Array(<?php echo implode(',', $range); ?>);

			var avg_date_array = new Array();
			avg_date_array.push('<?php echo $avg_date_array[0] ?>');
			avg_date_array.push('<?php echo $avg_date_array[1] ?>');
			
			//per day
			//var temperatureEachMinute = new Array(<?php echo implode(',', $temperatureEachMinute[0]); ?>);
			 var temperatureEachMinute = new Array(<?php echo implode(',', $temperatureFullEachMinute[$room[0]]); ?>);
			//console.log('temperatureEachMinute.length: ' + temperatureEachMinute.length);
			var minutes = new Array(<?php echo implode(',', $minutes[0]); ?>);

			//var tempAvgPredict = [];
			var temperaturePredictEachMinute = [];
			
			var numRows = temperatureEachMinute.length;
			console.log('len numRows: ' + numRows);
			var numCols = 3;
			var monthEst = [2,2];
			var monthEst =new Array(<?php echo implode(',', $month); ?>);
			
			
			var monthConfig = new Array(<?php echo implode(',', $monthConfig); ?>);
			
	
			var ekk =0;
			var bkk =0;
			var wkk =0;
			var ek = new Array(<?php echo implode(',', $ek); ?>);
			var bk = new Array(<?php echo implode(',', $bk); ?>);
			var wk = new Array(<?php echo implode(',', $wk); ?>);
			
			for(var i= 0; i < monthConfig.length; i++) {
				if(monthConfig[i] == monthEst[0]) {
					ekk = ek[i];
					bkk = bk[i];
					wkk = wk[i];
					console.log('ekk: ' + ekk);
					console.log('bkk: ' + bkk);
					console.log('wkk: ' + wkk);
					break;
				}
			}

            data.addColumn('string', 'Day');
            data.addColumn('number', 'Attual');
            data.addColumn('number', 'Predict');
			//var tempAvg = new Array(<?php echo implode(',', $avg_temper_per_date_array); ?>);
			
			console.log('len ek: ' + ek.length);
			console.log('len bk: ' + bk.length);
			console.log('len wk: ' + wk.length);
			console.log('len month: ' + monthConfig.length);
			console.log('len dayYearEst: ' + dayYearEst.length);
			console.log('numRows: ' + numRows);
			console.log('numCols: ' + numCols);
			
			for(var k = 0; k < temperatureEachMinute.length; k++) {
				temperaturePredictEachMinute[k] = temperatureEachMinute[k] + bkk * wkk + ekk;
				//console.log('temperatureEachMinute: ' + temperatureEachMinute[k] );
			}

			//t(j, k) = c(j) + b(k)*w(j, k) + e(j, k)

			var dataTable = new google.visualization.DataTable();
		 
			tempArr = [] // or new Array
			tempArr[0] = [];
			tempArr[0].push('Minute');
			tempArr[0].push('Atual Celsius');
			tempArr[0].push('Predict Celsius');			 
			for (var i = 0; i < numRows; i++) { //day 
				tempArr[i+1] = [];
				for (var j = 0; j < numCols; j++) { //day, atual, predict
				

							if(j == 0)
								tempArr[i+1].push(String(i+1)); //day string value
							else  
							{
								var found = 0;
								if(j == 1) {
									tempArr[i+1].push(temperatureEachMinute[i]); 
									//console.log('temperatureEachMinute: ' + temperatureEachMinute[i] );
								}
								if(j == 2) {
									tempArr[i+1].push(temperaturePredictEachMinute[i]);
								}

							}
					}
				
			  
			}

			  dataTable.addColumn('string', tempArr[0][0]);

			  // all other columns are of type 'number'.
			  for (var j = 1; j < numCols; j++)
				dataTable.addColumn('number', tempArr[0][j]);    
			
			  for (var i = 1; i < numRows; i++)
				dataTable.addRow(tempArr[i]);
		

            // Set chart options
            var options = {
               chart: {
                  title: '',
                  subtitle: 'Room 1'
               },   
               hAxis: {
                  title: 'Date: ' + avg_date_array[0] + ' - data [1..' + rangeDate[0] +']' + ', ' +  avg_date_array[1] + ' - data (' + rangeDate[0]  + '..' + rangeDate[1] +']',       
               },
               vAxis: {
                  title: 'Temperature',        
               }, 
               'width':1200,
               'height':400      
            };

            // Instantiate and draw the chart.
            var chart = new google.charts.Line(document.getElementById('chartId2'));
			chart.draw(dataTable, google.charts.Line.convertOptions(options));
			
            // Set chart options
            var options = {
               chart: {
                  title: '',
                  subtitle: 'Room 2'
               },   
               hAxis: {
                  title: 'Date: ' + avg_date_array[0] + ' - data [1..' + rangeDate[0] +']' + ', ' +  avg_date_array[1] + ' - data (' + rangeDate[0]  + '..' + rangeDate[1] +']',       
               },
               vAxis: {
                  title: 'Temperature',        
               }, 
               'width':1200,
               'height':400      
            };

			
            var chart = new google.charts.Line(document.getElementById('chartId3'));
            chart.draw(dataTable, google.charts.Line.convertOptions(options));
			
            // Set chart options
            var options = {
               chart: {
                  title: '',
                  subtitle: 'Room 3'
               },   
               hAxis: {
                  title: 'Date: ' + avg_date_array[0] + ' - data [1..' + rangeDate[0] +']' + ', ' +  avg_date_array[1] + ' - data (' + rangeDate[0]  + '..' + rangeDate[1] +']',       
               },
               vAxis: {
                  title: 'Temperature',        
               }, 
               'width':1200,
               'height':400      
            };
			
			var chart = new google.charts.Line(document.getElementById('chartId4'));
            chart.draw(dataTable, google.charts.Line.convertOptions(options));
			
            // Set chart options
            var options = {
               chart: {
                  title: '',
                  subtitle: 'Room 4'
               },   
               hAxis: {
                  title: 'Date: ' + avg_date_array[0] + ' - data [1..' + rangeDate[0] +']' + ', ' +  avg_date_array[1] + ' - data (' + rangeDate[0]  + '..' + rangeDate[1] +']',       
               },
               vAxis: {
                  title: 'Temperature',        
               }, 
               'width':1200,
               'height':400      
            };
			
			var chart = new google.charts.Line(document.getElementById('chartId5'));
            chart.draw(dataTable, google.charts.Line.convertOptions(options));

            // Set chart options
            var options = {
               chart: {
                  title: '',
                  subtitle: 'Room 5'
               },   
               hAxis: {
                  title: 'Date: ' + avg_date_array[0] + ' - data [1..' + rangeDate[0] +']' + ', ' +  avg_date_array[1] + ' - data (' + rangeDate[0]  + '..' + rangeDate[1] +']',       
               },
               vAxis: {
                  title: 'Temperature',        
               }, 
               'width':1200,
               'height':400      
            };
			
			var chart = new google.charts.Line(document.getElementById('chartId6'));
            chart.draw(dataTable, google.charts.Line.convertOptions(options));		
			
			
         }
        // google.charts.setOnLoadCallback(drawChart);
      </script>
   </body>
</html>