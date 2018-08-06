<?php
$yearmonth = date('Y-m');
$year = date('Y');
$month = date('m');

if (isset($_GET['ym'])) { 
	if (isset($_SESSION['y'])) { unset($_SESSION['y']); }
	$_SESSION['ym'] = filter_input(INPUT_GET,'ym',FILTER_SANITIZE_STRING); 
} elseif (isset($_GET['y'])){
	if (isset($_SESSION['ym'])) { unset($_SESSION['ym']); }
	$_SESSION['y'] = filter_input(INPUT_GET,'y',FILTER_SANITIZE_STRING);
}

// Get prev & next month
if (isset($_GET['ym'])) {
	$selectedyear= null;
	$selectedmonth = filter_input(INPUT_GET,'ym',FILTER_SANITIZE_STRING);
} elseif (isset($_GET['y'])) {
	$selectedmonth = null;
	$selectedyear= filter_input(INPUT_GET,'y',FILTER_SANITIZE_STRING);
} elseif (isset($_SESSION['ym'])) {
	$selectedyear= null;
	$selectedmonth = $_SESSION['ym'];
} elseif (isset($_SESSION['y'])) {
	$selectedmonth = null;
	$selectedyear= $_SESSION['y'];
} else {
	$selectedmonth = date('Y-m');
	$_SESSION['ym'] = date('Y-m');
}

if ($selectedmonth != null) {
	$yearmonth = $selectedmonth;
	$ym = explode('-',$yearmonth);
	$year = $ym[0];
	$month = $ym[1];
}

if (isset($selectedyear) && $selectedyear != null) {
	$year = $selectedyear;
}

$ymd = $year.'-'.$month."-01";
$timestamp = strtotime($ymd);
if ($timestamp == false) {
	$timestamp = time();
}

if ($selectedmonth != null) {
	$prev = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)-1, 1, date('Y', $timestamp)));
	$next = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)+1, 1, date('Y', $timestamp)));
} else {
	$prev = date('Y', mktime(0, 0, 0, 1, 1, date('Y', $timestamp)-1));
	$next = date('Y', mktime(0, 0, 0, 1, 1, date('Y', $timestamp)+1));
}

?>