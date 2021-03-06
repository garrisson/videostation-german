﻿<?php
error_reporting(E_ALL);
$time_start = microtime(true);
session_start();
require('lib/config.php');
require('lib/API-allocine.php');
require('lib/functions.php');
require('lib/lang.php');
login_check($LOGIN,$PORT_SYNO,$SECURE);
if($INSTALL){
if($_GET['action'] == 'login') echo '<script>document.location.href="index.php"</script>';
die (include('INSTALL.php'));
 }
$root = admin($root);
if($LOGIN){ if(empty($_SESSION['user'])) die (include('login.php'));}
$dir = rep(urldecode($_GET['rep']));
$tri = tri($_GET['tri']);
connect($PASSWORD_SQL,$DATABASE);
if(is_serie($SERIES_DIR)) $db = 'series';
else $db = 'movies';
$folders = folders($dir,$HIDDEN_FILES);
if (isset($_GET['recherche'])){
	$string = explode(' ',$_GET['recherche']);
	for($i=0;$i<count($string);$i++){
		if ($i == 0) $desc = "name LIKE '%".$string[$i]."%'"; 
		else $desc .= " OR name LIKE '%".$string[$i]."%'";
	}
	$sql = "SELECT * FROM $db WHERE ".$desc." ORDER BY ".$tri;
	$folders = null;
}
elseif (isset($_GET['genre'])){
	$sql = "SELECT DISTINCT id_movie, name, note, link, dir, year FROM movies, movie_genre WHERE fk_id_genre = '".$_GET['genre']."' and id_movie = fk_id_movie ORDER BY ".$tri;
	$folders = null;
}
else $sql = "SELECT * FROM $db WHERE dir='".$dir."' ORDER BY ".$tri;
$req = mysql_query($sql) or die ('Erreur SQL : '.mysql_error());
$nb_entree_bdd = mysql_num_rows($req); 
?>
<?php //echo round((microtime(true)-$time_start),3);?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $APP_NAME;?></title>
<link rel="stylesheet" href="css/default.css">
<link rel="stylesheet" href="css/nyroModal.css">
<link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.17.custom.css" />

<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/jquery-ui.min.js"></script>-->
<!--<script type="text/javascript" src="http://code.jquery.com/ui/jquery-ui-git.js"></script>-->
<script type="text/javascript" src="/javascript/jquery-ui-1.8.14.custom.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.8.21/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.nyroModal.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.tools.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.popup.js"></script>
</head>
<body>
<div id="indexing"></div>
<!-- HEADER -->
<header>

	<div class="header_left logo"><img src="images/logo.png"></div>


	
	<div id="empty" class="header_left" style="margin-left:30px;padding-top:3px;">
	</div>
	
	<div class="header_right demo" style="margin-right:8px;padding-top:1px;">
	<a href="#param"><button id="parameters" value="Infos">Infos</button></a>
	<div class="ui-widget-content" id="param" aria-label="Login options" style="float:right;">
		<?php if(isset($_SESSION['user'])) echo '['.$_SESSION['user'].'] | <a href="index.php?action=logout">Logout</a>';
		else echo '| <a href="login.php">Login</a>';
		if ($root) echo ' | <a href="admin.php">Administration</a>';
		echo ' |';
		$stats = countVideos();?>
		<hr>
		<table border="0">
		<tr><td>Filme :</td><td><?php echo $stats['movies'];?></td></tr>
		<tr><td>Serien :</td><td><?php echo $stats['series'];?></td></tr>
		<tr><td>Falsch indexiert :</td><td><?php echo $stats['wrong'];?></td></tr>
		<tr><td>Fehler :</td><td><?php echo $stats['errors'];?></td></tr>
		</table>
		<hr>
	</div>
	</div>
	
	
	<div class="header_right" style="margin-right:5px;padding-top:1px;">
		<form method="GET" action="index.php" >
			<input type="text" name="recherche">
			<button value="Rechercher" id="search" onclick="this.form.submit()">Suchen</button>
		</form>
	</div>
			
	<div class="header_right">
		<form method="GET" action="<?php echo $_SERVER['REQUEST_URI'];?>">
			<select name="tri" onChange="this.form.submit()" style="margin-right:2px;margin-top:3px;">
				<option><?php echo sortby;?></option>
				<option value="name"><?php echo name;?></option>
				<option value="note DESC"><?php echo note;?></option>
				<option value="year"><?php echo year;?></option>
			</select>
			<input type="hidden" name="<?php 
			if(isset($_GET['rep'])) echo 'rep'; 
			elseif(isset($_GET['recherche'])) echo research;
			else echo genre;?>" value="<?php 
			if (isset($_GET['rep'])) echo $_GET['rep']; 
			elseif (isset($_GET['recherche'])) echo $_GET['recherche'];
			else echo $_GET['genre'];?>">
		</form>
	</div>
			
	<div class="header_right">
		<?php
		$sql_genres = "SELECT * FROM genres ORDER BY name";
		$req_genres = mysql_query($sql_genres) or die ('Erreur SQL '.mysql_error());
		echo '<form method="GET" action="index.php"><select onChange="this.form.submit()" name="genre" style="margin-right:2px;margin-top:3px;"><option>--'.display.'--</option>';
		while($data_genres = mysql_fetch_array($req_genres)){
			echo '<option value="'.$data_genres['id_genre'].'">'.$data_genres['name'].'</option>';
		}
		echo '</select></form>';
		?>
	</div>
		
			
			
			
