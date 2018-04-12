<?php include('server.php') ?>
<html>
   <head>
      <title>Simedtrieste Charts</title>
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
	
	$roomSelected = $_GET['room'];
	if($roomSelected == '' || $roomSelected < 1  || $roomSelected > 5) {
		$roomSelected = ($_SESSION['curRoom'] == '' ? 1: $_SESSION['curRoom']);
	}
	$_SESSION['curRoom'] =  $roomSelected;
	$configKey = 'config.'.$roomSelected;
	$configFileFromDisk = 'Room'.$roomSelected.".config.csv";
	$dataKey = 'data.'.$roomSelected;
	$csv_file_name = $_SESSION[$dataKey];
	$session_id='1'; // User session id
	$path = "uploads/";
	$valid_formats = array("csv");

	if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {
	  $dataCsvFile = $_FILES['dataCsvFile']['name'];
	  if(strlen($configFile)) {
		$size = $_FILES['configFile']['size'];
		list($txt, $ext) = explode(".", $configFile);
		if(in_array($ext, $valid_formats)) {
		  if($size<(1024*1024)) // Image size max 1 MB
		  {
			$actual_image_name = 'Room'.$roomSelected.".config.".$ext;
			$tmp = $_FILES['configFile']['tmp_name'];
			if(move_uploaded_file($tmp, $path.$actual_image_name)) {
				$_SESSION[$configKey] = $actual_image_name;
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
		  if($size<(1024*1024)) // Image size max 1 MB
		  {
			$csv_file_name = $_FILES['dataCsvFile']['name'];
			$tmp = $_FILES['dataCsvFile']['tmp_name'];
			if(move_uploaded_file($tmp, $path.$csv_file_name)) {
				$_SESSION[$dataKey] = $csv_file_name;
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
		
		<p> <a href="menu.php" style="color: red;">Main Menu</a> </p>
		

		<p> Room selected: <?php echo $roomSelected ?> </p>
				
		<form id="imageform1" method="post" enctype="multipart/form-data" action='chart.php'>

		  Set config <input type="file" name="configFile" id="configFile" >current:<?php echo $configFileFromDisk ?> </input>
		  
		</form>

		<form id="imageform2" method="post" enctype="multipart/form-data" action='chart.php'>

		  Set data <input type="file" name="dataCsvFile" id="dataCsvFile" >current:<?php echo $_SESSION[$dataKey] ?> </input>
		  
		</form>
	
		<div id='preview'>
		</div>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		 <script src="http://malsup.github.com/jquery.form.js"></script> 
		<script type="text/javascript">
		var roomText = "Room " + <?php echo $roomSelected ?>;
		var roomId = 1;
		  $(document).ready(function()
		  {
			$('#roomId').live('change', function()
			{
				var e = document.getElementById("roomId");
				roomText = e.options[e.selectedIndex].text;
				roomId = e.options[e.selectedIndex].value;
				//parent.window.location.reload();
				//alert("roomId: " + roomId);
			});
			
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
		 <div id = "chartId1" style = "width: 550px; height: 400px; margin: 0 auto">  
			
	<?php
 
	if(file_exists('uploads/'.$configFileFromDisk) == 0) {
		$ret = file_exists('uploads/'.$_SESSION[$configKey]);	
		unset($_SESSION[$configKey]);
		$_SESSION[$configKey] = '';
		return;
	}
	//data key
	if($_SESSION[$dataKey] == '') { return; }
	if(file_exists('uploads/'.$_SESSION[$dataKey]) == 0) {
		unset($_SESSION[$dataKey]);
		$_SESSION[$dataKey] = '';
		return;
	}

	$fileHandle = fopen('uploads/'.$configFileFromDisk, "r");
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
		if($index != 0) {
			$monthConfig[$index] = $row[0];
			$ek[$index] = $row[2];
			$bk[$index] = $row[3];
			$wk[$index] = $row[4];
		}
		$index++;
	}
	fclose($fileHandle);
	if($_SESSION[$dataKey] == '') {return; }
	$fileHandle1;
	$ret = file_exists('uploads/'.$_SESSION[$dataKey]);		
	if($ret == 0) {
		unset($_SESSION[$dataKey]);
		$_SESSION[$dataKey] = '';
		return;
	}
	$fileHandle1 = fopen('uploads/'.$_SESSION[$dataKey], "r");
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
	$watt = 0;
	while (($row = fgetcsv($fileHandle1, 0, ",")) !== FALSE) {
		$old_short_date = $cur_short_date;
		$date_arr= explode(" ", $row[0]);
		$cur_short_date= $date_arr[0];
		$time= $date_arr[1];
		$tmpTime= explode(":", $time);
		if(strcmp($cur_short_date, $old_short_date) == 0) {
			$avg_temper +=  $row[1];
			$count++;
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
		$watt += $row[1];
		$minutes[$nextDay][$minIdxPerDate] = $tmpTime[1];
		$minIdxPerDate++;
		$minIdx++;
	}
	fclose($fileHandle1);	
	
	//echo '$temperatureEachMinute[0].len: ' . count($temperatureEachMinute[0]) . '<br>';
	//echo '$tmpTime.len: ' . count($minutes[0]) . '<br>';
	
	if($avg_temper > 0) {
		$index++;
		$avg_temper_per_date_array[$index] = $avg_temper/$count ;
		$avg_date_array[$index] = $old_short_date;
		$range[$index] = $minIdx - 1;
	}
	date_default_timezone_set('America/Los_Angeles');
	$dyear[0] = date("z", strtotime($avg_date_array[0]));
	$dyear[1] = date("z", strtotime($avg_date_array[1]));

	for($i = 0; $i < count($avg_date_array); $i++) {
		$date_php = date_parse($avg_date_array[$i]);
		if($month != '') {
			if (!in_array($date_php['month'], $month)) {
				$month[$i] = $date_php['month'];
			}
		}
		else {
				$month[$i] = $date_php['month'];
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
	
	//store to database to analysis
	{
	  /*$avgWatt = ($watt/$range[1])*$bk[$roomSelected];
	  echo 'watt '.$watt.'</br>';
	  echo 'range '.$range[1].'</br>';
	  echo 'avgWatt '.$avgWatt.'</br>';
	  echo 'bk[$roomSelected] '.$bk[$roomSelected].'</br>';
	  echo 'bk[$roomSelected] '.$bk[$roomSelected].'</br>';
	  echo '[$month[0] '.$month[0].'</br>';
	  echo '[$month[1] '.$month[1].'</br>';
	  echo 'wk[$month[0] '.$wk[$month[0]].'</br>';
	  echo '[$month[1] '.$wk[$month[1]].'</br>';
	  echo 'csv_file_name '.$csv_file_name.'</br>';*/

	  $savedEnergy = (1 - (1/$avgWatt))* ($wk[$month[0]] + $wk[$month[1]])/2;
	  echo 'savedEnergy '.$savedEnergy.'</br>';
	   
	  /*$user_check_query = "SELECT * FROM energy WHERE room = $roomSelected and csv_file_name='$csv_file_name' LIMIT 1";
	  //echo $user_check_query;
	  $result = mysqli_query($db, $user_check_query);
	  $user = mysqli_fetch_assoc($result);
	  $duration = $month[0];
		if ($user) { // login ok
				//update
			$query = "UPDATE energy set room = '$roomSelected', month= '$duration', savedEnergy = '$savedEnergy'";
		}
		else {
			$query = "INSERT INTO energy (room, csv_file_name, month, savedEnergy) VALUES('$roomSelected', '$csv_file_name', '$duration', '$savedEnergy')";
			mysqli_query($db, $query);			
		}
		mysqli_query($db, $query);	
		mysqli_free_result($result);*/		
	}
	
	?>
      </div>
	  
	   <div id = "chartId2" style = "width: 550px; height: 400px; margin: 0 auto">
	   
	   </div>
	   
	   <div id = "chartId3" style = "width: 550px; height: 400px; margin: 0 auto">
	   
	   </div>
	   
	   	   
	   <div id = "chartId4" style = "width: 550px; height: 400px; margin: 0 auto">
	   
	   </div>
	   
	   	   
	   <div id = "chartId5" style = "width: 550px; height: 400px; margin: 0 auto">
	   
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
                  subtitle: roomText
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
            var chart = new google.charts.Line(document.getElementById('chartId' + roomId));
			chart.draw(dataTable, google.charts.Line.convertOptions(options));
			
         }
        // google.charts.setOnLoadCallback(drawChart);
      </script>
   </body>
</html>