<?php

   $lastTS = intval($_GET['lastts']);
   $TS = intval($_GET['ts']);
   $dataid = $_GET['dataid'];
   $keystr = $_GET['keystr'];

   //$input = JFactory::getApplication()->input;
   //$lastTS = $input->post->get('lastts');

   require_once($_SERVER['DOCUMENT_ROOT'].'/data/dbinfo.php');

   $con = mysql_connect($host, $dbuser, $dbpasswd) or die(mysql_error());

   mysql_select_db("cobsdb", $con);

   $sqlkeys = "SHOW COLUMNS FROM " . $dataid;
   $querykeys=mysql_query($sqlkeys);

   //$sql = "SELECT * FROM " . $dataid . " ORDER BY `time` DESC LIMIT 1";
   $sql = "SELECT * FROM " . $dataid . " ORDER BY `time` DESC LIMIT " . $lastTS;
   $query=mysql_query($sql);

   $time= array();
   $keys = explode(",",$keystr);
   foreach ($keys as &$key) { eval('$' . $key . ' = array();'); }

  while($rs = mysql_fetch_array($query))
  {
    $time[] = $rs['time'];
    foreach ($keys as &$key) { eval('$' . $key . '[] = $rs["' . $key . '"];'); }
  }

  mysql_close($con);

  $time = array_reverse($time);

  $json = array(
  'time' => $time,
  'lastts' => $TS/1000.,
  'ts' => $TS,
  'dataid' => $dataid,
  'keystr' => $keystr,
  );
  //$reversed = array_reverse($input);
  foreach ($keys as &$key) { eval('$json["' . $key . '"] = array_reverse($' . $key . ');'); }

  echo json_encode($json);
?>

