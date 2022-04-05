<?php
$f = file_get_contents('logs/logs.json');
$f = json_decode($f, true);

echo "<body bgcolor=#cacaca><table width=80% border=0 align='center'><tr>
<td>jika logs dibersihkan maka <b>admin</b> dan <b>sender</b> (yang sebelumnya ngirim menfess) gak akan bisa unsend menfessnya, <br>karena logsnya hanya disimpan dalam bentuk json, gak pake database<br>
sebaiknya lakukan clear logs jika urgensi saja</td>
<td><br><form method=post><center><input type=submit name=clear class=log value='clear all logs'><br><br>coded by @senggolbaok</center></form></td>
</tr></table>";
echo "<table width=80% border=1 align='center'><tr><td align=center><b>method</b></td><td align=center><b>tweet id</b></td><td align=center><b>sender id</b></td><td align=center><b>username</b></td></tr>";

foreach ($f as $data) {

    echo "<tr><td align=center>";
    if ($data['method'] == 'unsend tweet') {
        echo "<font color=red>" . $data['method'] . "</font>";
    } elseif ($data['method'] == 'deleted by admin') {
        echo "<font color=red>" . $data['method'] . "</font>";
    } else {
        echo $data['method'];
    }
    echo "</td><td><a href='" . $data['twId'] . "' target='_blank'>" . $data['twId'] . "</a></td><td>" . $data['senderId'] . "</td><td> @" . $data['senderName'] . "</td></tr>
	";
}
echo "</table>";

if (isset($_POST['clear'])) {
    clearLog();
    header('Location: shadowLog.php');
}

function clearLog()
{
    $file = fopen('logs/logs.json', 'w');
    fwrite($file, '[]');
    fclose($file);
}
