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
// $day = "monday";
$time = [date("H"), date("i")];
// $time = ["13", "42"];
$wkn_times = ["08000840", "08500930", "09401020", "10301110", "11201200", "12101250", "13401420", "14301510", "15201600", "16001640"];
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
		$a = 0;
		foreach ($wkn_times as $time)
		{
			$a+=1;
			if ($a < 10)
			{
				$times[] = $time;
			}
		}
		break;
	case "saturday":
		$day = "cumartesi";
		$times = $wkns_times;
		break;
	case "sunday":
		$day = false;
		break;
}

if ($day)
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


	// current lesson id $ll
	// time and minute $time[0], $time[1]
	// lessons infos $les
	// dersse not_tenefus true oluyor
	// w_time mevcut dersin saatlerini veriyor
	// rem_time dersin sonuna kaç dk kaldığını veriyor

	
	// dersin id'sine göre dersin bilgilerini getiriyor
	$husamettin = "SELECT * FROM lesson WHERE id='".$ll."'";

	$result = $dhb->query($husamettin);

	foreach ($result as $row) 
	{
		$les = $row;

	}

	/*
	 * TODO: bazı değişkenleri fonksiyona çevirmek lazım
	// ful ders programı
	$abd = "SELECT lessons FROM day";

	$result = $dhb->query($abd);
	foreach ($result as $row)
	{
		$all_in = str_split($row[0], 4);
		$in_all = [];
		foreach ($all_in as $in)
		{
			$in_all[] == $les
		}
	}*/



	// dersin bitimine kaç dk kaldıgını hesaplıyor
	if ($not_tenefus && $w_time[2] == $time[0]) 
	{
		$rem_time = intval($w_time[3]) - intval($time[1]);
	}
	else if ($not_tenefus && $w_time[2] > $time[0]) 
	{
		$rem_time = 60-intval($time[1])+intval($w_time[3]);
	}

}
else
{
	$tatil = true;
}
?>

	<?php if($tatil) {echo "<h1>Git Başımdaan!!<br>Senin hiç arkadaşın yok mu? çık dışarda oyna</h1>";} ?>



	<h1 class="text"><?php if ($tatil == false) echo $les["name"]; ?></h1>
	<h2 class="text"><?php if ($tatil == false) echo $les["teacher"]; ?></h2>
	<h1 class="text"><?php if ($tatil == false) echo $rem_time; ?></h1>
	<h3 class="text"><?php if ($tatil == false) echo $w_time[0]."-".$w_time[1]."  ".$w_time[2]."-".$w_time[3]; ?></h3>
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
<h1 class="text">Ders Programı</h1>




<table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Gün</th>
      <th scope="col">1. ders</th>
      <th scope="col">2. ders</th>
      <th scope="col">3. ders</th>
      <th scope="col">4. ders</th>
      <th scope="col">5. ders</th>
      <th scope="col">6. ders</th>
      <th scope="col">7. ders</th>
      <th scope="col">9. ders</th>
      <th scope="col">10. ders</th>
    </tr>
  </thead>
  <tbody>
    <tr>
<?php

?>
      <th scope="row">Pazartesi</th>
      <td>Mark</td>
      <td>@mdo</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>@mdo</td>
      <td>@mdo</td>
      <td>@mdo</td>
    </tr>
    <tr>
      <th scope="row">Salı</th>
      <td>Jacob</td>
      <td>Thornton</td>
      <td>@fat</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
    </tr>
    <tr>
      <th scope="row">Çarşamba</th>
      <td>Larry</td>
      <td>@twitter</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
    </tr>
    <tr>
      <th scope="row">Perşembe</th>
      <td>Larry</td>
      <td>@twitter</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
    </tr>
    <tr>
      <th scope="row">Cuma</th>
      <td>Larry</td>
      <td>@twitter</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
    </tr>
    <tr>
      <th scope="row">Cumartesi</th>
      <td>Larry</td>
      <td>@twitter</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>Otto</td>
      <td>-</td>
      <td>-</td>
      <td>-</td>
    </tr>
  </tbody>
</table>
//-->




	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>
</html>
