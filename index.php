<?
include 'inc/calendar.php';
include 'class/Calendar.php';
?>

<!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8">
	<title>Booking Calendar</title>
	<meta name="description" content="Booking Calendar">
	<meta name="author" content="Erik Stenmark">
	<link href="css/style.css" rel="stylesheet">
	</head>
  
	<body>
	<div id="calendarnav" class="padd content-box">
	<div class="calnav">
	
  <span>
		<?php 
		if ($selectedmonth != null) {
			echo '
				<a href="?ym='.$prev.'"><</a> 
				<span id="monthnavhead" class="'.$year.'-'.$month.'">'.$month.' / '.'<a href="?y='.$year.'">'.$year.'</a> </span>
				<a href="?ym='.$next.'">></a>
			';
		} else {
			echo '
				<a href="?y='.$prev.'"><</a>
				<span id="yearnavhead" class="'.$year.'"><a href="?y='.$year.'">'.$year.'</a> </span>
				<a href="?y='.$next.'">></a>
			';
		}
		?>
	</span>

	</div>
		<div id="selection" class="padd-hor half-vert-pad"><p id="disparrive"></p><p id="displeave"></p></div>
		<div class="clear"></div>
	</div>
	<?php 
	if (isset($_SESSION['message'])) { 
		echo '<div class="padd flash-message">';
		echo $_SESSION['message']; unset($_SESSION['message']);
		echo '</div>';
	}
	

	?>		
	<div class="calact">
		<form action="book.php" method="post">
			<button type="button" id="clearlink">clear</button>
			<a><button type="button" id="now">back</button></a>
			<input type="hidden" name="arrive" id="arrivefield" />
			<input type="hidden" name="leave" id="leavefield" />
			<button type="submit" id="booklink">book</button>
		</form>
	</div>
	
  <div id="calendar">
		<?php
			if ($selectedmonth != null) {
				echo '<div class="month">';
				$calendar = new Calendar($month,$year,'m');
				$calendar->show();
			} else {
				echo '<div class="year">';
				for ($i = 1; $i <= 12; $i++) {
					$calendar = new Calendar($i,$year,'m');
					$calendar->show();
				}
			}
		?>
  </div>

	<script src="js/calendar.js"></script>
	</body>
</html>