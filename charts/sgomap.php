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

<form name="live-map-gmo" method="POST" action="index.php?option=com_jumi&fileid=12" >

<div id="mainimage"><img src="images/backgrounds/SGO5.png" alt="" width="1300" />

<script type="text/javascript">
    var divWidth = document.getElementById("mydiv").clientWidth; 
</script>

<?php
// TODO Sensor systems with different sensor positions (like RCS or Supergrad)


/*
Method to show database contents on maps. 
Each map requires a image and calibration points. The rest of the code is 
identical for each location using a MagPy DB.
Location calibration (long-lat to px) requires 4 points on each image:
W,E,N,S: W and E should be on a horizontal line. The first three parameters
are percental positions of W and E relative to the window width.
The following 8 numbers are the coordinates of four points:
w  
*/ 


// SGO Specific
function coord2screen($lon, $lat, $width) {
    //SGO
    $cx1 = 0.226;
    $cx2 = 0.978;
    $cy = 0.108;
    $clon1 = 310067.66;  // Stollen Ende
    $clon2 = 310067.66;  // Stollen Anfang
    $clon3 = 310067.99;  // Stollen Sockel1 W
    $clon4 = 310072.61;  // Messraum GWR
    $clat1 = -35291.28;  // Stollen Anfang
    $clat2 = -35148.48;  // Stollen Ende
    $clat3 = -35217.52;  // Stollen Sockel1 W
    $clat4 = -35301.85;  // Messraum GWR

    $mlat = ($cx2-$cx1)/($clat2-$clat1);
    //echo '<p> Lat: ' . $lat . '  Long: ' . $lon . '</p>';
    $tlat = $cx1 - $clat1*$mlat;
    $xcorr = ($lon-$clon3)*($clat4-$clat3)/($clon4-$clon3);
    //$x = $width * (($lat-$xcorr)*$mlat + $tlat);
    $x = $width * (($lat)*$mlat + $tlat);
    //echo '<p> x' . $lat . ' ' . $x . '</p>';
    $ycorr = ($lat-$clat1)*($clon2-$clon1)/($clat2-$clat1);
    // 1 m entspricht $mlon
    $diff = ($lon-$clon1)-$ycorr;
    $y = $width * ($cy - $diff*$mlat);
    if ($x <= $width and $y <= $width) {
        return array ($x, $y);
    }
}

