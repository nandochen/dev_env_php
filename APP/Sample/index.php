<?php 
// params 
$env = getenv('env');
$env_title = $env == 'prod' ? '' : ($env == 'dev' ? 
		'<span class=\'badge alert-info\'>Development</span>' : 
		'<span class=\'badge alert-warning text-dark\'>Stage</span>');
$db_servername = getenv('db_servername');
$db_schema = getenv('db_schema');
$db_username = getenv('db_username');
$db_password = getenv('db_password');
$db_conn_ok = false;
$db_create_ok = false;
$db_table_ok = false;
$db_data_ok = false;
$db_get_ok = false;
$redis_servername = getenv('redis_servername');
$redis_conn_ok = false;
$redis_write_ok = false;
$redis_read_ok = false;
$ok = "<span class='text-success'>OK</span>";
$ng = "<span class='text-danger'>Error</span>"; 
// Database init 
// Connection
$conn = mysqli_connect($db_servername, $db_username, $db_password);
// Check
$db_conn_ok = !$conn->connect_error;
// Create mock data 
if ($db_conn_ok) {
	// schema 
	$db_selected = mysqli_select_db($conn, $db_schema);
	if ($db_selected) { 
		// drop if exists
		$cmd = 'drop DATABASE sample;';	
		if (mysqli_query($conn, $cmd)) { 
		}
	} 
	// 
	$cmd = 'CREATE DATABASE sample';	
	  if (mysqli_query($conn, $cmd)) { 
		$db_selected = mysqli_select_db($conn, $db_schema);
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
        <title><?=getenv('site_title')?></title> 
        <meta charset="utf-8">
        <meta name="author" content="Nando Chen">
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<!-- Bootstraps -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	</head>
	<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<h1>Hello world.<?=$env_title?></h1>
				<p class="lead"><?=getenv('site_title')?></p>
			</div>
		</div><!-- /header info -->
		<div class="row">
			<div class="col-md-6 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">Database</div>
					<div class="panel-body">
						<ul class="list-group">
							<li class="list-group-item">Connection: <?=$db_conn_ok ? $ok : $ng ?></li>
							<li class="list-group-item">Create schema: <?=$db_create_ok ? $ok : $ng ?></li>
							<li class="list-group-item">Create table: <?=$db_table_ok ? $ok : $ng ?></li>
							<li class="list-group-item">Create data: <?=$db_data_ok ? $ok : $ng ?></li>
							<li class="list-group-item">Read data: <?=$db_get_ok ? $ok : $ng ?></li>
						</ul>
					</div>
				</div>
			</div><!-- /db -->
			<div class="col-md-6 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">Redis</div>
					<div class="panel-body">
						<ul class="list-group">
							<li class="list-group-item">Connection: <?=$redis_conn_ok ? $ok : $ng ?></li>
							<li class="list-group-item">Write: <?=$redis_write_ok ? $ok : $ng ?></li>
							<li class="list-group-item">Read: <?=$redis_read_ok ? $ok : $ng ?></li>
						</ul>
					</div>
				</div>
			</div><!-- /redis -->
		</div><!-- /env test -->
	</div><!-- /.container -->
	<!-- JavaScript -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	</body>
</html>