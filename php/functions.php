<?php


mysql_connect("localhost", "iatifreqs", "iatifreqs");
mysql_select_db("iatireportcard");

function getPublisherTotals($type = null, $order = null)
{
	$publishers = array();

	$qry = "SELECT p.id, p.name, u.timestamp, u.activity_count, u.activity_delta AS updated FROM publisher p, iati_update u WHERE u.publisher_id=p.id";

	if ($type)
		$qry .= " AND p.type=$type";

	$qry .= " AND u.current=1";

	if ($order == 'name')
		$qry .= " ORDER BY p.name";
	else
		$qry .= " ORDER BY u.timestamp DESC";
	
	$result = mysql_query($qry);

	$count = 0;
	$delta = 0;
	$max = 0;
	while ($row = mysql_fetch_array($result))
	{
		if (!$row['activity_delta'])
			$row['activity_delta'] = 0;
			
		$publishers[$row['id']]['name'] = $row['name'];
		$publishers[$row['id']]['group_id'] = $row['iati_group'];
		$publishers[$row['id']]['total'] = $publishers[$row['id']]['total'] + $row['activity_count'];

		if ($publishers[$row['id']]['total'] > $max)
			$max = $publishers[$row['id']]['total'];

		if ($publishers[$row['id']]['updated'] < $row['timestamp'])
		{
			$publishers[$row['id']]['updated'] = $row['timestamp'];			

			//$publishers[$row['id']]['added'] = $row['activity_delta'];
			// erm. sample data time.
			$publishers[$row['id']]['added'] = rand(0, $publishers[$row['id']]['total']);
		}
		
	}

	return array('countries' => $publishers, 'max_count' => $max);
}

function getSinglePublisher($group_id)
{
	$qry = "SELECT id FROM publisher WHERE iati_group='" . addslashes($group_id) . "'";
	$result = mysql_query($qry);

	if (!mysql_num_rows($result))
		return;

	list($id) = mysql_fetch_array($result);

	$qry = "SELECT u.timestamp, u.publisher_id, u.recipient_id, u.activity_count, u.activity_delta, u.current, r.country_code, r.name
			FROM iati_update u, recipient r WHERE u.publisher_id=$id AND u.recipient_id=r.id
			ORDER BY u.timestamp DESC";
	$result = mysql_query($qry);

	$countries = array();
	$activities = 0;
	$history = array();

	$rands = array();

	while ($row = mysql_fetch_array($result))
	{
		if ($row['current'] == 1)
		{
			$countries[$row['country_code']] = array('code' => $row['country_code'],
											  'name' => $row['name'],
											  'updated' => $row['timestamp'],
											  'activities' => $row['activity_count']);

			$activities = $activities + $row['activity_count'];
			$rands[$country_code] = $row['activity_count'];
		}

		$theRand = rand(0, $rands[$country_code]);
		$rands[$country_code] = $theRand;

		$history[] = array('time' => $row['timestamp'],
						   'country_name' => $row['name'],
						   'country_code' => $row['country_code'],
						   //'delta' => $row['activity_delta']);
						   // erm. sample data time.
						   'delta' => $rands[$country_code]);
	}

	return array('countries' => $countries,
				 'activities' => $activities,
				 'history' => $history);
}

?>

