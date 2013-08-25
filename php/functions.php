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
	while ($row = mysql_fetch_array($result))
	{
		if (!$row['activity_delta'])
			$row['activity_delta'] = 0;
			
		$publishers[$row['id']]['name'] = $row['name'];

		if ($publishers[$row['id']]['updated'] < $row['timestamp'])
		{
			$publishers[$row['id']]['updated'] = $row['timestamp'];			
			$publishers[$row['id']]['added'] = $row['activity_delta'];
		}
		
		$publishers[$row['id']]['total'] = $publishers[$row['id']]['total'] + $row['activity_count'];
	}

	return $publishers;
}

var_dump(getPublisherTotals());


?>
