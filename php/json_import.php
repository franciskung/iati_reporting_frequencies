<?php
// import from the historical JSON dump

mysql_connect("localhost", "iatifreqs", "iatifreqs");
mysql_select_db("iatireportcard");

$json = file_get_contents('../iati_data_dump.json', 'r');

$fullarray = json_decode($json, true);

$counter = 1;
foreach ($fullarray as $package_id => $data)
{
	$country = trim($data['country'], "\n\r\t\"");
	$name = trim($data['name'], "\n\r\t\"");
	$publisher = trim($data['publisher'], "\n\r\t\"");
	$updates = $data['update_dates'];

	$qry = "SELECT * FROM iati_update WHERE name='" . addslashes($name) . "'";
	$result = mysql_query($qry);

	if (mysql_num_rows($result))
	{
		list($id, $activity_id, $rev_id, $name, $ts, $pub_id, $recip_id, $act_count) = mysql_fetch_array($result);

		foreach ($updates as $update)
		{
			$qry2 = "INSERT INTO iati_update SET
						iati_activity_id='$activity_id',
						name='$name',
						timestamp='" . addslashes($update) . "',
						publisher_id=$pub_id,
						recipient_id=$recip_id";
			mysql_query($qry2);
		}
		echo ".";
	}
	else
	{
		$pub_id = get_publisher($publisher);
		$recip_id = get_recipient($country);
		
		foreach ($updates as $update)
		{
			$qry2 = "INSERT INTO iati_update SET
						iati_activity_id='$activity_id',
						name='$name',
						timestamp='" . addslashes($update) . "',
						publisher_id=$pub_id,
						recipient_id=$recip_id";
			mysql_query($qry2);
		}
		echo "|";
	}

	$counter++;
	if ($counter == 100)
	{
		echo "<br/>";
		$counter = 1;
	}
	
	flush();
	ob_flush();
	set_time_limit(600);
	
}

echo "<br/><br/>done<br/>";


// this isn't group code, but full IATI id
function get_publisher($pub)
{
	$qry = "SELECT id FROM publisher WHERE iati_id='" . addslashes($pub) . "'";
	$result = mysql_query($qry);

	if (mysql_num_rows($result))
	{
		list($pub_id) = mysql_fetch_array($result);
		return $pub_id;
	}
}

// Given a recipient code ("country" field in the metadata), get or create the recipient country
// we assume the db is already populated with the numeric IDs and/or ISO codes (see country_codes.php)
function get_recipient($country)
{
	// empty? just ignore it.
	if (!$country)
		return "";


	if (is_numeric($country))
	{
		$qry = "SELECT id FROM recipient WHERE country_id='" . addslashes($country) . "'";
		$result = mysql_query($qry);
		
		if (mysql_num_rows($result))
		{
			list($recipient_id) = mysql_fetch_array($result);
			return $recipient_id;
		}
	
		$qry = "INSERT INTO recipient SET 
					name='FILL ME OUT',
					country_id='" . addslashes($country) . "',
					num_activities=0";
		mysql_query($qry);

		return mysql_insert_id();
	}
	
	$qry = "SELECT id FROM recipient WHERE country_code='" . addslashes($country) . "'";
	$result = mysql_query($qry);

	if (mysql_num_rows($result))
	{
		list($recipient_id) = mysql_fetch_array($result);
		return $recipient_id;
	}
	
	$qry = "INSERT INTO recipient SET 
				name='FILL ME OUT',
				country_code='" . addslashes($country) . "',
				num_activities=0";
	mysql_query($qry);

	return mysql_insert_id();
}

?>
