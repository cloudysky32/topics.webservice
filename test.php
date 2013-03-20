<?php
header("Content-Type: multipart/form-data; charset=UTF-8");

require_once ('../topicsdb_connect.php'); // Connect to the db.
		
$sql = "insert into [issue] (topic_id, user_email, content) output inserted.issue_id values (123, 'cloudysky32@gmail.com', 'ddd')";
		
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    $output['status'] = false;
} else {
	$output['status'] = true;
	
	while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_BOTH)) {
		$output['result'][] = array(
			'issue_id' => $row['issue_id']
		);
	}
}

print(json_encode($output));

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

?>