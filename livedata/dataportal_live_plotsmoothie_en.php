<?php 
    $keys = $_POST['keys'];
    $sr = $_POST['sr'];
    $dataid = $_POST['dataid'];
    $duration = $_POST['duration'];
    $update = $_POST['update'];
    $elem = $_POST['elem'];
    $units = $_POST['units'];
    $cont = $_POST['cont'];
    $type = $_POST['type'];
    //echo '<p>' . $duration . ' - ' . $update . '</p>';           

    $keylst = explode(",", $keys);
    $elemlst = explode(",", $elem);
    $contlst = explode(",", $cont);
    $unitlst = explode(",", $units);
    // get active keys
    $keyarray = array();
    foreach ($keylst as &$key) {
        $testkey = $_POST[$key];
        // Get only selected keys
        if ($testkey == $key) {
            $keyarray[] = $key;
            //$pos = array_search($key, array_keys($keylist));
            //$elemname = $elem[$pos];
            $ind1 = array_search($key, $keylst);
            $ind2 = array_search($elemlst[$ind1], $contlst);
            //echo '<p>' . $key . ' - ' . $elemlst[$ind1] . ' - ' . $unitlst[$ind2] . '</p>';           
            $elemarray[] = $elemlst[$ind1];
            $unitarray[] = $unitlst[$ind2];
        }
    }
    $keystr = implode(",", $keyarray);
    $elemstr = implode(",", $elemarray);
    $unitstr = implode(",", $unitarray);
?>


<html>
  <head>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="../data/components/com_jumi/files/smoothie.js"></script>
    <script type="text/javascript">

      var keylst = "<?php echo $keylst ?>";
      var sr = "<?php echo $sr ?>";
      var dataid = "<?php echo $dataid ?>";
      var type = "<?php echo $type ?>";
      var keystr = "<?php echo $keystr ?>";
      var keylist = keystr.split(",");
      var elemstr = "<?php echo $elemstr ?>";
      var elemlist = elemstr.split(",");
      var unitstr = "<?php echo $unitstr ?>";
      var unitlist = unitstr.split(",");
      var duration = "<?php echo $duration ?>";
      var update = "<?php echo $update ?>";
      console.log(dataid);
      var lastkey = keylist[keylist.length - 1];

      var timeseries = {'x': new TimeSeries(), 'y': new TimeSeries(), 'z': new TimeSeries() };      
      for (key in keylist) { 
          timeseries[keylist[key]] = new TimeSeries(); 
          };

      var TS = 4000; // TS for requests
      //var TS = update*1000;
      var interval = setInterval(doRequest, TS); // run "doRequest" every TS ms, e.g. 4000ms
      var lastTS = 100;  // Amount of data points to read since last timestep
      var horsize = 1000;
      var msperpixel = 100;
      // 1000 pixel horsize and 100 msperpixel means 100000 ms == 100 sec (horsize*msperpixel/(1000*60) = duration )
      // -> duration*1000*60/horsize = msperpixel
      var msperpixel = Math.round(duration*60000/horsize);
      var msgrid = Math.round(msperpixel*horsize/10);
      console.log(msperpixel);
      console.log(update*1000);
      console.log(lastkey);

      function doRequest(e) {
          var url = 'http://localhost/data/components/com_jumi/files/dataportal_live_getmysql.php';
          var data = {'lastts': lastTS, 'ts': TS, 'dataid': dataid, 'keystr': keystr}; // input for the PHP file
          $.getJSON(url, data, requestCallback); // send request
      }

      function dateconvert(datestring) {
          var datetime = datestring.split(' ');
          var day = datetime[0].split('-');
          var ms = datetime[1].split('.');
          var time = ms[0].split(':');
          var datearray = new Date(day[0], parseInt(day[1], 10) - 1, day[2], time[0], time[1], time[2], ms[1]/1000);
          return datearray;
          }

      // this function is run when $.getJSON() is completed
      function requestCallback(data, textStatus, xhr) {
          // momentan wird nur das letzte element gelesen
          lastTS = data.lastts; // save lastID
          TS = data.ts;
          for (var i = 0; i < data.time.length; i++) {
              // Iterate over numeric indexes from 0 to 5, as everyone expects.
              console.log(data.time)
              var last_date = data.time[i];
              //var last_date = data.time[data.time.length - 1];
              var last_time = dateconvert(last_date);
              var ctime = new Date();
              console.log(lastTS);
              for (key in keylist) { 
                  var arr = data[keylist[key]];
                  //var last_element = arr[arr.length - 1];
                  var last_element = arr[i];
                  //timeseries[keylist[key]].append(last_time.getTime(), last_element);
                  timeseries[keylist[key]].append(ctime.getTime(), last_element);
                  };
              console.log(timeseries[keylist[key]]);
              };
          }
      
      function myYRangeFunction(range) {
            // TODO implement your calculation using range.min and range.max
            var diff = Math.abs(range.max - range.min);
            if (diff == 0) { diff = 30; }; 
            var min = range.min - diff*0.05;
            var max = range.max + diff*0.05;
            //console.log(min, max, diff);
            return {min: min, max: max};
            }

      function createCanvas(keylist, elemlist, unitlist, horsize) {
             var tab=document.createElement('table');
             tab.setAttribute('id','graphtable');
             tab.className="livedatatable";
             var tbo=document.createElement('tbody');
             var row, cell, canvastag;
             var nrRows = 4;
             var nrCols = 2;
             for (var key in keylist) {
                 row=document.createElement('tr');
	         cell=document.createElement('td');
                 var label = elemlist[key] + ' [' + unitlist[key] + ']';
                 cell.appendChild(document.createTextNode(label));
                 row.appendChild(cell);
	         cell=document.createElement('td');
       	         canvastag = document.createElement('canvas');
                 //canvastag.id = "chart";
                 canvastag.id = "chart"+keylist[key];
	         canvastag.width=horsize;
                 canvastag.height="150";
	         cell.appendChild(canvastag);
                 row.appendChild(cell);
       	         tbo.appendChild(row);
             }
             tab.appendChild(tbo);
             return tab;
         }


      function createTimeline(keyname,msperpixel,msgrid,lastkey) {
          if (keyname == lastkey) {
              var chart = new SmoothieChart({
                  grid: { strokeStyle:'rgb(125, 0, 0)',
                  lineWidth: 1.0, millisPerLine: msgrid, verticalSections: 6, },
                  labels: {fontSize: 12,fontFamily: 'sans-serif',precision: 1},
                  timestampFormatter: SmoothieChart.timeFormatter,
                  millisPerPixel: msperpixel,
                  yRangeFunction:myYRangeFunction, 
                  }); }
          else {
              var chart = new SmoothieChart({
                  grid: { strokeStyle:'rgb(125, 0, 0)',
                  lineWidth: 1.0, millisPerLine: msgrid, verticalSections: 6, },
                  labels: {fontSize: 12,fontFamily: 'sans-serif',precision: 1},
                  millisPerPixel: msperpixel,
                  yRangeFunction:myYRangeFunction, 
                  }); }

          var elementid = "chart" + keyname
          chart.addTimeSeries(timeseries[keyname], { strokeStyle: 'rgba(0, 255, 0, 1)', fillStyle: 'rgba(0, 255, 0, 0.2)', lineWidth: 4 });
          chart.streamTo(document.getElementById(elementid), 500);
      }

      window.onload = function ()
      {
         document.getElementById('dataid').innerHTML = dataid;
         //document.getElementById('dataid').innerHTML = type;
         document.getElementById('charttable').appendChild(createCanvas(keylist,elemlist,unitlist, horsize));
         for (key in keylist) {
             createTimeline(keylist[key],msperpixel,msgrid,lastkey);
             };
      }

    </script>
  </head>
  <body>
      <h3><span id="dataid">-</span> - LIVE</h3>
      <div class="three-fourth">
      <div id="charttable"></div>
      </div>

