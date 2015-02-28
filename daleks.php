<html>
<head>
<style>
tr
{
    height:30px;
}

td
{
    width: 25px;
    text-align: center;
    border: 1px solid #222222;
}

.top
{
    border-top: 1px solid white;
}

.bottom
{
    border-bottom: 1px solid white;
}

.left
{
    border-left: 1px solid white;
}

.right
{
    border-right: 1px solid white;
}

.theDoctor
{
    background-image: url('Tennant.jpg');
    background-size: 15px 30px;
    background-repeat: no-repeat;
    background-position: center;
}

.dalek
{
    background-image: url('dalekBl.JPG');
    background-size: 25px 30px;
    background-repeat: no-repeat;
}

table
{
    table-layout: fixed;
    width: 135px;
    height: 135px;
    margin: 30 auto;
    border-collapse: collapse;
}

.score
{
    float:right;
    font-size: 40px;
    margin: -50px 0 0;
    width: 200px;
}

</style>
</head>
<body style="background-color:black;color:whitesmoke" onkeydown="takeTurn(event)">
<table>
<?php $row = 19; $col = 0; $debug = false;?>
<?php while ($row >=0):?>
<tr>
    <?php while ($col < 30):?>
<td id='<?php echo "{$row}:{$col}";?>' class="<?php if($row == 19){echo 'top ';}elseif($row == 0){echo 'bottom ';}if($col == 0){echo 'left';}elseif($col == 29){echo 'right';}$col++;?>"> &nbsp; </td>
    <?php endwhile;?>
</tr>
<?php $row --; $col = 0; ?>
<?php endwhile;?>
</table>
<div style='width:800px;margin: -20 auto;'>
    <div style="display:inline;">
Using the number pad, move The Doctor.<br/>
If you're stuck use 't' to teleport randomly, <br/>
or ~ to teleport to a "safe" place... but it will cost you.<br/>
The goal is to destroy all the daleks by runnning them into each other or the obstacles.
    </div>
    <div class="score">
    <div>Score:</div>
    <div id="score"><?php echo $_POST['sendScore']; ?></div>
    </div>
</div>
<div style='display:none'>
<form id='nextLevel' action='daleks.php' method='post'>
<input type='numeric' name='daleks' value='<?php echo $_POST['daleks'] + 5;?>'/>
<input type='numeric' id='sendScore' name='sendScore' value='<?php echo $_POST['sendScore'];?>'/>
<input type='numeric' name='obstacles' value='<?php echo $_POST['obstacles']/10;?>'/>
</form>
</div>
</body>
<?php

$obstacles = $_POST['obstacles'];
$daleks = $_POST['daleks'];
$coords = array();
$i = 0;
while ($i <= $obstacles + $daleks)
{
    $go = false;
    while(!$go)
    {
        $tmp = array(rand(1,18),rand(1,28));
        if(!in_array($tmp, $coords))
        {
            $coords[] = $tmp;
            $go = true;
            $i++;
        }
    }
}

$doctor = $coords[0];
$daleks = array_slice($coords,1,$daleks);
$obstacles = array_slice($coords,count($daleks)+1);
?>
<script type="text/javascript">
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

var doctor = <?php print_r(json_encode($doctor)); ?>;
var start = document.getElementById(doctor.join(':'));
start.className += ' theDoctor ';
var dalekLocations = <?php print_r(json_encode($daleks)); ?>;
var obstacles = <?php print_r(json_encode($obstacles));?>;
console.log(dalekLocations.length);
for(var i=0, j=dalekLocations.length; i<j; i++)
{
    var start = document.getElementById(dalekLocations[i].join(':'));
    start.className += ' dalek ';
}

