<?php

mysql_connect("localhost", "root", "");
mysql_select_db("iatireportcard");


$url = "http://www.iatiregistry.org/api/search/dataset?filetype=activity&all_fields=1";

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$all_records = 1;
$current_record = 0;

while ($current_record < $all_records)
{
	curl_setopt($ch, CURLOPT_URL, $url . "&offset=" . $current_record . "&limit=100");
	$result_json = curl_exec($ch);

	$result = json_decode($result_json, true);
	$all_records = $result['count'];

	foreach ($result['results'] as $r)
	{
		echo $current_record;
		
		$activity_id = $r['index_id'];
		$rev_id = $r['revision_id'];
		$groups = $r['groups'];
		$recipient = $r['extras']['country'];
		$activity_count = $r['extras']['activity_count'];
		$timestamp = normalize_timestamp($r['metadata_modified']);
		echo " ($timestamp) ";

		$qry = "SELECT id FROM iati_update WHERE iati_revision_id='" . addslashes($rev_id) . "'";
		$result = mysql_query($qry);

		if (!mysql_num_rows($result))
		{
			$pub_id = get_publisher($groups);
			$recipient_id = get_recipient($recipient);

			$qry2 = "INSERT INTO iati_update SET
						iati_activity_id='" . addslashes($activity_id) . "', 
						iati_revision_id='" . addslashes($rev_id) . "', 
						`timestamp`='" . addslashes($timestamp) . "', 
						publisher_id='" . addslashes($pub_id) . "', 
						recipient_id='" . addslashes($recipient_id) . "', 
						activity_count='" . addslashes($activity_count) . "'";
			mysql_query($qry2);
			echo " - added <br/>";
		}
		else
			echo " - found<br/>";
		flush();
		ob_flush();

		$current_record++;
		set_time_limit(600);
	}
}

curl_close($ch);


function normalize_timestamp($ts)
{
	return $ts;
	//return strptime($ts, '%Y-%m-%dT%H:%M:%s');
	//return strtotime($ts);
	//return "1376829923";
}

function get_publisher($pub)
{
	$qry = "SELECT id FROM publisher WHERE iati_group='" . addslashes($pub[0]) . "'";
	$result = mysql_query($qry);

	if (mysql_num_rows($result))
	{
		list($pub_id) = mysql_fetch_array($result);
		return $pub_id;
	}

	$url = "http://www.iatiregistry.org/api/rest/group/" . urlencode($pub[0]) . "?all_fields=1";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result_json = curl_exec($ch);
	curl_close($ch);

	$result = json_decode($result_json, true);

	$qry = "INSERT INTO publisher SET 
				iati_id='" . addslashes($result['extras']['publisher_iati_id']) . "',
				iati_group='" . addslashes($pub[0]) . "',
				name='" . addslashes($result['display_name']) . "',
				num_countries=0,
				type='" . addslashes($result['extras']['publisher_organization_type']) . "'";
	mysql_query($qry);

	return mysql_insert_id();
}

function get_recipient($country)
{
	if (!$country)
		return "";
		
	if (is_numeric($country))
	{
		$country = "grr";
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
