<?php
$file = './hgy.txt';
$fp = fopen($file,'r');
while($str = fgets($fp)){
	echo '='.$str;
}