for(var i=0, j=obstacles.length; i<j; i++)
{
    var start = document.getElementById(obstacles[i].join(':'));
            start.innerHTML = '&';
    var above = (obstacles[i][0]-1) + ':' + (obstacles[i][1]);
        document.getElementById(above).className += 'o_bottom';
    var left = (obstacles[i][0]) + ':' + (obstacles[i][1]-1);
        document.getElementById(left).className += ' o_right';
    var below = (obstacles[i][0]+1) + ':' + (obstacles[i][1]);
        document.getElementById(below).className += ' o_top';
    var right = (obstacles[i][0]) + ':' + (obstacles[i][1]+1);
        document.getElementById(right).className += ' o_left';
}

function restore()
{
    for(var i=0, j=obstacles.length; i<j; i++)
    {
        var start = document.getElementById(obstacles[i].join(':'));
            start.innerHTML = '&';
    }
    for(var i=0, j=dalekLocations.length; i<j; i++)
    {
        var start = document.getElementById(dalekLocations[i].join(':'));
            start.className += ' dalek ';
    }
}

function teleport(safe)
{
    if (!safe)
    {
        while(!safe)
        {
            var go = [Math.floor(Math.random()*19),Math.floor(Math.random()*29)];
            safe=true;
            for(var i=0, j=obstacles.length; i<j; i++)
            {
                if(Array(obstacles[i][0], obstacles[i][0]-1, obstacles[i][0]+1).indexOf(go[0]) != -1 
                && 
                   Array(obstacles[i][1], obstacles[i][1]-1, obstacles[i][1]+1).indexOf(go[1]) != -1)
                {
                    safe = false;
                    break;
                }
            }
        }
        return go;
    }
    if (parseInt(document.getElementById('sendScore').value) < 1000)
        return false;
    document.getElementById('score').innerHTML = Math.floor(parseInt(document.getElementById('score').innerHTML)*.5);
    document.getElementById('sendScore').value = Math.floor(parseInt(document.getElementById('sendScore').value)*.5);
    while(safe)
    {
        var go = [Math.floor(Math.random()*19),Math.floor(Math.random()*29)];
        safe=false;
        for(var i=0, j=dalekLocations.length; i<j; i++)
        {
            if(Array(dalekLocations[i][0], dalekLocations[i][0]-1, dalekLocations[i][0]+1).indexOf(go[0]) != -1 
            && 
               Array(dalekLocations[i][1], dalekLocations[i][1]-1, dalekLocations[i][1]+1).indexOf(go[1]) != -1)
            {
                safe = true;
                break;
            }
            
        }
        if(safe == false)
        {
            for(var i=0, j=obstacles.length; i<j; i++)
            {
                if(Array(obstacles[i][0], obstacles[i][0]-1, obstacles[i][0]+1).indexOf(go[0]) != -1 
                && 
                   Array(obstacles[i][1], obstacles[i][1]-1, obstacles[i][1]+1).indexOf(go[1]) != -1)
                {
                    safe = true;
                    break;
                }
                
            }
        }
    }
    return go;
}


function moveGuy(key)
{
    var current = document.getElementById(doctor.join(':'));
    var newDoc = doctor;
    switch (key)
    {
        case 84:
            newDoc = teleport(false);
            break;
        case 192:
            newDoc = teleport(true);
            break;
        case 97:
            newDoc = [(doctor[0]-1),(doctor[1]-1)];
            break;
        case 98:
            newDoc = [(doctor[0]-1),(doctor[1])];
            break;
        case 99:
            newDoc = [(doctor[0]-1),(doctor[1]+1)];
            break;
        case 100:
            newDoc = [doctor[0],doctor[1]-1];
            break;
        case 101:
            newDoc = [doctor[0],doctor[1]];
            break;
        case 102:
            newDoc = [doctor[0],doctor[1]+1];
            break;
        case 103:
            newDoc = [doctor[0]+1,doctor[1]-1];
            break;
        case 104:
            newDoc = [doctor[0]+1,doctor[1]];
            break;
        case 105:
            newDoc = [doctor[0]+1,doctor[1]+1];
            break;
        // doubles
        case 46:
            newDoc = [(doctor[0]-2),(doctor[1]-2)];
            break;
        case 40:
            newDoc = [(doctor[0]-2),(doctor[1])];
            break;
        case 34:
            newDoc = [(doctor[0]-2),(doctor[1]+2)];
            break;
        case 37:
            newDoc = [doctor[0],doctor[1]-2];
            break;
        case 39:
            newDoc = [doctor[0],doctor[1]+2];
            break;
        case 45:
            newDoc = [doctor[0]+2,doctor[1]-2];
            break;
        case 38:
            newDoc = [doctor[0]+2,doctor[1]];
            break;
        case 33:
            newDoc = [doctor[0]+2,doctor[1]+2];
            break;
    }
    if (!newDoc)
        return false;
    var location = document.getElementById(newDoc.join(':'));
    if(location
      && location.innerHTML != '&amp;')
    {
        location.className += ' theDoctor ';
        current.className = current.className.replace(' theDoctor ','');
        doctor = newDoc;
    }
    return true;
}

