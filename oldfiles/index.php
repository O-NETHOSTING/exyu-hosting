<?php
session_start();

$naslov = "Početna";
$fajl = "index";

include("includes.php");

$br = @file_get_contents('preuzimanja.txt');

$br = number_format($br, 0);

$slajd = mysql_query("SELECT * FROM `slajdovi`");

include("./assets/header.php");
?>
<?php     	$replies = query_numrows("SELECT `id` FROM `tiketi` WHERE `user_id` = '{$_SESSION[klijentid]}' AND `status` = '2'");
            if($replies > 0)
            {
                if($replies > 1) $textet = "odgovorena tiketa";
                else $textet = "odgovoren tiket";
?>
            <div id="brzakcija" style="display: block;">
                <p id="h4">Informacija</p>
                <p id="h2">Imate <z><?php echo $replies."</z> ".$textet; ?>. <a href="gp-podrska.php">Klik</a>.
                </p>
            </div> <!-- #brzaakcija end -->
<?php } ?>
			<div id="slajder">
				<div id="slideshow" style="float: left;">
					<div id="slidesContainer">
						<div class="cycle-slideshow"
							data-cycle-auto-height="4:3"
							data-cycle-timeout="6000"	
							data-cycle-prev="#prev"
							data-cycle-next="#next"
							data-cycle-pause-on-hover="true"
							data-cycle-speed="300"	
							data-cycle-slides="> div"
							>	
						<span class="controls">
							<div class="leftControl" id="prev" href="#"></div>
							<div class="rightControl" id="next" href="#"></div>	
						</span>
<?php
							$i = 0;
							while($row = mysql_fetch_array($slajd)) { $i = $i+1;
?>
							<div class="slide">
								<p id="h4"><z>#<?php echo $i; ?></z> <?php echo $row['naslov']; ?><span id="h1" class="right"><?php echo $row['datum']; ?></span></p>
								<div id="content" style="width: 400px;height: 148px;background: url('./assets/img/slider/2.png') no-repeat">
									<img width="398" height="148" src="<?php echo $row['slika']; ?>" />
									<div id="msg">
										<p><?php echo $row['text']; ?></p>
									</div>
								</div>
							</div>
<?php
							}
?>
						</div>
					</div> 
				</div>
				<div id="skinics">
					<a href="#" onclick="skidanjecs()">
						<i class="icon-cloud-download"></i>
					</a>
					<p id="dwn"><?php echo $jezik['text31']; ?><br /><span style="font-size: 12px;" rel="tipr" title="Skidanja sa sajta"><?php echo $jezik['text32']; ?>: <z><?php echo $br; ?></z></span> <span style="font-size: 9px; font-style: italic;"><a href="#">Više...</a></p>
				</div>
				<div id="etnovosti">
					<h3>Novosti na sajtu</h3>
					<ul>
						<li><span style="color: white; font-size: 10px;">23.01.2016, 23:54</span> - <a>Update v2.1.1</a></li>
						<li><span style="color: white; font-size: 10px;">15.02.2015, 12:54</span> - <a>Nova verzija sajta v2.1.0</a></li>
						<li><span style="color: white; font-size: 10px;">19.11.2014, 16:22</span> - Informacije o odgovoru na tiketu</li>
						<li><span style="color: white; font-size: 10px;">14.10.2014, 12:42</span> - Promenjen je deo kod slidera</li>
						<li><span style="color: white; font-size: 10px;">16.09.2014, 12:34</span> - Dodato je plaćanje putem PayPal-a</li>
						<li><span style="color: white; font-size: 10px;">13.09.2014, 02:14</span> - Mogućnost biranja 2. teme na sajtu</li>
					</ul>
				</div>
			</div><!-- #slajder end -->
			
			<div id="sep"></div> <!-- #sep end -->

			<div id="serverscroll">
				<ul id="skrolsrv">
