<?php
$servername='mysql';//server name
$username='student';//username
$password='student';//password
$databasename='ijdb';//databasename
//connected php to the database
$Connection = new PDO('mysql:dbname=' . $databasename . ';host=' .$servername,$username,$password);
?>