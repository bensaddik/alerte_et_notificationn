<?php
$DBServer = 'localhost';
$DBUser   = 'root';
$DBPass   = 'Amed05';
$DBName   = 'alerte_et_notification';

$db = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
$db->set_charset("utf8");
if ($db->connect_error) {
  trigger_error('Database connection failed: '  . $db->connect_error, E_USER_ERROR);
}
?>