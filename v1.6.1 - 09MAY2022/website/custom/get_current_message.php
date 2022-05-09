<?php
	$myfile = fopen("current_message.txt", "r") or die("Unable to open file!");
	echo "#BEGIN".fgets($myfile);
	fclose($myfile);
?>