function takeTurn(event)
{
    var key = event.which;
    //console.log(key);
    var acceptable = [84,97,98,99,100,101,102,103,104,105,192,45,38,33,37,39,46,40,34];
    //return false;
    if(acceptable.indexOf(key) != -1)
    {
        go = moveGuy(key);
        if (go)
        {
            moveDaleks();
            //alive();
            collision();
            restore();
            win();
        }
        else
        {
            alert("You can't do that");
        }
    }
}

function win()
{
    if(dalekLocations.length==0)
    {
        document.forms.nextLevel.submit();
    }
}

function moveDaleks()
{
    for( var i=0, j=dalekLocations.length; i<j; i++)
    {
        var current = document.getElementById(dalekLocations[i].join(':'));
        for (var k=0; k<2; k++)
        {
            if (dalekLocations[i][k] < doctor[k])
            {
                dalekLocations[i][k] += 1;
            }
            else if(dalekLocations[i][k] > doctor[k])
            {
                dalekLocations[i][k] -= 1;
            }
        }
        var newDalek = document.getElementById(dalekLocations[i].join(':'));
        current.className = current.className.replace(' dalek ','');
    }
}

function alive()
{
    for(var i=0, j=dalekLocations.length; i<j; i++)
    {
        if(dalekLocations[i][0] == doctor[0] && dalekLocations[i][1] == doctor[1])
        {
            document.forms.nextLevel.action='setScore.php';
            document.forms.nextLevel.submit();
        }
    }
    return true;
}

function collision()
{
    var destroy = new Array();
    for(var i=0, j=dalekLocations.length; i<j; i++)
    {
        for(var k=0, l=dalekLocations.length; k<l; k++)
        {
            if(i != k)
            {
                if(dalekLocations[i][0] == dalekLocations[k][0]
                  && dalekLocations[i][1] == dalekLocations[k][1])
                {
                    if(destroy.indexOf(i) == -1)
                        destroy.push(i);
                }
            }   
        }
        for(var k=0, l=obstacles.length; k<l; k++)
        {
            //console.log(dalekLocations[i] + '-' + obstacles[k]);
            if(dalekLocations[i][0] == obstacles[k][0]
              && dalekLocations[i][1] == obstacles[k][1])
            {
                if(destroy.indexOf(i) == -1)
                    destroy.push(i);
            }
        }
    }
    if(destroy.length > 0)
    {
        //console.log(destroy);
        for(var i=destroy.length-1; i >= 0; i--)
        {
            document.getElementById(dalekLocations[destroy[i]].join(':')).innerHTML='&';
            obstacles.push(dalekLocations[destroy[i]]);
            document.getElementById('score').innerHTML = parseInt(document.getElementById('score').innerHTML)+500;
            document.getElementById('sendScore').value = parseInt(document.getElementById('sendScore').value)+500;
            dalekLocations.splice(destroy[i],1);
        }
    }
}
</script>
</html>
