<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ŞÖH Portal</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<!-- ÖNCELİKLE ZAMAN AYIRIP BU PROJEYİ SİZLER İÇİN GELİŞTİRDİĞİM İÇİN RİCA EDERİM İKİNCİ OLARAK DA KEYFİNE BAKIN :) -->
</head>
<body style="width:100%;">
<?php
	header("Refresh: 60");
	date_default_timezone_set("Turkey");

	$day = strtolower(date('l', strtotime(date("y-m-d"))));
	// $day = "monday";
	$time = [date("H"), date("i")];
	// $time = [12,41];

	$gunler = ["pazartesi","salı","çarşamba","perşembe","cuma","cumartesi"];

	$n_saatler = ["08000840", "08500930", "09401020", "10301110", "11201200", "12101250", "13401420", "14301510", "15201600", "16001640"];
	$c_saatler = ["08000840", "08500930", "09401020", "10301110", "11201200", "12101250", "13401420", "14301510", "15201600"];
	$h_saatler = ["08300910", "09100950", "10051045", "10451125", "11401220", "12201300"];

	$pazar = "";

	switch ($day) {
		case "monday":
			$day = "pazartesi";
			$times = $n_saatler;
			break;
		case "tuesday":
			$day = "salı";
			$times = $n_saatler;
			break;
		case "wednesday":
			$day = "çarşamba";
			$times = $n_saatler;
			break;
		case "thursday":
			$day = "perşembe";
			$times = $n_saatler;
			break;
		case "friday":
			$day = "cuma";
			$times = $c_saatler;
			break;
		case "saturday":
			$day = "cumartesi";
			$times = $h_saatler;
			break;
		case "sunday":
			$day = "pazar";
			$times = $wkns_times;
			$pazar = true;
			break;
	}

	
	// gunluk dersleri array olarak getiriyor
	function get_day_lessons($day, $class)
	{
		$dir = "sqlite:db.sqlite";
		$database = new PDO($dir);
		$qry = "SELECT lessons FROM ".$class."_day WHERE name='$day'";
		$results = $database->query($qry);

		foreach ($results as $result)
		{
			$row = str_split($result[0], 4);
		}

		if ($row == NULL) return "tatil";

		return $row;
	}
	// [fiz1, fiz1, kim1, kim1, ...]

	
	// gunleri array halinde dondurup dersleri getiriyor
	function get_all_lessons($class)
	{
		$dir = "sqlite:db.sqlite";
		$database = new PDO($dir);
		$qry = "SELECT lessons FROM ".$class."_day";
		$results = $database->query($qry);

		$week = [];

		foreach ($results as $result)
		{
			$week[] = $result[0];
		}

		return $week;

	}
	// [fiz1fiz1kim1kim1...,arp1arp1biy1biy1]


	// tenefus olup olmadığını, ders saati,hangi derste, derse/tenefüse ne kadar kaldığını ve dersin aralığını belirliyor
	function get_lesson($time, $times, $get_day_less)
	{
		if ($get_day_less == "tatil")
		{
			return false;
		}
		for ($less=0; $less < sizeof($times); $less++)
		{
			//								 08 00 08 40
			$current_time_periot = str_split($times[$less], 2);
			$lesson_start_munite = intval($current_time_periot[0])*60+intval($current_time_periot[1]);
			$lesson_end_munite = intval($current_time_periot[2])*60+intval($current_time_periot[3]);
			$time_munite = intval($time[0])*60+intval($time[1]);


			



			// eğer saat ders saatleri içinde ise
			if 
			(
				$lesson_start_munite < $time_munite+1 &&
				$time_munite-1 < $lesson_end_munite
			)
			{
				$current_lesson = $get_day_less[$less]; // tur1
				$tenefus = false;
				$remaining_time = $lesson_end_munite - $time_munite; // 32
				$lesson_periot = $current_time_periot; // [08,50,09,30]
				$lesson_number = $less;

				return [$tenefus,$lesson_number,$current_lesson, $remaining_time, $lesson_periot];
			}

			// eğer son dersse
			else if 
			(
				$less+1 > sizeof($times) || $less+1 == sizeof($times)
			)
			{
				return false;
			}


			// onceki ve sonraki ders arasındaki vakit arligi
			$iki_ders_arasi_fark = (intval(str_split($times[$less+1], 2)[0])*60+intval(str_split($times[$less+1], 2)[1]))-$lesson_end_munite;

			// tenefusse
			if 
			(
				$time_munite > $lesson_end_munite && $time_munite-$lesson_end_munite < $iki_ders_arasi_fark
			)
			{
				$current_lesson = $get_day_less[$less+1];
				$tenefus = true;
				$remaining_time = $iki_ders_arasi_fark-$time_munite+$lesson_end_munite;
				$lesson_periot = [str_split($times[$less], 2)[2], str_split($times[$less], 2)[3], str_split($times[$less+1], 2)[0], str_split($times[$less+1], 2)[1]];
				$lesson_number = $less+1;

				return [$tenefus,$lesson_number,$current_lesson, $remaining_time, $lesson_periot];
			}
		}

	}
	// false -> okul çıkışında
	// false,2, tur1, 23, [08,00,08,40] -> derste
	// true,3, tur1, 2, [08,50,09,30] -> tenefüste


	// ders idsi ve sınıf ile dersin bilgilerine erişiyor
	function lesson_infos($lesson_id, $class)
	{
		if ($lesson_id == "kul1")
		{
			return ["kul1", "kulüp", "-", ""];
		}

		$dir = "sqlite:db.sqlite";
		$database = new PDO($dir);
		$query = "SELECT * FROM $class WHERE id='$lesson_id'";
		
		$results = $database->query($query);

		foreach ($results as $result)
		{
			$lesson_id = $result[0];
			$lesson_name = $result[1];
			$lesson_teacher = $result[2];
			$homework = $result[3];
		}

		return [$lesson_id, $lesson_name, $lesson_teacher, $homework];
	}
	// tur1, türkçe 1, hakan aydoğdu, 100 soru

