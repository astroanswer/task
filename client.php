<?



$url="http://127.0.0.1/task/webservice/index.php";


//  Initiate curl
$ch = curl_init();
// Disable SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);

// Will dump a beauty json :3

$x=json_decode($result);
$s=sizeof($x)

?>

<table style="width:500px;border:1px solid #AAAAAA;">
<tr>
<td width="50%">Date</td>	
<td width="50%">Engineer</td>		
</tr>		
<?
for ($dt=0;$dt<$s;$dt++){
	$next_date=$x[$dt]->schedule;
	$next_am=$x[$dt]->AM;
	$next_pm=$x[$dt]->PM;
	$next_dow= date("l", strtotime($next_date));
?>
<tr>
<td width="50%"><?=$next_date . '('.$next_dow.')'?></td>	
<td width="50%">AM: <?=$next_am?>
	<br>
	PM: <?=$next_pm?>
	<br><br>
	</td>		
</tr>	
<?
}
?>
</table>