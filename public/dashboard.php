<?php
	/*
	 * @author: Sunaina Punyani,Arpita Karkera
	 * @date: 6th December, 2016
	 * 
	 * Dashboard
	 *
	 */

	// authenticate
	require_once(__DIR__ . '/../includes/authenticate.php');

	// connect to database
	require_once(__DIR__ . '/../includes/dbconfig.php');

	$title = 'Dashboard';
	require_once(__DIR__ . '/../includes/header.php');
?>

<div class="container-fluid" id="p">
<div class="row">
<div class="col-sm-1">
</div>
<div class="col-sm-4" id="a"> 
<p style="font-size:40px"><?php echo htmlspecialchars($_SESSION['first_name']); ?>&nbsp;&nbsp;<?php echo htmlspecialchars($_SESSION['last_name']); ?></p>
</div>
<div class="col-sm-3">
</div>
<div class="col-sm-3 text-right" style="padding-top: 2.5%;">
<a href="myevents.php"><button class="button" id="q">My Events</button></a>
<br>
<br>
<a href="manage.php"><button class="button">Manage Events</button></a>
</div>
<div class="col-sm-2">
</div>  
</div>
</div>

<ul>
<li><a href="dashboard.php?cat=1#start">Events</a></li>
<li><a href="dashboard.php?cat=4#start">Lectures</a></li>
<li><a href="dashboard.php?cat=2#start">Workshops</a></li>
<li><a href="dashboard.php?cat=3#start">Competitions</a></li>
<li style="float:right;padding:1% 15%;"><div class="input-group">
	<form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
		<input type="text" name="q" class="form-control" placeholder="Search" value="<?php if (isset($_GET['q']) && !empty($_GET['q'])) echo htmlspecialchars($_GET['q']); ?>"><button type="submit" class="input-group-addon transparent"><span class="glyphicon glyphicon-search"></span></button>
	</form>
		</div>
</li>
</ul>
<div class="container-fluid text-center" style="padding-top: 2%; font-family: 'Raleway', sans-serif; font-size: 30px;">Committees</div>

<div class="container-fluid" style="width:100%;">
<div class="row">


<div class="col-sm-12" style="padding-left:6%;padding-right:6%;padding-top:1%;padding-bottom:2%;text-align:center;">
<?php
	// get the committee names
	$query = "SELECT * FROM committees";
	$result = mysqli_query($dbc, $query);
	$count = 0;
	if (mysqli_num_rows($result) != 0) {
		while ($com = mysqli_fetch_array($result)) {
?>
<a href="dashboard.php?com=<?php echo htmlspecialchars($com['committee_id']); ?>"><img class="img img-responsive inline-block" width="130" height="130" src="../images/<?php echo str_replace(' ','',strtolower($com['committee_name'])); ?>_logo.png"></a>
<?php		
			++$count;
			if ($count == 8)
				break;
		}
	}
?>

</div>
</div>
</div>
<?php
	// get the events
	if (isset($_GET['cat'])) {
		// get events that fall under specified category
		$category = mysqli_real_escape_string($dbc, trim($_GET['cat']));
		$query = "SELECT event_id, event_name, start_date, end_date, start_time, end_time, venue FROM events WHERE category = $category ORDER BY start_date";
	}
	else if (isset($_GET['com'])) {
		// get events that fall under specified committee
		$committee = mysqli_real_escape_string($dbc, trim($_GET['com']));
		$query = "SELECT event_id, event_name, start_date, end_date, start_time, end_time, venue FROM events WHERE committee = $committee ORDER BY start_date";
	}
	else if (isset($_GET['q']) && !empty($_GET['q'])) {
		// search for event with specified keywords
		$keywords = mysqli_real_escape_string($dbc, trim($_GET['q']));
		$query = "SELECT event_id, event_name, start_date, start_time, end_date, end_time, venue FROM events WHERE MATCH (event_name, description) AGAINST ('$keywords' IN NATURAL LANGUAGE MODE);";
	}
	else {
		// get all upcoming events
		$query = "SELECT event_id, event_name, start_date, end_date, start_time, end_time, venue FROM events ORDER BY start_date";
	}
?>

<div class="container-fluid text-center" style="padding-top: 2%;font-family: 'Raleway', sans-serif; font-size: 30px; " id="start">Upcoming</div>
<div class="container" id="h">
<?php
	$result = mysqli_query($dbc, $query);
	if (mysqli_num_rows($result) != 0) {
		while ($event = mysqli_fetch_array($result)) {
			
?>
<div class="cube" id="a">
<div class="row">
<div class="col-sm-2">
<img src="../images/star.png" class="img img-responsive" style="padding-top:11%; ">
</div>
<div class="col-sm-7">
<div style="text-align: left;padding-top: 1%;padding-left: 2%;">
<p style="font-size: 10px; color: gray; padding-bottom: 0px;"><?php echo 'EV'.htmlspecialchars(str_pad($event['event_id'], 3, '0', STR_PAD_LEFT)); ?></p>
<a href="event.php?event=<?php echo htmlspecialchars($event['event_id']); ?>"><p style="font-size:30px; padding-top: 0px;"><?php echo htmlspecialchars($event['event_name']) ?></p></a>
<p style="font-size:15px;"><span class="glyphicon glyphicon-calendar"></span>&nbsp;&nbsp;
<?php 
	if (empty($event['end_date'])) 
		echo htmlspecialchars(date('d M, Y', strtotime($event['start_date']))); 
	else 
		echo htmlspecialchars(date('d M, Y', strtotime($event['start_date']))).' - '.htmlspecialchars(date('d M, Y', strtotime($event['end_date']))); 
?></p>
<p style="font-size:15px;"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;
<?php
	if (empty($event['end_time']))
		echo htmlspecialchars(date('H:i A', strtotime($event['start_time'])));
	else
		echo htmlspecialchars(date('H:i A', strtotime($event['start_time']))).' - '.htmlspecialchars(date('H:i A', strtotime($event['end_time'])));;
?></p>
<p style="font-size:15px;"><span class="glyphicon glyphicon-map-marker"></span>&nbsp;&nbsp;
<?php
	echo htmlspecialchars($event['venue']);
?></p>
</div>
</div>
<div class="col-sm-3">
<div style="padding-top:12%;" class="text-center">
<a href="../controls/register.php?event=<?php echo htmlspecialchars($event['event_id']); ?>"><button id="tileb" class="button">Register</button></a>
<br>
<br>
<a href="event.php?event=<?php echo htmlspecialchars($event['event_id']); ?>"><button id="tile" class="button">More</button></a>
</div>
</div>
</div>
</div>
<br>
<?php
		}
	}
	else
		echo "No upcoming event!";
?>
</div>
</div>
<?php
	require_once(__DIR__ . '/../includes/footer.php');
?>