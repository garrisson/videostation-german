<?php
// Stream Script für Windows Media Player. => Erfordert Win. M. Player Plugin.
//error_reporting(E_ALL);
if (isset($_GET['movie']))
{
$movie = $_GET['movie'];
echo("
<object id=\"MediaPlayer1\" CLASSID=\"CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95\" codebase=\"http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701\"
standby=\"Loading Microsoft Windows® Media Player components...\" type=\"application/x-oleobject\" width=\"600\" height=\"400\">
<param name=\"fileName\" value=\"$movie\">
<param name=\"animationatStart\" value=\"true\">
<param name=\"transparentatStart\" value=\"true\">
<param name=\"autoStart\" value=\"true\">
<param name=\"showControls\" value=\"true\">
<param name=\"Volume\" value=\"-450\">
<embed type=\"application/x-mplayer2\" pluginspage=\"http://www.microsoft.com/Windows/MediaPlayer/\" src=\"$movie\" name=\"MediaPlayer1\" width=600 height=400 autostart=1 showcontrols=1 volume=-450>
</object>
");

}
?> 