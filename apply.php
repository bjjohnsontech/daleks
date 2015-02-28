<?php
//Database connection credentials

$host = "fbt-db01.swe.reyrey.net"; //development
$user = "scoreKeeper";
$pass = "cubsWin"; //cubsWin
$db = "scores";

$connection = pg_connect ("host=$host dbname=$db user=$user password=$pass");
//Database queries
//pg_query("BEGIN");
$query2 = "INSERT INTO daleks VALUES('{$_POST['initials']}',{$_POST['score']})";
$result = pg_query($query2) ;

// Commit Database changes
pg_query("COMMIT");
echo 'Success';
?>