?>

<?php
	$get_day_lessons = get_day_lessons($day, "a");
	$get_all_lessons = get_all_lessons("a");
	$get_lesson = get_lesson($time, $times, $get_day_lessons);
	$lesson_infos = lesson_infos($get_lesson[2], "a");
?>



<?php
	if ($get_lesson != false)
	{
		echo '<div style="margin-top: 20rem;">';	
	
		if ($get_lesson[0]) echo '<h1 class="text" style="text-align: center; font-size: 64px;" >Tenefüs</h1>';
		else echo '<h1 class="text" style="text-align: center; font-size: 64px;" >Ders</h1>';
?>

<h1 class="text" style="text-align: center; font-size: 100px;" ><?php echo "<p style='font-size: 32px;'>Zil çalmasına kalan süre:<br></p>".$get_lesson[3]; ?></h1>

<h3 class="text" style="text-align: center; margin-bottom: 10rem; font-size: 40px;" ><?php echo $get_lesson[4][0]."-".$get_lesson[4][1]."  ".$get_lesson[4][2]."-".$get_lesson[4][3]; ?></h3>
<?php 
} 
else echo '<div style="margin-top: 10rem;"></div>';
?>

</div>
<hr>



<?php 
$classes = ["a","b","c","d","e"];

foreach ($classes as $class)
{
	$get_day_lessons = get_day_lessons($day, $class);
	$get_all_lessons = get_all_lessons($class);
	$get_lesson = get_lesson($time, $times, $get_day_lessons);
	$lesson_infos = lesson_infos($get_lesson[2], $class);
?>


<!--- HTML KODU --->





<!------------------------------------------------------->

<!---SINIF--->
<h1 style="font-size: 90px; text-align: center; margin-bottom: 5rem; margin-top: 5rem;">12-<?php echo ucwords($class); ?></h1>
<!--- DERS ADI --->
<h1 class="text" style="text-align: center; margin-top: 5rem; font-size: 64px;" ><?php echo ucwords($lesson_infos[1]); ?></h1>
<!--- DERS HOCASI --->
<h2 class="text" style="text-align: center; margin-bottom: 5rem; font-size: 40px;" ><?php echo ucwords($lesson_infos[2]); ?></h2>





<h1 style="text-align: center; margin-bottom: 3rem;" class="text">Ders Programı</h1>



<table style="margin-left: auto; margin-right: auto; margin-bottom: 10rem; font-size: 10px;" class="table table-bordered">
<thead>
<tr>
  <th scope="col" style='text-align: center;'>Gün</th>
<?php


for($r=0;$r<10;$r++)
{
	$ders_saatleri = str_split($n_saatler[$r], 2);
if ($r == $get_lesson[1] && ($day != "cumartesi" || $class != "d") && $get_lesson != false)
{
	echo '<th scope="col" class="text-light bg-secondary" style="text-align: center;">'.$ders_saatleri[0].'-'.$ders_saatleri[1].' '.$ders_saatleri[2].'-'.$ders_saatleri[3].'</th>';
}
else 
{
	echo '<th scope="col" style="text-align: center;">'.$ders_saatleri[0].'-'.$ders_saatleri[1].' '.$ders_saatleri[2].'-'.$ders_saatleri[3].'</th>';
}
}

?>
</tr>
</thead>
<tbody>
<?php
$gunler = ["pazartesi","salı","çarşamba","perşembe","cuma","cumartesi"];

if ($class == "d") $gunler = [$gunler[0], $gunler[1], $gunler[2], $gunler[3], $gunler[4]];
for ($a=0;$a<sizeof($gunler);$a++)
{
	$today = $gunler[$a];
	$get_day_lessons = get_day_lessons($today,$class);


	if($gunler[$a] == $day && ($day != "cumartesi" || $day != "d") && $get_lesson != false) echo "<tr class='bg-secondary text-light'>";
	else echo "<tr>";
	echo "<th scope='row' style='text-align: center;'>".ucwords($gunler[$a])."</th>";

	$o = 0;
	foreach ($get_day_lessons as $lesson)
	{
		$ders = lesson_infos($lesson, $class)[1];
		$current_lesson_teacher = lesson_infos($lesson, $class)[2];
		if ($day == "cumartesi" && $class == "d" && $get_lesson != false) echo "<td style='text-align: center;'>".ucwords($lesson)."</td>";
		else if (($o == $get_lesson[1] && $gunler[$a] == $day) && ($day != "cumartesi" || $day != "d") && $get_lesson != false) echo "<td style='text-align: center;' class='bg-dark'>".ucwords($ders)." (".ucwords($current_lesson_teacher).")"."</td>";
		else if ($o == $get_lesson[1] && ($day != "cumartesi" || $day != "d") && $get_lesson != false) echo "<td style='text-align: center;' class='bg-secondary text-light'>".ucwords($ders)." (".ucwords($current_lesson_teacher).")"."</td>";
		else echo "<td style='text-align: center;'>".ucwords($ders)." (".ucwords($current_lesson_teacher).")"."</td>";
		$o+=1;
	}
	
	for($f=sizeof($get_day_lessons); $f<10; $f++)
	{
		echo "<td style='text-align: center;'>-</td>";
	}

	echo "</tr>";

}
?>
</tbody>
</table>

<hr>

<?php
}

?>


<footer class="footer" style="text-align: center; height: 50px; font-size: 16px; margin: auto;">
        <span class="text-muted" style="margin: auto; font-style: italic;">Bir Ege Paksoy Ürünü</span>
<br>
        <span class="text" style="margin: auto; font-size: 1px;">Pi sayısı dairenin çevre uzunluğunun çapına oranıdır</span>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>
</html>
