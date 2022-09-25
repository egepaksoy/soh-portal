<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>API Site</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
</head>
<body>
<?php
date_default_timezone_set('Europe/Istanbul');


$day = strtolower(date('l', strtotime(date("y-m-d"))));
// $day = "wednesday";
$gunler = ["pazartesi","salı","çarşamba","perşembe","cuma","cumartesi"];
$time = [date("H"), date("i")];
// $time = ["8", "20"];
$wkn_times = ["08000840", "08500930", "09401020", "10301110", "11201200", "12101250", "13401420", "14301510", "15201600", "16001640"];
$friday_times = ["08000840", "08500930", "09401020", "10301110", "11201200", "12101250", "13401420", "14301510", "15201600"];
$wkns_times = ["08300910", "09100950", "10051045", "10451125", "11401220", "12201300"];


switch ($day) {
	case "monday":
		$day = "pazartesi";
		$times = $wkn_times;
		break;
	case "tuesday":
		$day = "salı";
		$times = $wkn_times;
		break;
	case "wednesday":
		$day = "çarşamba";
		$times = $wkn_times;
		break;
	case "thursday":
		$day = "perşembe";
		$times = $wkn_times;
		break;
	case "friday":
		$day = "cuma";
		$times = $friday_times;
		break;
	case "saturday":
		$day = "cumartesi";
		$times = $wkns_times;
		break;
	case "sunday":
		$day = "pazar";
		$times = $wkns_times;
		break;
}




if ($day)
{


	function get_lessons($day)
	{
	$tatil = false;
	$dir = "sqlite:db.sqlite";
	$dhb = new PDO($dir);
		$abidin = "SELECT lessons FROM day WHERE name='".$day."'";



		$result = $dhb->query($abidin);

		// günün ders programını çektik
		$a=0;
		foreach ($result as $row) 
		{
			$less = str_split($row[0], 4);
		}

		return $less;
	}


	$less = get_lessons($day);



	// guncel saat, ders saatleri, get_lessons donutu
	function which_lesson($times, $time, $less)
	{
		// kaçıncı derste ve dersin ne olduğunu aldık
		$not_tenefus = false;
		for ($a=0; $a < sizeof($times); $a++)
		{
			$ttimes = str_split($times[$a], 2);


			if (
				(intval($time[0]) == intval($ttimes[2]) && intval($time[1]) < intval($ttimes[3]))
				||
				(intval($time[0]) == intval($ttimes[0]) && intval($time[1]) > intval($ttimes[1]))
			)
			{
				// mevcut dersin id'si
				$ll = $less[$a];
				// kaçıncı derste olduğunu aldık
				$aa = $a;
				// dersin bas-bitis saatleri
				$w_time = $ttimes;
				// tenefus ise true oluyor
				$not_tenefus = true;
			}
		}

		//      dersse true   dersin saatleri   kacinci derste oldugu   ders id'si
		return [$not_tenefus, $w_time, $aa, $ll];
	}

	$not_tenefus = which_lesson($times, $time, $less)[0];
	$w_time = which_lesson($times, $time, $less)[1];
	$aa = which_lesson($times, $time, $less)[2];
	$ll = which_lesson($times, $time, $less)[3];


	// current lesson id $ll
	// time and minute $time[0], $time[1]
	// lessons infos $les
	// dersse not_tenefus true oluyor
	// w_time mevcut dersin saatlerini veriyor
	// rem_time dersin sonuna kaç dk kaldığını veriyor
	// $lesss = tum dersler

	
	function lesson_infos($ll)
	{
		// dersin id'sine göre dersin bilgilerini getiriyor
		$tatil = false;
		$dir = "sqlite:db.sqlite";
		$dhb = new PDO($dir);
		$husamettin = "SELECT * FROM lesson WHERE id='".$ll."'";

		$result = $dhb->query($husamettin);

		foreach ($result as $row) 
		{
			$les = $row;

		}

		return $les;
	}

	$les = lesson_infos($ll);


	function get_all_lessons($gunler)
	{
		$dir = "sqlite:db.sqlite";
		$dhb = new PDO($dir);
		$abd = "SELECT lessons FROM day;";

		$result = $dhb->query($abd);

		$all_lessons = [];

		$b = 0;
		$ttr = [];
		foreach($result as $row)
		{
			$all_lessons = str_split($row[0], 4);

			foreach($all_lessons as $lessonn)
			{
				$ttr[$b][] = $lessonn;
			}
			$b+=1;
		}

		return $ttr;
	}

	$lesss = get_all_lessons($gunler);


	function rem_munite($w_time, $not_tenefus, $time)
	{
		// dersin bitimine kaç dk kaldıgını hesaplıyor
		if ($not_tenefus && $w_time[2] == $time[0]) 
		{
			$rem_time = intval($w_time[3]) - intval($time[1]);
		}
		else if ($not_tenefus && $w_time[2] > $time[0]) 
		{
			$rem_time = 60-intval($time[1])+intval($w_time[3]);
		}

		return $rem_time;
	}

	$rem_time = rem_munite($w_time, $not_tenefus, $time);



	if ($not_tenefus == false)
	{
		$les["name"] = "Tenefüs";
	}

}
if ($day == "pazar") $tatil = true;
?>

	<?php if($tatil) {echo "<h1>Git Başımdaan!!<br>Senin hiç arkadaşın yok mu? çık dışarda oyna</h1>";} ?>



	<h1 class="text" style="text-align: center; margin-top: 10rem; font-size: 64px;" ><?php if ($tatil == false) echo $les["name"]; ?></h1>
	<h2 class="text" style="text-align: center; margin-bottom: 5rem;" ><?php if ($tatil == false) echo $les["teacher"]; ?></h2>
	<h1 class="text" style="text-align: center; font-size: 70px;" ><?php if ($tatil == false) echo $rem_time; ?></h1>
	<h3 class="text" style="text-align: center; margin-bottom: 10rem;" ><?php if ($tatil == false && $w_time) echo $w_time[0]."-".$w_time[1]."  ".$w_time[2]."-".$w_time[3]; ?></h3>





