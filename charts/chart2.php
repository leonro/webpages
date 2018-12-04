<html>
<form name="chart" method="POST" action="index.php?option=com_jumi&fileid=7" >

<head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['line']});
      google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable();
      data.addColumn('date', 'Date');
      data.addColumn('number', 'Temp');

<?php
  // connect to mysql server
  // -----------------------
  require($_SERVER['DOCUMENT_ROOT'].'/portal/dbinfo.php');
  // Create connection (new)
  $con = new mysqli($host, $dbuser, $dbpasswd, $db);
  // Check connection
  if ($con->connect_error) {
      die("Connection failed: " . $con->connect_error);
  }

  // get POST data
  // -----------------------
  if (isset($_POST['dataid'])) 
  {
      $dataid = $_POST['dataid'];
  } else {
      $dataid = 'DS2438_26CBC454010000_0001_0001';
  }
  if (isset($_POST['keys'])) 
  {
      $key = $_POST['keys'];
  } else {
      $key = 't1';
  }
  if (isset($_POST['endtime'])) 
  {
      $endtime = $_POST['endtime'];
  } else {
      $endtime = new DateTime();
      $endtime=date("Y-m-jTH:i:sP");
  }
  if (isset($_POST['starttime'])) 
  {
      $starttime = $_POST['starttime'];
  } else {
      // Three days back
      $starttime = date("Y-m-jTH:i:sP", time()-(86400*3));
  }

  //$dataid='DS2438_26CBC454010000_0001_0001';
  $key='t1';

  $sdate=date_create($starttime);
  $edate=date_create($endtime);
  $valarray = array();
  $datasql = 'SELECT UNIX_TIMESTAMP(time) as seconds,' . $key . ' FROM ' . $dataid . ' ORDER BY time DESC LIMIT 1440';

  $datasql = 'SELECT UNIX_TIMESTAMP(time) as seconds,' . $key . ' FROM ' . $dataid . ' WHERE time >="' . date_format($sdate,"Y-m-j H:i:s") . '" AND time <= "' . date_format($edate,"Y-m-j H:i:s") . '"';

  $result = mysqli_query($con, $datasql);
  //$ll = mysqli_fetch_array($result);
  //$valarray[] = array($ll[time],$ll[$key]);

  while($row = mysqli_fetch_array($result)) {
      $line = array();
      $line[] = array($row[seconds],$row[$key]);
      $date = (int)$row[seconds];
      $value = (float)$row[$key];
      echo "data.addRow([new Date(" . $date . '000),' . $value . "]);";
      $valarray[] = $line;
  }

?>

      //data.addRows(<?php $data ?>);

      var options = {
        chart: {
          title: 'Temperature',
          subtitle: 'in degree Celsius (°C)'
        },
        chartArea: {
          backgroundColor: {
          stroke: 'grey',
          strokeWidth: 3
          }
        },
        width: 1080,
        height: 500,
        //backgroundColor: 'black',
        hAxis: {
            format: 'd.M.yy-HH:mm',
            gridlines: {count: 15}
          },
        vAxis: {
            gridlines: {count: 15}
            //gridlines: {color: 'none'},
          }
      };

      var chart = new google.charts.Line(document.getElementById('line_top_x'));

      chart.draw(data, google.charts.Line.convertOptions(options));
    }
  </script>
</head>
<body>

<?php
  // connect to mysql server
  // -----------------------
  require($_SERVER['DOCUMENT_ROOT'].'/portal/dbinfo.php');
  // Create connection (new)
  $con = new mysqli($host, $dbuser, $dbpasswd, $db);
  // Check connection
  if ($con->connect_error) {
      die("Connection failed: " . $con->connect_error);
  }

  // Get all available sensors 
  // -----------------------
  // put a small table environment here
  echo "<p><select name='dataid' onchange='this.form.submit()'>";
  $keyssql = "SELECT DISTINCT DataID,DataPier FROM DATAINFO";
  $result = mysqli_query($con, $keyssql);
  $datalst = array();
  while($row = mysqli_fetch_array($result)) {
      $elem = $row[DataID];
      $loc = $row[DataPier];
      if ($elem == $dataid) {
          echo '<option selected value="' . $elem . '">' . $loc . '</option>';
      } else {
          echo '<option value="' . $elem . '">' .  $loc . '</option>';
      }
  }
  echo '</select></p>';

  // get mnin and max from database
  //echo "<p>" . $endtime . "<p>";
  //$date=date_create($starttime);
  //echo "<p> HERE" . date_format($date,"Y-m-j H:i:s") . "<p>";
  echo "<p><input type='datetime-local' name='starttime' onchange='this.form.submit()' min='2017-06-01T08:30' max=" . $endtime . " value=" . $starttime . "></p>";
  echo "<p><input type='datetime-local' name='endtime' onchange='this.form.submit()' min='2017-06-01T08:30' max=" . $endtime . " value=" . $endtime . "></p>";

?>

  <div id="line_top_x"></div>

</body>

</form>

</html>

