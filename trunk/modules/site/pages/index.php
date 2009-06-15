<?php
function tm()
{
   $a = explode(' ', microtime());
   return $a[0].$a[1];
}
$start=tm();

$a='aaa';
while($a<'zzz') {
echo $a.' ';
$a++;
}
$stop=tm();
echo "<hr>Генерация: ".substr(($stop-$start), 0, 5)." секунд.";
?>