<!--
	<h1 class="text"><?php if ($tatil == false) echo "Günün Ödevleri"; ?></h1>
<?php 
$b = $les["homework"];
$homeworks = explode("|", $b);
foreach($homeworks as $c)
{
	echo "<h5 class='text'>$c</h5>";
}
// TODO: odev ekleme kodu yapılacak
?>
-->





<h1 style="text-align: center; margin-bottom: 3rem;" class="text">Ders Programı</h1>




<table style="width: 80%; margin: 0 auto; margin-bottom: 13rem;" class="table table-bordered">
  <thead class="thead-dark">
    <tr>
      <th scope="col" style='text-align: center;'>Gün</th>
<?php


for($r=0;$r<10;$r++)
{
	if($r==$aa && $not_tenefus) echo '<th scope="col" class="text-light bg-secondary" style="text-align: center;">'.($r+1).'. ders</th>';
	else echo '<th scope="col" style="text-align: center;">'.($r+1).'. ders</th>';
}

?>
    </tr>
  </thead>
  <tbody>
<?php
$a = 0;
foreach($lesss as $le)
{
	if($gunler[$a] == $day && $tatil == false) echo "<tr class='bg-secondary text-light'>";
	else echo "<tr>";
	echo "<th scope='row' style='text-align: center;'>".ucwords($gunler[$a])."</th>";
	
	$o = 0;
	foreach($le as $l)
	{
		if ($o == $aa && $gunler[$a] == $day && $not_tenefus) echo "<td style='text-align: center;' class='bg-dark'>".ucwords(lesson_infos($l)["name"])."</td>";
		else if ($o == $aa && $not_tenefus) echo "<td style='text-align: center;' class='bg-secondary text-light'>".ucwords(lesson_infos($l)["name"])."</td>";
		else echo "<td style='text-align: center;'>".ucwords(lesson_infos($l)["name"])."</td>";
		$o+=1;
	}

	for($f=sizeof($le); $f<10; $f++)
	{
		echo "<td style='text-align: center;'>-</td>";
	}

	echo "</tr>";
	$a+=1;
}
?>
  </tbody>
</table>




	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>
</html>
