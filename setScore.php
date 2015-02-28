<!--?php
//Database connection credentials

$host = "fbt-db01.swe.reyrey.net"; //development
$user = "scoreKeeper";
$pass = "cubsWin"; //cubsWin
$db = "scores";

$connection = pg_connect ("host=$host dbname=$db user=$user password=$pass");
//Database queries
//pg_query("BEGIN");
$query2 = "SELECT initials, score FROM daleks ORDER BY score DESC";
$result = pg_query($query2) ;
$scores = array();
for ($rows = 0; $rows < pg_numrows($result); $rows++)
{
    $scores[] = array(
        pg_result($result, $rows, 0),
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
$player = false;
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
    margin: 100px 50px 0 0
}

#scores
{
    width: 350px;
}

#theScores
{
    height:500px;
    overflow: auto;
}

#theScores ul
{
    list-style: none outside none;
    margin: 0 0 0 -25px;
}

.initials
{
    width: 120px;
    font-size: 30px;
    display: inline;
    float: left;
}

.points
{
    width: 160px;
    text-align: right;
    font-size: 30px;
    float: right;
    margin: 0 10px 0 0;
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
    width: 175px;
    font-size: 25px;
    border: none;
    color: whitesmoke;
    margin: 0 0 0px 0;
}

.wheel
{
    font-size: 100px;
    width: 75px;
    text-align: center;
    border: solid 2px whitesmoke;
    border-top-left-radius: 10% 50%;
    border-top-right-radius: 10% 50%;
    border-bottom-left-radius: 10% 50%;
    border-bottom-right-radius: 10% 50%;
}

.selected
{
    background: whitesmoke;
    color: black;
}
</style>
<title>Scores</title>
</head>
<body onkeydown='pressed(event)'>
  <div id="body_div">
    <div class='menu' id='start'>
        <?php if(isset($_POST['sendScore'])):?>
        <div style='width:265;margin:0 auto;'>
        <div name='selectable' id='I1' class='menu wheel selected'>A</div>
        <div name='selectable' id='I2' class='menu wheel'>A</div>
        <div name='selectable' id='I3' class='menu wheel'>A</div>
        </div>
        <?php endif; ?>
        <div id='menu'>
            <?php if(isset($_POST['sendScore'])):?>
            <input type='button' name='selectable' id='history' class='button' value='Submit' onclick='applyScore()'/>
            <input type='button' name='selectable' id='home' class='button' value='To Main Menu' onclick='window.location = "index.php"'/>
            <?php else:?>
            <input type='button' name='selectable' id='home' class='button selected' value='To Main Menu' onclick='window.location = "index.php"'/>
            <?php endif; ?>
        </div>
    <!--start--></div>
    <div id="scores" class='menu'>
    <div style="width:200px; margin:-25px auto 0"><h1>High Scores!!!</h1></div>
    <div id='theScores'>
        <ul>
        <?php $i=1; foreach ($scores as $score): ?>
            <?php if(isset($_POST['sendScore']) && !$player && $score[1] < $_POST['sendScore']):?>
                <li>
                    <div class="initials">
                        <?php echo $i . '. '; $i++; ?><span id="playerInit">&nbsp;</span>
                    </div>
                        <div class='points menu'>
                            <?php echo $_POST['sendScore']; ?>
                        </div>
                </li>
                <?php $player = true;?>
            <?php endif; ?>
            <li>
                <div class="initials">
                    <?php echo $i . '. ' . $score[0]; $i++; ?>
                </div>
                    <div class='points menu'>
                        <?php echo $score[1]; ?>
                    </div>
            </li>
        <?php endforeach;?>
        <?php if(isset($_POST['sendScore']) && !$player):?>
            <li>
                <div class="initials">
                    <?php echo $i . '. '; $i++; ?><span id="playerInit">&nbsp;</span>
                </div>
                    <div class='points menu'>
                        <?php echo $_POST['sendScore']; ?>
                    </div>
            </li>
            <?php $player = true;?>
        <?php endif; ?>
        </ul>
    <!--theScores--></div>
    <!--scores--></div>
  <!--body_div--></div>
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

<?php if(isset($_POST['sendScore'])):?>
document.getElementById('playerInit').scrollIntoView(true);

function applyScore()
{
    var initials = document.getElementById('I1').innerHTML;
        initials += document.getElementById('I2').innerHTML;
        initials += document.getElementById('I3').innerHTML;
    var post = 'initials=' + initials;
        post += '&score=<?php echo $_POST['sendScore'];?>';
    var url = 'apply.php';
    var expireCheck = new XMLHttpRequest();
        expireCheck.open("POST", url, true);
        expireCheck.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        expireCheck.onreadystatechange=function() 
        {
            if (expireCheck.readyState==4) 
            {
                var response = expireCheck.responseText;
                if (response == "Success")
                {
                    document.getElementById('playerInit').innerHTML = initials;
                    document.getElementById('history').disabled = 'disabled';
                }
                else
                {
                    alert("Sorry, that didn't work... Please try again");
                }
            }
        }
        expireCheck.send(post);
    
}
<?php endif; ?>
function change(key, toChange)
{
    var chars = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','&nbsp;','0','1','2','3','4','5','6','7','8','9'];
    var current = chars.indexOf(toChange.innerHTML);
    switch (key)
    {
        case 38:
        case 104:
            if(current == chars.length-1)
                toChange.innerHTML=chars[0];
            else
                toChange.innerHTML=chars[current+1];
            break;
        case 40:
        case 98:
            if(current == 0)
                toChange.innerHTML=chars[chars.length-1];
            else
                toChange.innerHTML=chars[current-1];
            break;
    }
    return;
}

function pressed(event)
{
    var key = event.which;
    //console.log(key);
    //return;
    if (new Array(38,40,104,98).indexOf(key) != -1)
    {
        var elms = document.getElementsByName('selectable');
        for (var i=0, j=elms.length; i<j; i++)
        {
            if (elms[i].className == 'menu wheel selected')
            {
                change(key, elms[i]);
                break;
            }
        }
    }
    else if(new Array(100, 37, 102,39).indexOf(key) !=-1)
    {
        var elms = document.getElementsByName('selectable');
        for (var i=0, j=elms.length; i<j; i++)
        {
            if (elms[i].className.indexOf('selected') != -1)
            {
                switch(key)
                {
                    case 100:
                    case 37:
                        elms[i].className = elms[i].className.replace(' selected', '');
                        if (i == 0)
                        {
                            elms[j-1].className += ' selected';
                        }
                        else
                        {
                            elms[i-1].className += ' selected';
                        }
                        break;
                    case 102:
                    case 39:
                        elms[i].className = elms[i].className.replace(' selected', '');
                        if (i == j-1)
                        {
                            elms[0].className += ' selected';
                        }
                        else
                        {
                            elms[i+1].className += ' selected';
                        }
                        break;
                }
                break;
            }
        }
    }
    else if(key == 13)
    {
        var elms = document.getElementsByName('selectable');
        for (var i=0, j=elms.length; i<j; i++)
        {
            if (elms[i].className == 'button selected')
            {
                elms[i].click();
                break;
            }
        }
    }
}

</script>
</html>
