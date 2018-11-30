<form name="dataportal_live_dataids_en" method="POST" action="index.php?option=com_jumi&fileid=5" >

<h4>Select data source for live view:</h4>
<p>(Details on the available instruments can be found here (coming soon) and also are displayed on the next page)</p>

<?php

function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

require($_SERVER['DOCUMENT_ROOT'].'/data/dbinfo.php');

// Create connection (new)
$con = new mysqli($host, $dbuser, $dbpasswd, $db);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

debug_to_console("Database successfully connected");

   // select sensorids from SENSORS
   $sql = "SELECT DISTINCT SensorID FROM DATAINFO";
   $result = mysqli_query($con, $sql);
   // for each sensorid check 0001 table for current data (less then 1 min old)
   echo '<p>Available for live view ...</p>';
   echo '<p><select name="dataid">';
   while($row = mysqli_fetch_array($result)) {
       $dataid = $row[SensorID] . '_0001';
       // Check if a table of name dataid is existing
       $sqltab = 'SHOW TABLES LIKE "' . $dataid . '"';
       $resulttab = mysqli_query($con, $sqltab);
       $found = mysqli_num_rows($resulttab);
       if ($found > 0) {
           // Check Sampling rate - only select data with sr < 120 sec
           $sqlsr = 'SELECT DataSamplingRate FROM DATAINFO WHERE DataID = "' . $dataid . '"';
           $resultsr = mysqli_query($con, $sqlsr);
           $srline = mysqli_fetch_array($resultsr);
           $sr = $srline[DataSamplingRate];
           // Check last time stamp in data table
           $sqlactual = 'SELECT time FROM ' . $dataid . ' ORDER BY time DESC LIMIT 1';
           $resultactual = mysqli_query($con, $sqlactual);
           $line = mysqli_fetch_array($resultactual);
           debug_to_console($dataid);
           $dtsplit = explode(".",$line[time]);
           $dt = $dtsplit[0];
           $lastdate = strtotime($dt);
           debug_to_console($dt);
           debug_to_console($lastdate);
           date_default_timezone_set('UTC');
           debug_to_console("AND NOW");
           debug_to_console(date("Y-m-d H:i:s"));
           $now = strtotime(date("Y-m-d H:i:s"));
           debug_to_console($now);

           $now2 = new DateTime();
           debug_to_console($now2->format('Y-m-d H:i:s'));
           $diff = round(abs($now - $lastdate));
           debug_to_console($diff);
           debug_to_console($sr);
           echo $line[time] . " " . $sr;
           if ($sr < 120 and $sr > 0  and $diff < 120) {
               // if time == current and not $dataid startswith BLV_ then
               echo '<option value="' . $dataid . '">' . $dataid . '</option>';
           }
       }
   }
   echo '<p>data and instrument details will be shown in the next page </p>';
   mysqli_close($con);
   echo '</select></p>';
?>

<div align="left"><input type="submit" name="submit" value="submit query"></div>

</form>
