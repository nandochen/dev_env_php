<?php
// params 
$db_servername = "172.99.0.10";
$db_username = "test";
$db_password = "1234"; 
$db_conn_ok = false;
$db_create_ok = false;
$db_table_ok = false;
$db_data_ok = false;
$db_get_ok = false;
$redis_servername = "172.99.0.11";
$redis_conn_ok = false;
$redis_write_ok = false;
$redis_read_ok = false;
$ok = "<span style='color: green;'>OK</span>";
$ng = "<span style='color: red;'>Error</span>"; 
// Database init 
// Connection
$conn = mysqli_connect($db_servername, $db_username, $db_password);
// Check
$db_conn_ok = !$conn->connect_error;
// Create mock data 
if ($db_conn_ok) {
	// schema 
	$db_selected = mysqli_select_db($conn, 'sample');
	if ($db_selected) { 
		// drop if exists
		$cmd = 'drop DATABASE sample;';	
		if (mysqli_query($conn, $cmd)) { 
		}
	} 
	// 
	$cmd = 'CREATE DATABASE sample';	
	  if (mysqli_query($conn, $cmd)) { 
		$db_selected = mysqli_select_db($conn, 'sample');
		$db_create_ok = true;
		// table 
		$cmd = "CREATE TABLE user (
			id int(11) AUTO_INCREMENT,
			email varchar(255) NOT NULL,
			screen_name varchar(100) NOT NULL, 
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
			)";
		if (mysqli_query($conn, $cmd)) {
			$db_table_ok = true;
			// data 
			$cmd = "insert into user (email, screen_name) values ('john.doe@gmail.com', 'john.doe')"
			. ", ('no.body@gmail.com', 'no.body');";
			if (mysqli_query($conn, $cmd)) {
				$db_data_ok = true;
				// get 
				$cmd = "select * from user;";
				if (mysqli_query($conn, $cmd)) {
					$db_get_ok = true; 
				}
			}
		} 
	  }
}
// Redis 
// Connect
$redis = new Redis();
try {
	$redis->connect($redis_servername, 6379);
	$redis_conn_ok = true;
	// Write 
	$redis_write_ok = $redis->set("dacc:sample:test", "abc");
	// Read 
	$redis_read_ok = $redis->get("dacc:sample:test") == "abc" ? true : false;
} catch (Exception $e) { }
?>
<html>
	<head>
        <title>Docker Dev. Env. Sample - DACC</title> 
        <meta charset="utf-8">
        <meta name="author" content="Nando Chen">
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	</head>
	<body>
		<h1>Hello world.</h1>
		<hr />
		<h2>Database</h2>
		<ul>
			<li>Connection: <?=$db_conn_ok ? $ok : $ng ?></li>
			<li>Create schema: <?=$db_create_ok ? $ok : $ng ?></li>
			<li>Create table: <?=$db_table_ok ? $ok : $ng ?></li>
			<li>Create data: <?=$db_data_ok ? $ok : $ng ?></li>
			<li>Read data: <?=$db_get_ok ? $ok : $ng ?></li>
		</ul>
		<h2>Redis</h2>
		<ul>
			<li>Connection: <?=$redis_conn_ok ? $ok : $ng ?></li>
			<li>Write: <?=$redis_write_ok ? $ok : $ng ?></li>
			<li>Read: <?=$redis_read_ok ? $ok : $ng ?></li>
		</ul>
	</body>
</html>