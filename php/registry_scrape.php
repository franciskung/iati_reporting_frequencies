<?php

mysql_connect("localhost", "root", "");
mysql_select_db("iatireportcard");


$url = "http://www.iatiregistry.org/api/search/dataset?filetype=activity&all_fields=1";

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$all_records = 1;
$current_record = 0;

// Grab records from IATI registry, 100 at a time
while ($current_record < $all_records)
{
	curl_setopt($ch, CURLOPT_URL, $url . "&offset=" . $current_record . "&limit=100");
	$result_json = curl_exec($ch);

	$result = json_decode($result_json, true);
	$all_records = $result['count'];

	// Loop all results
	foreach ($result['results'] as $r)
	{
		echo $current_record;

		// Variables to make life easier
		$activity_id = $r['index_id'];
		$rev_id = $r['revision_id'];
		$groups = $r['groups'];
		$recipient = $r['extras']['country'];
		$activity_count = $r['extras']['activity_count'];
		$timestamp = normalize_timestamp($r['metadata_modified']);

		// Check if this revision is already in our database...
		$qry = "SELECT id FROM iati_update WHERE iati_revision_id='" . addslashes($rev_id) . "'";
		$result = mysql_query($qry);

		if (!mysql_num_rows($result))
		{
			// New revision, add it!
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

// Oh.  mySQL will do this for me, for free. =)
function normalize_timestamp($ts)
{
	return $ts;
	//return strptime($ts, '%Y-%m-%dT%H:%M:%s');
	//return strtotime($ts);
	//return "1376829923";
}

// Given a three-letter "group" code, get or create the publishing organization
// TODO: in the registry, an activity can have multiple groups. But I only consider the first one (in practise, they have one anyway?)
function get_publisher($pub)
{
	$qry = "SELECT id FROM publisher WHERE iati_group='" . addslashes($pub[0]) . "'";
	$result = mysql_query($qry);

	if (mysql_num_rows($result))
	{
		list($pub_id) = mysql_fetch_array($result);
		return $pub_id;
	}

	// Retrieve full org info from the registry
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
