<?php
//	error_reporting(0);

	if (isset($_GET['id']))
	{
        	$id= (int)strip_tags($_GET['id']);
	}
	else
	{
        	$id= 0;
	}

	$msg= "";

	$msgfile = fopen("messages.txt", "r") or die("Unable to open file!");
	while(!feof($msgfile))
	{
		$line= fgets($msgfile);
		$pieces= explode("|", $line);
		if (sizeof($pieces)== 2)
		{
			$fid= (int)$pieces[0];
			$fmessage= preg_replace("/\r|\n/", "", $pieces[1]);
			if ($fid== $id)
			{
				$msg= $fmessage;
				$cmfile = fopen("current_message.txt", "w") or die("Unable to open file!");
				fputs($cmfile, $msg);
				fclose($cmfile);
			}
		}
	}
	fclose($msgfile);
	
	echo $msg;

?>