<div class="one-fourth last">

<?php
    if ($type == '') {
        $instimage = 'Inst_Supergrad.jpg';
        $instdesc = 'This potassium sensor of the supergradiometer, manufactured by GEM, measures the absolute value of the geomagnetic field and is the most sensitive geomagnetic instrument at the Observatory. For interpretation, the gradients of several sensors are analyzed.';
    } elseif ($type == 'Overhauzer') {
        $instimage = 'Inst_Overhauzer.jpg';
        $instdesc = 'The Overhauzer sensor measures the absolute strength of the geomagnetic field.';
    } elseif ($type == 'Fluxgate') {
        $instimage = 'Inst_Fluxgate.jpg';
        $instdesc = 'Fluxgate sensors are used to measure the direction of geomagnetic field variations.';
    } else {
        $instimage = 'Inst_Supergrad.jpg';
        $instdesc = 'This potassium sensor of the supergradiometer, manufactured by GEM, measures the absolute value of the geomagnetic field and is the most sensitive geomagnetic instrument at the Observatory. For interpretation, the gradients of several sensors are analyzed.';
    };

    echo '<div class="hover3d">';
    echo '<div class="hover3d-card">';
    echo '<div class="hover3d-image"><img src="images/fotos/' . $instimage . '" alt="" /></div>';
    echo '</div>';
    echo '</div>';
    echo $instdesc;
    echo $group;
?>

<div class="hover3d">
<div class="hover3d-card">
<div class="hover3d-image"><img src="https://sohowww.nascom.nasa.gov/data/realtime/eit_304/1024/latest.jpg" alt="" /></div>
</div>
</div>
</div>
Among others, geomagnetic data is used to characterize the impact of space weather events on earth. The dominent source of space weather are processes on the sun. A live image of the sun (EIT 304 from SOHO, ESA/NASA) is shown above.

</div>

  </body>
</html>


