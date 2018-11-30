<style type="text/css">
      #smallimage {
          position: absolute;
          }
      #mainimage {
          float: left;
          position: relative;
          }
}
</style>

<form name="tabelle" method="POST" action="index.php?option=com_jumi&fileid=6" >

<?php

  // select all recent temperature measurements from all sensor tables
  // #############################################################################

  require($_SERVER['DOCUMENT_ROOT'].'/portal/dbinfo.php');
  // Create connection (new)
  $con = new mysqli($host, $dbuser, $dbpasswd, $db);
  // Check connection
  if ($con->connect_error) {
      die("Connection failed: " . $con->connect_error);
  }

  $ctime = '';
  $now = date('Y-m-d H:i:s',strtotime("-1 days"));

  //echo '<p>Here:' . $_POST['keys'] . '</p>';
  if (isset($_POST['keys'])) 
  {
      $key = $_POST['keys'];
  } else {
      $key = 't1';
  }

  // Get all available keys 
  echo "<p><select name='keys' onchange='this.form.submit()'>";
  $keyssql = "SELECT DISTINCT SensorKeys, SensorElements FROM SENSORS";
  $result = mysqli_query($con, $keyssql);
  $keylst = array();
  $elelst = array();
  while($row = mysqli_fetch_array($result)) {
      $skeys = $row[SensorKeys];
      $selements = $row[SensorElements];
      $keyssplit = explode(",",$skeys);
      $elemsplit = explode(",",$selements);
      foreach ($keyssplit as $ekey) {
          if (in_array($ekey, $keylst)) {
          } else {
            $keylst[] = $ekey;
            $k = array_search($ekey, $keyssplit);
            $elelst[] = $elemsplit[$k];
          } 
      }
  }
  foreach ($keylst as $ekey) {
      $k = array_search($ekey,$keylst);
      if ($ekey == $key) {
          echo '<option selected value="' . $ekey . '">' . $elelst[$k] . '</option>';
      } else {
          echo '<option value="' . $ekey . '">' .  $elelst[$k] . '</option>';
      }
  }
  echo '</select>';


  echo '<div class="table-responsive">';
  echo '<table class="table table-hover">';

  echo '<tr>';
  echo '<th>Location</td>';
  echo '<th>' . $key . '</td>';
  echo '<th>SensorID</td>';
  echo '<th>Time</td>';
  echo '</tr>';


  $sql = "SELECT DISTINCT SensorID, DataPier FROM DATAINFO ORDER BY DataPier";
  $result = mysqli_query($con, $sql);
  $datelst = array();
  while($row = mysqli_fetch_array($result)) {
      $datapier = $row[DataPier];
      $sensorid = $row[SensorID];
      //echo '<p>' . $sensorid . '</p>';
      $sqlgettables = 'show tables LIKE "' . $sensorid . '_____%"';
      $tabs = mysqli_query($con, $sqlgettables);
      while($lst = mysqli_fetch_row($tabs)) {
              // Value
              $sqltime = 'SELECT time,' . $key . ' FROM ' . $lst[0] . ' ORDER BY time DESC LIMIT 1';
              $res = mysqli_query($con, $sqltime);
              $ll = mysqli_fetch_array($res);
              $datelst[] = array($ll[time],$ll[$key]);
              $val = $ll[$key];
              // check time
              if ($val != Null) {
              echo '<tr>';
              echo '<td>' . $datapier . '</td>';
              echo '<td>' . $val . '</td>';
              echo '<td>' . $sensorid . '</td>';
              echo '<td>' . $ll[time] . '</td>';
              //<tr class="warning">...</tr>
              //echo '<p>' . $datapier . ' : ' . $sensorid . ' : ' . $val . '</p>';
              echo '</tr>';
              }

       }
  }
  $latestdata = max($datelst);
  $ctime = $latestdata[0];

  mysqli_close($con);
  echo '</table table-bordered>';
  echo '</div>';


  echo '<p>Values updated at ' . $ctime . 'UTC </p>';

?>

</form>

