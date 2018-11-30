<?php

function parseToXML($htmlStr)
{
$xmlStr=str_replace('<','&lt;',$htmlStr);
$xmlStr=str_replace('>','&gt;',$xmlStr);
$xmlStr=str_replace('"','&quot;',$xmlStr);
$xmlStr=str_replace("'",'&#39;',$xmlStr);
$xmlStr=str_replace("&",'&amp;',$xmlStr);
return $xmlStr;
}


// Start database connection
require($_SERVER['DOCUMENT_ROOT'].'/data/dbinfo.php');
// Create connection (new)
$con = new mysqli($host, $dbuser, $dbpasswd, $db);
// Check connection
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}


// Select all the rows in the markers table  -- eventually limit by post message
$query = "SELECT * FROM STATIONS WHERE 1";
$result = mysqli_query($con, $query);
if (!$result) {
  die('Invalid query: ' . mysqli_error());
}

header("Content-type: text/xml");

// Start XML file, echo parent node
//echo '<p>Hello World: </p>';
echo '<markers>';

// Iterate through the rows, printing XML nodes for each
while ($row = @mysqli_fetch_assoc($result)){
  // Add to XML document node
  echo '<marker ';
  echo 'id="' . parseToXML($row['StationID']) . '" ';
  echo 'name="' . parseToXML($row['StationName']) . '" ';
  echo 'web="' . parseToXML($row['StationWebInfo']) . '" ';
  echo 'lat="' . $row['StationLatitude'] . '" ';
  echo 'lng="' . $row['StationLongitude'] . '" ';
  echo 'type="' . $row['StationType'] . '" ';
  echo '/>';
}

// End XML file
echo '</markers>';
//echo '<p>Hello World:' . $row['StationType'] .  ' </p>';

?>