<?php
					require("./includes/libs/lgsl/lgsl_class.php");

					$query = mysql_query("SELECT s.igra, bi.ip, s.port, s.id, s.rank 
						FROM serveri s, box b, boxip bi 
						WHERE s.status = 'Aktivan' 
						AND s.startovan = '1' 
						AND s.free = 'Ne' 						
						AND b.boxid = s.box_id 
						AND bi.ipid = s.ip_id 
                                                ORDER BY RAND()
						LIMIT 5");

					while($row = mysql_fetch_assoc($query)) 
					{
						if($row['igra'] == "1") { $mapc = "cs"; $querytype = "halflife"; }
						else if($row['igra'] == "2") { $mapc = "samp"; $querytype = "samp"; }
						else if($row['igra'] == "3") { $mapc = "minecraft"; $querytype = "minecraft"; }

						$serverlgsl = lgsl_query_cached($querytype, $row['ip'], $row['port'], $row['port'], $row['port'], "sep", $row['id']);

						if(@$serverlgsl['b']['status'] == '1')
						{
							$srv = array(
								'name' => @$serverlgsl['s']['name'],
								'map' => @$serverlgsl['s']['map'],
								'players' => @$serverlgsl['s']['players'].'/'.@$serverlgsl['s']['playersmax'],
								'ipaddress' => $row['ip'].":".$row['port'],
								'rank' => $row['rank'],
							);
						}
						else
						{
							$srv = array(
								'name' => $row['name'],
								'map' => 'offline',
								'players' => '<font color="red">Offline</font>',
								'ipaddress' => $row['ip'].":".$row['port'],
								'rank' => $row['rank'],
							);
						}

?>
					<li>
						<div id="mapimg">
							<img width="90" height="70" src="assets/img/<?php echo $mapc; ?>/<?php echo "offline"; //echo $srv['map']; ?>.jpg" />
						</div>
						<div id="infos">
							<a href="server.php?id=<?php echo $row['id']; ?>"><?php echo $srv['name']; ?></a><br />
							<?php echo $srv['ipaddress']; ?><br />						
							Players: <z><?php echo $srv['players']; ?></z><br />
							Rank: <z>#<?php echo $srv['rank']; ?></z>
						</div>
					</li>
<?php
					}
?>
				</ul>
			</div>

			<div id="sep"></div> <!-- #sep end -->

			<a href="#" onclick="skidanjecs()"><div id="promocija">
<div id="img">
			</div>
			</div></a>
 <!-- #promocija end -->
			<div class="narucii">
				<div id="indexnbox" class="one">
					<div id="hdr">
						<p><i class="icon-double-angle-right"></i> Counter-Strike 1.6</p>
					</div>
					<div id="cs16"></div>
					<div class="span"></div>
					<div id="info">
						Lite: <cr>FR-OVH SAS</cr> | Ping: <cr>20-60 ms</cr>
					</div>
					<div id="info">
						Premium: <cr>//</cr> | Ping: <cr>//</cr>
					</div>	
					<div id="button">
						<a href="naruci.php">
							<div id="buttonh">
								<i class="icon-shopping-cart"></i> <?php echo $jezik['text33']; ?>
							</div>
						</a>
						<p><?php echo $jezik['text34']; ?></p>
						<p style="margin-top: -15px;">LITE <z>0.7KM</z> Slot</p>
					</div>
				</div> 
				<div id="indexnbox" class="two">
					<div id="hdr">
						<p><i class="icon-double-angle-right"></i> Call of Duty 4 MW3</p>
					</div>
					<div id="cod"></div>
					<div class="span"></div>
					<div id="info">
						Lite: <cr>//</cr> | Ping: <cr>//</cr>
					</div>
					<div id="info">
						Premium: <cr>//</cr> | Ping: <cr>//</cr>
					</div>	
					<div id="button">
						<a href="javascript:;">
							<div id="buttonh" class="cod4mw3">
								<i class="icon-shopping-cart"></i> <?php echo $jezik['text35']; ?>...
							</div>
						</a>
						<p><?php echo $jezik['text34']; ?></p>
						<p style="margin-top: -15px;">LITE <z>//</z> / </p>
					</div>				
				</div>
				<div id="indexnbox" class="three">
					<div id="hdr">
						<p><i class="icon-double-angle-right"></i> Minecraft</p>
					</div>
					<div id="mc"></div>
					<div class="span"></div>
					<div id="info">
						Lite: <cr>FR-OVH SAS</cr> | Ping: <cr>20-60 ms</cr>
					</div>
					<div id="info">
						DDOS: <cr>Full zastita od ddos-a</cr> | Ping: <cr>//</cr>
					</div>	
					<div id="button">
						<a href="naruci.php">
							<div id="buttonh" class="mc">
								<i class="icon-shopping-cart"></i> <?php echo $jezik['text33']; ?>
							</div>
						</a>
						<p><?php echo $jezik['text34']; ?></p>
						<p style="margin-top: -15px;">LITE <z>4€</z> 1 gb mem </p>
					</div>				
				</div>
				<div id="indexnbox" class="four">
					<div id="hdr">
						<p><i class="icon-double-angle-right"></i> SAMP</p>
					</div>
					<div id="samp"></div>
					<div class="span"></div>
					<div id="info">
						Lite: <cr>FR-OVH SAS</cr> | Ping: <cr>20-60 ms</cr>
					</div>
					<div id="info">
						Premium: <cr>//</cr> | Ping: <cr>//</cr>
					</div>	
					<div id="button">
						<a href="naruci.php">
							<div id="buttonh" class="gta">
								<i class="icon-shopping-cart"></i> <?php echo $jezik['text33']; ?>
							</div>
						</a>
						<p><?php echo $jezik['text34']; ?></p>
						<p style="margin-top: -15px;">LITE <z>0.1KM</z> slot </p>
					</div>				
				</div>
			</div> <!-- .narucii end -->
			
			<div id="statistika">
				<div id="l">
					<div class="resize"></div>
					<div id="promo">
					<a href="naruci.php"><div id="hov"></div></a>
					</div>
					<div id="polja">
						<table width="90%">
							<tr>
								<th width="65px;"></th>
								<th style="max-width: 410px;"></th>
							</tr>
							<tr>
								<td><div id="poljaimg" class="novac"></div></td>
								<td>
								<h4><?php echo $jezik['text36']; ?></h4>
								<p>
									<?php echo $jezik['text37']; ?>
								</p>
							</td>
							</tr>
							<tr>
								<td><div id="poljaimg" class="free"></div></td>
								<td>
								<h4 style="margin-top: -8px;"><?php echo $jezik['text38']; ?></h4>
								<p id="test">
									<?php echo $jezik['text39']; ?>
								</p>
								</td>
							</tr>
							<tr>
								<td><div id="poljaimg" class="korpa"></div></td>
								<td>
								<h4 style="margin-top: -10px;"><?php echo $jezik['text40']; ?></h4>
								<p>
									<?php echo $jezik['text41']; ?>
								</p>
								</td>
							</tr>
							<tr>
								<td><div id="poljaimg" class="cetri"></div></td>
								<td>
								<h4 style="margin-top: -12px;"><?php echo $jezik['text42']; ?></h4>
								<p id="test">
									<?php echo $jezik['text43']; ?>
								</p>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div id="r">
				<div id="krug">
					<div id="krugbroj" class="serveri">
						<?php echo $jezik['text44']; ?>
					</div>
					<div id="crta">
						<p id="hostovano"></p>
					</div>
					<div id="krugbroj" class="masine">
						<?php echo $jezik['text45']; ?>
					</div>
					<div id="crta2">
						<p id="dostavljeno"></p>
					</div>
					<div id="krugbroj" class="klijenti">
						<?php echo $jezik['text46']; ?>
					</div>
				</div>
				</div>
			</div>
<?php
include("./assets/footer.php");
?>
