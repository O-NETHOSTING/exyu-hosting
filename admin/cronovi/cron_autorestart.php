<?php
$fajl = "login";

include("/home/admin/web/your-hosting.com/public_html/konfiguracija.php");
include("/home/admin/web/your-hosting.com/public_html/admin/includes.php");
require_once('/home/admin/web/your-hosting.com/public_html/includes/libs/lgsl/lgsl_class.php');
require("/home/admin/web/your-hosting.com/public_html/includes/libs/phpseclib/SSH2.php");
require_once("/home/admin/web/your-hosting.com/public_html/includes/libs/phpseclib/Crypt/AES.php");

error_reporting(E_ERROR | E_WARNING | E_PARSE);
/*------------------------------------------------------------------------------------------------------+
 * AUTO RESTART
/*------------------------------------------------------------------------------------------------------*/
$hour = date('H');
$serverx = mysql_query("SELECT * FROM `serveri` WHERE `autorestart`='{$hour}' AND `startovan`='1'");

echo "CRON AUTORESTART !<br />\n";
echo "Restarting all servers scheduled for {$hour}:00 !<br />\n";

while($row = mysql_fetch_array($serverx))
{
	
	
        $ip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$row['ip_id']."'");
	$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$row['box_id']."'");
	$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$row['ip_id']."'");
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$row['id']."'");
	$serverid = $row['id'];


        stop_server($ip['ip'], $box['sshport'], $server['username'], $server['password'], $serverid, "admin", TRUE);
	start_server($ip['ip'], $box['sshport'], $server['username'], $server['password'], $serverid, "admin", TRUE);
				
    echo $row['name'] ."\n";
	
}

echo "Finished !";

function start_server($ip, $port, $username, $password, $serverid, $klijentid, $restart)
{
	global $jezik;

	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	
	if($restart == FALSE)
	{	
		if($server['startovan'] == "1")
		{
			echo $jezik['text291'];
		}
	}

	if (!function_exists("ssh2_connect")) echo $jezik['text290'];

	if(!($con = ssh2_connect($ip, $port))) echo $jezik['text292'];
	else 
	{
		if(!ssh2_auth_password($con, $username, $password)) echo $jezik['text293'];
		else 
		{
			if($server['igra'] == "1")
			{
				$komanda = $server["komanda"];
				$komanda = str_replace('{$ip}', $ip, $komanda);
				$komanda = str_replace('{$port}', $server['port'], $komanda);
				$komanda = str_replace('{$slots}', $server['slotovi'], $komanda);
				$komanda = str_replace('{$map}', $server['map'], $komanda);
				$komanda = str_replace('{$fps}', $server['fps'], $komanda);	
			}
			else if($server['igra'] == "3")
			{
				$komanda = $server["komanda"];

				// Max Ram ( SLOT * 51.2)
				$mr = ($server['slotovi'] * 51.2);

				// Min Ram
				$minr = "512";

				$komanda = str_replace('{$maxram}', $mr, $komanda);
				$komanda = str_replace('{$minram}', $minr, $komanda);		
			}
			else
			{
				$komanda = $server["komanda"];
			}

			$stream = ssh2_shell($con, 'vt102', null, 80, 24, SSH2_TERM_UNIT_CHARS);
			fwrite( $stream, "screen -mSL $username".PHP_EOL);
			sleep(1);
			fwrite( $stream, "$komanda".PHP_EOL);
			sleep(1);
			fwrite( $stream, "rm log.log".PHP_EOL);
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) 
			{
				$data .= $line;
			}
			query_basic("UPDATE `serveri` SET `startovan` = '1' WHERE `id` = '".$serverid."'");
			echo 'startovan';
		}
	}	
}


function stop_server($ip, $port, $username, $password, $serverid, $klijentid, $restart)
{
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");

	if($restart == FALSE)
	{
		if($server['startovan'] == "0")
		{
			echo "Server mora biti startovan!";
		}
	}

	if (!function_exists("ssh2_connect")) echo "SSH2 PHP extenzija nije instalirana";

	if(!($con = ssh2_connect($ip, $port))) echo "Ne mogu se spojiti na server";
	else 
	{
		if(!ssh2_auth_password($con, $username, $password)) echo "Neta??ni podatci za prijavu";
		else 
		{
			$stream = ssh2_shell($con, 'vt102', null, 80, 24, SSH2_TERM_UNIT_CHARS);
			fwrite( $stream, 'kill -9 `screen -list | grep "'.$username.'" | awk {\'print $1\'} | cut -d . -f1`'.PHP_EOL);
			sleep(1);
			fwrite( $stream, 'screen -wipe'.PHP_EOL);
			sleep(1);
			
			$data = "";
			
			while($line = fgets($stream)) 
			{
				$data .= $line;
			}
			query_basic("UPDATE `serveri` SET `startovan` = '0' WHERE `id` = '".$serverid."'");			
			echo 'stopiran';
		}
	}	
}