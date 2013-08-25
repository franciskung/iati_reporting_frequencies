<?php

mysql_connect("localhost", "iatifreqs", "iatifreqs");
mysql_select_db("iatireportcard");


$url = "http://www.iatiregistry.org/api/rest/group";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$json = curl_exec($ch);

$groups = json_decode($json, true);

foreach ($groups as $code)
{
	$qry = "SELECT id FROM publisher WHERE iati_group='" . addslashes($code) . "'";
	$result = mysql_query($qry);

	if (!mysql_num_rows($result))
	{
		curl_setopt($ch, CURLOPT_URL, "http://www.iatiregistry.org/api/rest/group/$code?all_fields=1");
		$json2 = curl_exec($ch);

		$groupdetail = json_decode($json2, true);

		$qry = "INSERT INTO publisher SET
				iati_id='" . addslashes($groupdetail['extras']['publisher_iati_id']) . "',
				iati_group='$code',
				name='" . addslashes($groupdetail['display_name']) . "',
				type='" . addslashes($groupdetail['extras']['publisher_organization_type']) . "'";
		mysql_query($qry);
		
	}

	echo ".";
	flush();
	set_time_limit(600);
}

echo "<hr/>done";

?>