</header>
<!-- /HEADER -->

<!-- NAVIGATION -->
<nav class="margin">
<?php
if(is_serie($SERIES_DIR)){
$src = banner_serie();
if(!empty($src)) $style='style="background-image:url('.$src.');" class="banner"';
}
?>
<div <?php echo $style;?>><?php 
if(isset($_GET['recherche'])) echo '<a href="index.php"><img src="images/home.png" alt="home"></a> <a href="index.php">'.home.'</a> / '.research.' ['.$_GET['recherche'].']';
elseif (isset($_GET['genre'])){
$sql_search_genre = "SELECT name FROM genres WHERE id_genre=".$_GET['genre'];
$req_search_genre = mysql_query($sql_search_genre) or die ('Erreur SQL :'.mysql_error());
$name_genre = mysql_fetch_array($req_search_genre);
echo '<a href="index.php"><img src="images/home.png" alt="home"></a> <a href="index.php">'.home.'</a> / Genre ['.$name_genre['name'].']';
}
else repertoire($dir);?></div>
</nav>
<!-- /NAVIGATION -->

<!-- CONTENU -->
<div id="content">
<?php
if (count($folders)!=0 and !isset($_GET['recherche']) and !isset($_GET['genre'])){
	echo '<hr>';
	foreach ($folders as $folder){
		echo '<a href="?rep='.urlencode($dir.'/').urlencode($folder).'" class="movielist"><p class="folder"><img src="images/folder.png" alt="folder"> <span>'.$folder.'</span></p></a>';
	}
	echo '<hr>';
}
?>
<ul class="movielist">
<?php
$i=1;
while ($data = mysql_fetch_array($req)){
	echo '<li id="'.$i.'">';
	//echo '<a href="'.$dir.'/'.$data['link'].'">'.lenght($data['name'],18).'</a><br>';
	if($root) echo keywordsAdapt($data['link'],$DELETED_WORDS,1).'<br>';
	if(is_serie($SERIES_DIR)){
		$affiche = explode('-',$data['id_serie']);
		$affiche = 's-'.$affiche[0];
	}
	else $affiche = $data['id_movie'];
	if (is_file('images/poster_small/'.$affiche.'.jpg')){
		echo '<a href="#null" rel="'.urlencode($data['link']).'"';
		if ($MODAL) echo 'class="opener movielist"';
		echo '><img src="images/poster_small/'.$affiche.'.jpg" alt="'.$data['name'].'" class="poster"></a>';
	}
	else { 
	if($data['id_movie'] != '0' and $data['id_movie'] != '0-0-0') echo '<a href="#null" rel="'.urlencode($data['link']).'" class="opener movielist">';
	echo '<img src="images/movie.png" style="margin-top:20%;" alt="Film" class="poster">';
	if($data['id_movie'] != '0' and $data['id_movie'] != '0-0-0') echo '</a>';
	}
	//DIV TOOLTIP
	echo '<div class="tooltip">
	<table border="0"';
	if(!$root) echo 'style="margin-left:20px;"';
	echo '><tr>';
	if($data['id_movie'] != '0' and $data['id_movie'] != '0-0-0') echo '<td><a href="#null" rel="'.urlencode($data['link']).'" class="opener movielist"><img src="images/info.png" alt="Info"></a></td>';
	echo '<td><a href="';
	if($FTP) echo 'ftp://'.$_SERVER['SERVER_NAME'].'/'.$data['dir'].'/'.$data['link'];
	else echo $data['dir'].'/'.$data['link'];
	echo '" class="movielist" title="'.$data['link'].'"><img src="images/down.png"></a></td>';
	if($root and !is_serie($SERIES_DIR)) echo '<td><a href="update.php?link='.urlencode($data['link']).'&oldcode='.$data['id_movie'].'" class="nyroModal"><img src="images/update.png"></a></td>';
	echo '<td><a href="';
	//if($FTP) echo 'ftp://'.$_SERVER['SERVER_NAME'].'/'.$data['dir'].'/'.$data['link'];
	echo 'stream.php?movie='.$data['dir'].'/'.$data['link'];
	echo '" class="nyroModal" title="'.$data['link'].'"><img src="images/play.png"></a></td>';
	echo '</tr>
	<tr>';
	if($data['id_movie'] != '0' and $data['id_movie'] != '0-0-0') echo '<td>'.infos.'</td>';
	echo '<td>'.link.'</td>';
	if($root and !is_serie($SERIES_DIR)) echo '<td>'.update.'</td>';
	echo '<td>'.stream.'</td>';
	echo '</tr>
	</table>
	</div>';
	// /DIV TOOLTIP
	echo '<div class="title"><h5><a href="';
	if($FTP) echo 'ftp://'.$_SERVER['SERVER_NAME'].'/'.$data['dir'].'/'.$data['link'];
	else echo $data['dir'].'/'.$data['link'];
	echo '" class="movielist" title="'.$data['link'].'">'.length($data['name'],22).'</a></h5><p>'.$data['year'].'</p></div>';
	echo '<div class="stars">'.stars($data['note']).'</div>';
	echo '</li>';
	$i++;
}
?>
</ul>
<div class="resume">
<?php echo count($folders).' '.folders.' - '.$nb_entree_bdd.' '.files;?>
</div>
</div>
<!-- /CONTENU -->
<div id="push"></div>
<!-- FOOTER -->
<footer>
<div class="license">
<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/"><img alt="Licence Creative Commons" style="border-width:0" src="images/cc88x31.png" /></a><br /><span style="display:none;" class="licensetext">Diese Anwendung untersteht den Regeln der <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/">Licence CC BY-NC-ND 3.0</a>.</span></div>
<div class="generation"><?php echo pagegeneration.' '.round((microtime(true)-$time_start),3);?> s.<br><br>Version :<?php echo $VERSION;?></div>
<div class="hosted">Last version available on <br><a href="https://github.com/teebo/VideoStation" target="_blank"><img src="images/github.png" style="height:30px;"></a></div>
<div style="clear:both;"></div>
</footer>
<!-- /FOOTER -->
<script type="text/javascript">
$(document).ready(function(){
	$('button').button();
	$('#loading').hide().ajaxStart(function() {
    $(this).show();
    }).ajaxStop(function() {
        $(this).hide();
    });
	$('.nyroModal').nyroModal();
	$.nmObj({sizes: { minW: 300, minH: 400 }});
	/***
	HEADER
	***/
	$('#search').button({
	icons: {
                primary: "ui-icon-search"
            },
            text:false
            });
    $('#parameters').button({
	icons: {
                primary: "ui-icon-info",
                secondary: "ui-icon-triangle-1-s"
            },
            text:false
            });   
	//$('#param').popup();
	$('#param').hide();
	$('#parameters').click(function(){
	if($('#param').is(':hidden')){
	$('#param').slideDown();
	}
	else {
	$('#param').slideUp();
	}
	});
     
            
	/***
	CONTENT
	***/
	$('.poster').tooltip({ 
		effect: 'slide', 
		predelay:1100, 
		delay:600,
		opacity:1,
		offset:[15, 0]
		});

	$('#content ul li img.poster').hover(function(){
	$(this).addClass('gallerie_onMouse');
	},
	function(){
	$(this).removeClass('gallerie_onMouse');
	});	
	
	$('#content p.folder').hover(function(){
	$(this).addClass('folderHover');
	},
	function(){
	$(this).removeClass('folderHover');
	});
	
	$('a.opener').click(function(){
			
			var link = $(this).attr('rel');
			var screenheight = (screen.height-200);
			$.ajax({
  				type: "GET",
  				url: "video.php",
   				data: "rep=<?php echo $_GET['rep'];?>&link="+link,
   				error:function(msg){
     				alert( "Error !: " + msg );
   				},
   				success:function(data){
   					//affiche le contenu du fichier dans le conteneur d&eacute;di&eacute;
					$('<div id="dialog"></div>').html(data).dialog({
					title: '<?php echo details;?>',
					modal:true,
					maxHeight: screenheight,
					width : 940,
					draggable:true,
					resizable:false
					});
				}
			});
			
			});
			    
    /**
    FOOTER
    **/
    $('footer div.license').hover(function(){
    $('footer div.license span.licensetext').fadeIn();
    },function(){
    $('footer div.license span.licensetext').delay(1200).fadeOut();
    });
    $('footer').hide();
  	window.onload=function(){  
    var w = $(window).height();
    var h = ($('header').height()+22);
    var c = ($('#content').height()+$('nav').height()+20);
    var foo = ($('footer').height()+2);
    if((h+c+foo+10)<w){
    $('footer').attr('style','margin-top:'+(w-(h+c+foo))+'px;').show();
    }
    else $('footer').show();
     };
});
</script>
<?php if($INDEXATION_AUTO) index_auto($dir,$HIDDEN_FILES,$EXT,$SERIES_DIR);?>

<div id="loading"><img src="images/ajax-loader.gif" style="margin-top:20%;"><br><br><?php echo loading;?> ...</div>

</body>
</html>