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

	if (isset($_GET['io']))
	{
        	$io= (int)strip_tags($_GET['io']);
	}
	else
	{
        	$io= 0;
	}

	$cmfile = fopen("current_message.txt", "r") or die("Unable to open file!");
	if (!feof($cmfile))
	{
		$line= fgets($cmfile);
		$pieces= explode("#", $line);
		
		if (sizeof($pieces)>= 3)
		{
			$cm_line[1]= "#".$pieces[1];
			$cm_line[2]= "#".$pieces[2];
			$cm_line[3]= "#".$pieces[3];
		}
		else
		{
			die("Incomplete file!");
		}
	}
	fclose($cmfile);

	$msg= "";

	$cmfile = fopen("current_message.txt", "w") or die("Unable to open file!");
	$msgfile = fopen("messages.txt", "r") or die("Unable to open file!");
	while(!feof($msgfile))
	{
		$line= fgets($msgfile);
		$pieces= explode("|", $line);
		if (sizeof($pieces)== 3)
		{
			$fid= (int)$pieces[0];
			$fmessage_io1= preg_replace("/\r|\n/", "", $pieces[1]);
			$fmessage_io0= preg_replace("/\r|\n/", "", $pieces[2]);
			if ($fid== $id)
			{
				if ($io== 1)
				{
					$msg.= $fmessage_io1;
				}
				else
				{
					$msg.= $fmessage_io0;
				}
			}
			else
			{
				$msg.= $cm_line[$fid];
			}
		}
	}
	fclose($msgfile);

	fputs($cmfile, $msg);
	fclose($cmfile);
	
	echo $msg;

?>
