<?php
//	error_reporting(0);
	
	$LIST= 0;
	$EDIT= 1;
	$DO_EDIT= 11;
	$DELETE= 2;
	$ADD= 3;
	$DO_ADD= 31;
	
//------------------------------------------------------------------------------------------------------------------------	
	if (isset($_GET['id']))
	{
        	$id= (int)strip_tags($_GET['id']);
	}
	else
	{
        	$id= 0;
	}

	if (isset($_GET['action']))
	{
        	$action= (int)strip_tags($_GET['action']);
	}
	else
	{
        	$action= $LIST;
	}

	if (isset($_REQUEST['msg']))
	{
        	$msg= strip_tags($_REQUEST['msg']);
	}
	else
	{
        	$msg= "Void message";
	}


//------------------------------------------------------------------------------------------------------------------------	
	$page= '
<html lang="en">


<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Chrome, Firefox OS and Opera -->
	<link rel="shortcut icon" href="/favicon.ico?v=476mA4zprB" />
	<!-- Tab Color iOS Safari -->
	<meta name="apple-mobile-web-app-title" content="Metrici.ro" />
	<meta name="application-name" content="Metrici.ro" />
	<!-- Tab Color Android Chrome -->
	<meta name="theme-color" content="#e11422" />
	<!-- CSS -->
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/icomoon-style.css">
	<!-- JavaScript -->
	<script defer src="../javascript/jquery-3.6.0.min.js"></script>
	<script defer src="../javascript/main.js"></script>
    <title>Custom messages</title>
</head>
<body>
	<section>
		<div class="top_container update_container">
			<div class="mid_container">
	';
	
//------------------------------------------------------------------------------------------------------------------------	
	switch ($action)
	{
//------------------------------------------------------------------------------------------------------------------------	
		case $LIST:
			$cmfile = fopen("current_message.txt", "r") or die("Unable to open file!");
			$page.= '
    	<div class="title">
         	<h1>Current message: &nbsp;&nbsp;&nbsp;<span class="setting">'.fgets($cmfile).'</span></h1>
       	</div>
		<hr>
        <div class="custom-table-wrapper">
			<table border="0" cellspacing="10" class="custom-table">
				<tr>
					<th><b>ID</b></th>
					<th><b>Message</b></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tr>
			';
			
			$msgfile = fopen("messages.txt", "r") or die("Unable to open file!");
			$max_id= 1;
			while(!feof($msgfile))
			{
				$line= fgets($msgfile);
				$pieces= explode("|", $line);
				if (sizeof($pieces)== 2)
				{
					$fid= (int)$pieces[0];
					$fmessage= preg_replace("/\r|\n/", "", $pieces[1]);
				
					if ($fid> $max_id)
					{
						$max_id= $fid;
					}
				
					$page.= '
				<tr>
					<td>'.$fid.'</td>
					<td>'.$fmessage.'</td>
					<td><a href="?action='.$EDIT.'&id='.$fid.'" class="button upload_button">Edit</a></td>
					<td><a href="?action='.$DELETE.'&id='.$fid.'" class="button file_input_label">Delete</a></td>
				</tr>
					';
				}
			}
			fclose($msgfile);
			
			$max_id++;
			
			$page.= '
			</table>
			<div class="inner_buttons">
			<a href="?action='.$ADD.'&id='.$max_id.'" class="button upload_button">Add new message</a>
			</div>
		</div>
			';
			fclose($cmfile);
			break;
			
			
//------------------------------------------------------------------------------------------------------------------------	
		case $EDIT:
			$page.= '
    	<div class="title">
         	<h1>Edit message with ID:  &nbsp;&nbsp;&nbsp;<span class="setting">'.$id.'</span></h1>
       	</div>
		<div class="log_container">
			<form method="post" action="?action='.$DO_EDIT.'&id='.$id.'">
			';
	
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
						$page.= '
				<input type="text" name="msg" id="msg" value="'.$fmessage.'"><br>
						';
			
					}
				}
			}
			fclose($msgfile);
			
			$page.= '
				<table border="0">
					<tr>
					<td><input type="submit" class="button upload_button" value="Save"></td>
					<td><a href="?action='.$LIST.'" class="button file_input_label">Cancel</a></td>
					</tr>
				</table>
			</form>
			<br>
		</div>
			';
			
			break;			
	
//------------------------------------------------------------------------------------------------------------------------	
		case $DO_EDIT:
			$new_content= "";
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
						$new_content.= $id."|".$msg."\r\n";
					}
					else
					{
						$new_content.= $line;
					}
				}
			}
			fclose($msgfile);
			
			$msgfile = fopen("messages.txt", "w") or die("Unable to open file!");
			fputs($msgfile, $new_content);
			fclose($msgfile);			

			header("Location: ?action=".$LIST);
			die();

			break;


//------------------------------------------------------------------------------------------------------------------------	
		case $ADD:
			$page.= '
    	<div class="title">
         	<h1>Add message with ID:  &nbsp;&nbsp;&nbsp;<span class="setting">'.$id.'</span></h1>
       	</div>
		<div class="log_container">
			<form method="post" action="?action='.$DO_ADD.'&id='.$id.'">
				<input type="text" name="msg" id="msg" value=""><br>
				<table border="0">
					<tr>
					<td><input type="submit" class="button upload_button" value="Save"></td>
					<td><a href="?action='.$LIST.'" class="button file_input_label">Cancel</a></td>
					</tr>
				</table>
			</form>
			<br>
		</div>
			';
			
			break;			

//------------------------------------------------------------------------------------------------------------------------	
		case $DO_ADD:
			$msgfile = fopen("messages.txt", "a") or die("Unable to open file!");
			$msg.= "\r\n";
			$line= $id."|".$msg;
			fputs($msgfile, $line);
			fclose($msgfile);
			header("Location: ?action=".$LIST);
			die();
			break;


//------------------------------------------------------------------------------------------------------------------------	
		case $DELETE:
			$new_content= "";
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
					}
					else
					{
						$new_content.= $line;
					}
				}
			}
			fclose($msgfile);
			
			$msgfile = fopen("messages.txt", "w") or die("Unable to open file!");
			fputs($msgfile, $new_content);
			fclose($msgfile);			

			header("Location: ?action=".$LIST);
			die();
		
			break;

//------------------------------------------------------------------------------------------------------------------------	
	}
	
	$page.= '
			</div>
		</div>
	</section>
<footer>
	<span class="version"><a href="https://www.metrici.ro/products/metrici-display" target="_blank" title="Go to Display documentation">Display Controller</a> - Version: 1.5.3</span>
	<span class="copyright"><a href="https://www.metrici.ro/" target="_blank" title="Go to Metrici Website">Metrici</a> &#64; 2022 - All Rights Reserved.</span>
</footer>
</body>
</html>
	';
	
	echo $page;
?>