function getkey($elem, $elements, $keys, $units, $projkey) {
    // Searching for elem (e.g. F, rh T) in array elements and returning key at the
    // index position if existing
    // Units are also checked.
    // Optional, key is returned only when it corresponds to projkey 
    $unitorder = array('x','y','z','f','t1','t2');

    $a=array();
    if (sizeof($elements) != sizeof($keys)) {
        return array("fail","");
    }
    $unit = 'test';
    for ($i=0; $i <= sizeof($elements);$i++) {
        $exist = substr( $elements[$i], 0, 1 );
        if ($elem == $exist or $elem == strtolower($exist)  or $elem == strtoupper($exist)) {
            if ($projkey!="") {
                if ($keys[$i] == $projkey) {
                    return array($keys[$i],$unit);
                }
            } else {
                return array($keys[$i],$unit);
            }
        }
    }
    return array("fail","");
}

  if ($_POST['piers'] == 'piers') {
      $piercheck = 'checked';
  }
  else {
      $piercheck = '';
  }

  $selectedsensorgroups = array("environment","magnetism","gravity","radiometry","seismology");
  $groupcheck = array("checked","checked","checked","checked","checked");
  if (isset($_POST['groupcheckbox'])) 
  {
      $availablegroups = $_POST['groupcheckbox'];
      $groupcheck = array("","","","","");
      for ($i=0;$i<sizeof($_POST['groupcheckbox']);$i++) {
          $x = array_search($_POST['groupcheckbox'][$i], $selectedsensorgroups);
          $groupcheck[$x] = 'checked';
      }
  }
  else {
      $groupcheck = array("checked","checked","checked","checked","checked");
      $availablegroups = $selectedsensorgroups;
  }

  $valuelist = array('SensorID','T','F','rh','P');
  $unitlist = array('','°C','nT','per','hP');
  $radiocheck = array('','checked','','','');
  $selvalue = $valuelist[1];
  $selunit = $unitlist[1];
  //echo '<p> XXX:' . $_POST['component'] . '</p>';
  for ($i=0;$i<sizeof($valuelist);$i++){
      if ($valuelist[$i] == $_POST['component']) {
          $radiocheck[$i] = 'checked';
          $selvalue = $_POST['component'];
          $selunit = $unitlist[$i]; 
      }
      else {
          $radiocheck[$i] = '';
      }
  }
  if (!in_array("checked", $radiocheck)) {
      $radiocheck = array('','checked','','','');
      $selvalue = $valuelist[1];
      $selunit = $unitlist[1];
  }

  $width = 1300;

  // CALIBRATION
  // #############################################################################
  $calib = True;
  if ($calib) {
      $x1 = $width*0.226;  //x1 = -35148.48
      $x2 = $width*0.978;  //x2 = -35291.28
      $y = $width*0.108;

      echo '<div id="smallimage" STYLE="top:' . round($y-8,0) . 'px; left:' . round($x1-8,0) . 'px;"><img src="images/backgrounds/bullet-green.png" Title="Calib1" /></div>';
      echo '<div id="smallimage" STYLE="top:' . round($y-8,0) . 'px; left:' . round($x2-8,0) . 'px;"><img src="images/backgrounds/bullet-green.png" Title="Calib2" /></div>';

      list($x,$y) = coord2screen(310072.61,-35301.85,$width);
      echo '<div id="smallimage" STYLE="top:' . round($y-8,0) . 'px; left:' . round($x-8,0) . 'px;"><img src="images/backgrounds/bullet-green.png" Title="Test" /></div>';

      list($x,$y) = coord2screen(310067.99,-35217.52,$width);
      echo '<div id="smallimage" STYLE="top:' . round($y-8,0) . 'px; left:' . round($x-8,0) . 'px;"><img src="images/backgrounds/bullet-green.png" Title="Test" /></div>';
  }

  // select all recent temperature measurements from all sensor tables
  // #############################################################################

  require($_SERVER['DOCUMENT_ROOT'].'/data/dbinfo.php');
  // Create connection (new)
  $con = new mysqli($host, $dbuser, $dbpasswd, $db);
  // Check connection
  if ($con->connect_error) {
      die("Connection failed: " . $con->connect_error);
  }


  $ctime = '';
  $now = date('Y-m-d H:i:s',strtotime("-500 days"));
  // 1. Getting available SensorGroups
  // --------------------------------
  $sql = "SELECT DISTINCT SensorGroup FROM SENSORS ORDER BY SensorGroup";
  $result = mysqli_query($con, $sql);
  $incr = $width/4.1;
  $colorarray = array(
    "environment" => "green",
    "magnetism" => "red",
    "radiometry" => "black",
    "seismology" => "yellow",
    "gravity" => "blue",
  );
  echo '<div id="smallimage" style="top:' . $incr . 'px; left:40px;">Select Sensorgroups:</div>';
  $incr += $width/50.;
  while($row = mysqli_fetch_array($result)) {
      $sensorgroup = $row[SensorGroup];
      if ($sensorgroup != '') {
          if (in_array($sensorgroup, $selectedsensorgroups)) {
              $x = array_search($sensorgroup, $selectedsensorgroups); 
          }
          else {
              echo '<p>Sensor group not existing enthalten</p>' ;
              $x = 0;
          }

          echo '<div id="smallimage" style="top:' . $incr . 'px; left:60px;"><img src="images/backgrounds/bullet-' . $colorarray[$sensorgroup] . '.png"/>' . $sensorgroup . '</div>';
          $incr += $width/100.;
          echo '<div id="smallimage" style="top:' . $incr . 'px; left:40px;"><input type="checkbox" name="groupcheckbox[]" value="' . $sensorgroup . '" ' . $groupcheck[$x] . ' onchange="this.form.submit();"></div>';
          $incr += $width/100.;
      }
  }

  // 2. Getting Piers
  // --------------------------------
  $incr = $width/2.5;
  echo '<div id="smallimage" style="top:' . $incr . 'px; left:40px;">Select Piers:</div>';
  $incr += $width/50.;
  echo '<div id="smallimage" style="top:' . $incr . 'px; left:60px;"><img src="images/backgrounds/bullet-grey.png"/>piers</div>';
  $incr += $width/100.;
  echo '<div id="smallimage" style="top:' . $incr . 'px; left:40px;"><input type="checkbox" name="piers" value="piers" ' . $piercheck . ' onchange="this.form.submit();"></div>';
  if ($piercheck == 'checked') {
      $sql = "SELECT PierID,PierLong,PierLat FROM PIERS";
      $result = mysqli_query($con, $sql);
      while($row = mysqli_fetch_array($result)) {
          $pierid = $row[PierID];
          if (substr( $pierid, 0, 1 ) != 'X' and substr( $pierid, 0, 1 ) != 'T' and substr( $pierid, 0, 1 ) != 'Q') {
              list($x,$y) = coord2screen($row[PierLat],$row[PierLong],$width);
              // Limit the range of $x and $y to window
              if ($x > 1 and $y > 1) {
                  echo '<div id="smallimage" style="top:' . round($y-8,0) . 'px; left:' . round($x-8,0) . 'px;"><img src="images/backgrounds/bullet-grey.png" Title="' . $pierid . '"/></div>';
              }
          }
      }
  }

  // 3. Getting all available components
  // --------------------------------
  $incr = $width/4.1;
  $einschub = $width/6.;
  echo '<div id="smallimage" style="top:' . $incr . 'px; left:' . round($einschub,0) . 'px;">Select Components:</div>';
  $incr += $width/50.;
  for ($i=0; $i < sizeof($valuelist); $i++) {
      echo '<div id="smallimage" style="top:' . $incr . 'px; left:' . round($einschub+20,0) . 'px;">' . $valuelist[$i] . '</div>';
      //$incr += $width/100.;
      echo '<div id="smallimage" style="top:' . $incr . 'px; left:' . round($einschub,0) . 'px;"><input type="radio" name="component" value="' . $valuelist[$i] . '" ' . $radiocheck[$i] . ' onchange="this.form.submit();"></div>';
      $incr += $width/50.;
  }

  //$selectedsensorgroups = array("environment","magnetism","gravity","radiometry");
  $sql = "SELECT DISTINCT SensorID, SensorKeys, SensorElements, SensorGroup FROM SENSORS ORDER BY SensorGroup";
  $result = mysqli_query($con, $sql);
  while($row = mysqli_fetch_array($result)) {
      $sensorid = $row[SensorID];
      //echo '<p>' . $sensorid . '</p>';
      $sensorgroup = $row[SensorGroup];
      $sensorkeys = $row[SensorKeys];
      $sensorelements = $row[SensorElements];
      $sqlgettables = 'show tables LIKE "' . $sensorid . '_____%"';
      $tabs = mysqli_query($con, $sqlgettables);
      $datelst = array();
      if (in_array($sensorgroup, $availablegroups)) {
          while($lst = mysqli_fetch_row($tabs)) {
              // Get location
              $sqlloc = 'SELECT DataAcquisitionLongitude, DataAcquisitionLatitude, ColumnContents, ColumnUnits FROM DATAINFO WHERE DataID ="' . $lst[0] . '"';
              $loc = mysqli_query($con, $sqlloc);
              $location = mysqli_fetch_array($loc);
              $listlat = explode(';',$location[0]);
              $listlon = explode(';',$location[1]);
              if (sizeof($listlat) > 1) {
                  $latlist = array();
                  $longlist = array();
                  $projkeylist = array();
                  for ($i=0;$i<=sizeof($listlat);$i++) {
                      $lat = explode(':',$listlat[$i]);
                      $lon = explode(':',$listlon[$i]);
                      $latlist[] = $lat[1];
                      $longlist[] = $lon[1];
                      $projkeylist[] = $lat[0];
                      //echo '<p> Found:' . $lat . '</p>';
                  }
              }
              else {
                  $latlist = array($location[0]);
                  $longlist = array($location[1]);
                  $projkeylist = array("");
              }
              for ($i = 0; $i <= sizeof($latlist); $i++) {
                $latitude = $latlist[$i];
                $longitude = $longlist[$i];
                $projkey = $projkeylist[$i];
                if (sizeof($latlist) > 1) {
                    $datelst = array();  // reset datelst for multiple locations for one sensor
                }
                //echo '<p> Got:' . $latitude . ' : ' . $longitude . ' : ' . $projkey . '</p>';

                if ($_POST['component'] == 'SensorID') {
                  $recentdata = 0;
                }
                else {
                  $recentdata = 1;
                }
                if ($recentdata == 1) {
                 // get data to be displayed (e.g. all temperatures, f values,...)
                 // show value, sensorid with mouse over (update values ??)
                 // Determine the key for the selected value
                 $sensorkeys = explode(",",$row[SensorKeys]);
                 $sensorelements = explode(",",$row[SensorElements]);
                 $units = explode(",",$location[3]);
                 $keysel = getkey($selvalue, $sensorelements, $sensorkeys, $units, $projkey);
                 $key = $keysel[0];
                 //$unit = $keysel[1]; // could be extracted from DATAINFO - currently I use given unitlist
                 $unit = $selunit;
                 //echo '<p>Keys:' . $key . ' : ' . $selvalue . ' : '. $row[SensorElements] . '</p>';
                 if ($key == 'fail') {
                     $sqltime = 'SELECT time FROM ' . $lst[0] . ' ORDER BY time DESC LIMIT 1';
                     $res = mysqli_query($con, $sqltime);
                     $ll = mysqli_fetch_array($res);
                     $datelst[] = array($ll[time],$latitude,$longitude);
                 }
                 else { 
                     $sqltime = 'SELECT time,' . $key . ' FROM ' . $lst[0] . ' ORDER BY time DESC LIMIT 1';
                     $res = mysqli_query($con, $sqltime);
                     $ll = mysqli_fetch_array($res);
                     $divider = 1;
                     if (substr( $sensorid , 0, 6 ) =='GP20S3') {
                         // in case of supergrad use nT and not pT
                         $divider = 1000;
                     }
                     $datelst[] = array($ll[time],$latitude,$longitude,$ll[$key]/$divider);
                 }
                 $latestdata = max($datelst);
                 if ($latestdata > $now and $latestdata[2] != '') {
                     list($x,$y) = coord2screen($latestdata[2],$latestdata[1],$width);
                     if ($x>0 and $y>0) {
                         echo '<div id="smallimage" STYLE="top:' . round($y-8,0) . 'px; left:' . round($x-8,0) . 'px;"><img src="images/backgrounds/bullet-' . $colorarray[$sensorgroup] . '.png" Title="' . $sensorid . '" /></div>';
                         //echo '<p>' . sizeof($latestdata) . ' :kagsfd: ' . $latestdata[0] . $latestdata[3] . '</p>';
                         if (sizeof($latestdata) > 3 and !$latestdata[3] == "") {
                             //$unit = '°C';
	                     echo '<div id="smallimage" STYLE="top:' . round($y+$width/70.,0) . 'px; left:' . round($x+$width/80.,0) . 'px"><FONT SIZE="2" COLOR="B22222">' . round($latestdata[3],2) . $unit . '</FONT></div>';
                             $ctime = $latestdata[0];
                         }
                     }
                 }
                }
                else {
                 // Just show the location and sensorid with mouse over (datatable must be existing)
                 $datelst[] = array(0,$latitude,$longitude);
                 $latestdata = $datelst[0];
                 //echo '<p>' . $latestdata[2] . '  ' . $latestdata[1] . '</p>';
                 list($x,$y) = coord2screen($latestdata[2],$latestdata[1],$width);
                 echo '<p>' . $x . ' : ' . $y . '</p>';
                 if ($latestdata[2] != '' and $x>0) {
                     echo '<div id="smallimage" STYLE="top:' . round($y-8,0) . 'px; left:' . round($x-8,0) . 'px;"><img src="images/backgrounds/bullet-' . $colorarray[$sensorgroup] . '.png" Title="' . $sensorid . '" /></div>';
                     echo '<div id="smallimage" STYLE="top:' . round($y,0) . 'px; left:' . round($x-$width/20.,0) . 'px"><FONT SIZE="2" COLOR="B22222">' . $sensorid . '</FONT></div>';
                 }
                }
              } 
          }
      }
  }
  mysqli_close($con);
  echo '<p>Values updated at ' . $ctime . '</p>';

?>
</div>
</form>
