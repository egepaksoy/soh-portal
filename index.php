<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>API Site</title>
</head>
<body>
<?php
date_default_timezone_set('Europe/Istanbul');

$day = strtolower(date('l', strtotime(date("y-m-d"))));
$time = [date("H"), date("i"), date("s")];

switch ($day) {
	case "monday":
		$day = "pazartesi";
		break;
	case "tuesday":
		$day = "salı";
		break;
	case "wednesday":
		$day = "çarşamba";
		break;
	case "thursday":
		$day = "perşembe";
		break;
	case "friday":
		$day = "cuma";
		break;
	case "saturday":
		$day = "cumartesi";
		break;
	case "sunday":
		$day = "pazar";
		break;
}


$dir = "sqlite:db.sqlite";
$dhb = new PDO($dir);
$abidin = "SELECT lessons FROM day WHERE name='".$day."'";


$result = $dhb->query($abidin);

foreach ($result as $row) 
{
	echo $row[0];
}
?>
</body>
</html>
