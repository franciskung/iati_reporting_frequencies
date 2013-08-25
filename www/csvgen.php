<?php

mysql_connect("localhost", "iatifreqs", "iatifreqs");
mysql_select_db("iatireportcard");

$publisher = $_GET['pub'];
$recipient = $_GET['recip'];
$since = $_GET['since'];
$until = $_GET['until'];


$qry = "SELECT iati_activity_id, DATE(timestamp) AS ts, activity_count, activity_delta, current FROM iati_update WHERE 1 ";

if ($publisher)
{
	$qry2 = "SELECT id, name FROM publisher WHERE iati_group='" . addslashes($publisher) . "'";
	$result2 = mysql_query($qry2);
	list($pub_id, $pub_name) = mysql_fetch_array($result2);
	
	$qry .= " AND publisher_id=$pub_id";
}
else
	$pub_name = "Global";

if ($recipient)
{
	$qry2 = "SELECT id FROM recipient WHERE country_code='" . addslashes($recipient) . "'";
	$result2 = mysql_query($qry2);
	list($recip_id) = mysql_fetch_array($result2);
	
	$qry .= " AND recipient_id=$recip_id";
}

if ($since)
{
	$qry .= " AND timestamp > '" . addslashes($since) . "'";
}

if ($until)
{
	$qry .= " AND timestamp < '" . addslashes($until) . "'";
}

$qry .= " ORDER BY ts DESC";

$result = mysql_query($qry);

$out = fopen("php://output", 'w');

fputcsv($out, array('date', $pub_name));


$rands = array();

while ($row = mysql_fetch_array($result))
{
	if ($row['current'] == 1)
		$rands[$row['iati_activity_id']] = $row['activity_count'];
		
	$theRand = rand(0, $rands[$row['iati_activity_id']]);
	$rands[$row['iati_activity_id']] = $theRand;
	
	//fputcsv($out, array($row['ts'], $row['activity_delta']));
	// sample data...!!
	fputcsv($out, array($row['ts'], $theRand));
}


?>
