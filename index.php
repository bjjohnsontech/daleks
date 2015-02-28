<!--?php
//Database connection credentials

$host = "fbt-db01.swe.reyrey.net"; //development
$user = "scoreKeeper";
$pass = "cubsWin"; //cubsWin
$db = "scores";

$connection = pg_connect ("host=$host dbname=$db user=$user password=$pass");
//Database queries
//pg_query("BEGIN");
$query2 = "SELECT initials, score FROM daleks ORDER BY score DESC LIMIT(10)";
$result = pg_query($query2) ;
$scores = array();
for ($rows = 0; $rows < pg_numrows($result); $rows++)
{
    $scores[] = array(
        $rows+1 . '.  ' . pg_result($result, $rows, 0),
        pg_result($result, $rows, 1)
        );
}

if (!$result) {
    $errormessage = pg_last_error();
    syslog(LOG_WARNING, "($IP | $user) $errormessage");
    echo "Error with query: " . $errormessage;
    exit();
}

// Commit Database changes
pg_query("COMMIT");

?-->
<html>
<head>
<style>
body
{
    background: black;
    color: whitesmoke;
}

#body_div
{
    width: 800px;
    margin: 100px auto 0;
}

.menu
{
    display: inline;
    float: left;
    position: relative;
}

#start
{
    width: 300px;
}

#scores
{
    width: 300px;
}

#scores ul
{
    list-style: none outside none;
    margin: 0 0 0 -25px;
}

.initials
{
    width: 110px;
    font-size: 30px;
    display: inline;
    float: left;
}

.points
{
    width: 160px;
    text-align: right;
    font-size: 30px;
}

#logo
{
    font-family: Trebuchet MS;
    font-size: 1px;
    line-height: 1.25px;
}

#menu
{
    margin: 150px 0 0 50px;
}

.button
{
    background: none;
    width: 200px;
    font-size: 25px;
    border: none;
    color: whitesmoke;
    margin: 0 0 30px 0;
}

.selected
{
    background: whitesmoke;
    color: black;
}
</style>
<title>Not a waste of Time</title>
</head>
<body onkeydown='pressed(event)'>
  <div id="body_div">
    <div class='menu'>
        <h1>DALEKS!!!</h1>
        <div id='logo'><pre>
            <?php include 'dLogo.php'; ?></pre>
        </div>
    </div>
    <div id="start" class='menu'>
        <div id='menu'>
            <input type='button' id='go' class='button selected' value='START' onclick='startGame()'/>
            <input type='button' id='history' class='button' value='High Scores' onclick='window.location = "setScore.php"'/>
        </div>
    <!--start--></div>
    <div id="scores" class='menu'>
    <div style="width:200px; margin:-25px auto 0"><h1>High Scores!!!</h1></div>
    <ul>
    <?php foreach ($scores as $score): ?>
        <li>
            <div class="initials">
                <?php echo $score[0]; ?>
            </div>
                <div class='points menu'>
                    <?php echo $score[1]; ?>
                </div>
        </li>
    <?php endforeach;?>
    </ul>
    <!--scores--></div>
  <!--body_div--></div>
  <div style='visibility:hidden'>
    <form id='dalForm' action='daleks.php' method="post">
        <input type='numeric' value=10 name='daleks'/>
        <input type='numeric' value=0 name='sendScore'/>
        <input type='numeric' value=0 name='obstacles'/>
    </form>
  </div>
</body>
<script>
if (!Array.prototype.indexOf) 
{
    Array.prototype.indexOf = function(obj, start) {
        for (var i = (start || 0), j = this.length; i < j; i++) 
        {
            if (this[i] === obj) { return i; }
        }
     return -1;
    }
}

function pressed(event)
{
    var key = event.which;
    //console.log(key);
    if (new Array(38,40,104,98).indexOf(key) != -1)
    {
        var go = document.getElementById('go');
        var history = document.getElementById('history');
        if (go.className == 'button selected')
        {
            go.className = 'button';
            history.className = 'button selected';
        }
        else
        {
            go.className = 'button selected';
            history.className = 'button';
        }
    }
    else if(key == 13)
    {
        var go = document.getElementById('go');
        if (go.className == 'button selected')
        {
            startGame();
        }
        else
        {
            window.location = 'setScore.php';
        }
    }
}

function startGame()
{
    document.forms['dalForm'].submit();
}

</script>
</html>
