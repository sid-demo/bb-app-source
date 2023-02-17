<!DOCTYPE html>
<html>
  <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  <head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Titan+One&family=Rubik">
    <meta charset="utf-8" />
    <title>Block Buster - Level 2</title>
  </head>
  <body style="background-color: #FFFFFF">
    <div id="heading">
      <h1 class="stroke-text">BLOCK BUSTER</h1>
    </div> <?php

echo "
			<table class='container'>
				<tr>
					<td style='background-color:#222222; width:25%'><h2>&nbsp&nbsp Pod Name</h2></td>
					<td style='background-color:#222222'><h3>&nbsp&nbsp " . getenv("MY_POD_NAME") . "</h2></td>
				</tr>
				<tr>
					<td style='background-color:#3C3C3C'><h2>&nbsp&nbsp Pod IP</h2></td>
					<td style='background-color:#3C3C3C'><h3>&nbsp&nbsp " . getenv("MY_POD_IP") . "</h3></td>
				</tr>
				<tr>
					<td style='background-color:#222222'><h2>&nbsp&nbsp Namespace</h2></td>
					<td style='background-color:#222222'><h3>&nbsp&nbsp " . getenv("MY_POD_NAMESPACE") . "</h3></td>
				</tr>
				<tr>
					<td style='background-color:#3C3C3C'><h2>&nbsp&nbsp K8S Node</h2></td>
					<td style='background-color:#3C3C3C'><h3>&nbsp&nbsp " . getenv("MY_NODE_NAME") . "</h3></td>
				</tr>
				   <tr>
                      <td style='background-color:#222222'><h2>&nbsp&nbsp App Version</h2></td>
                      <td style='background-color:#222222'><h3>&nbsp&nbsp " . '7.7.1 (minor color change)' . "</h3></td>
                  </tr>
				  <tr>
                      <td style='background-color:#3C3C3C'><h2>&nbsp&nbsp What's new</h2></td>
                      <td style='background-color:#3C3C3C'><h3>&nbsp&nbsp " . ' Added HighScore field and new Level Skins ' . "</h3></td>
                  </tr>";
echo "
			</table>";

// $servername = "localhost";
$servername = "mysql";
$username = "root";
$password = "password";
$dbname = "bricks";



$conn = new mysqli($servername, $username, $password, $dbname); // Create connection
if ($conn->connect_error) {     // Check connection
    die("Connection failed: " . $conn->connect_error);
} 

$sql = 'SELECT max(score) FROM highscore';

$result = $conn->query($sql);
$high_score_result = $result->fetch_array()[0] ?? '';

$level1_score = $_GET["s"];
$remaining_lives = $_GET["l"];
$conn->close();     
?> 
<canvas id="myCanvas" width="800" height="600" style="background: url('images/level2.png')"></canvas>
<button id="play-button" onclick="play2()">START LEVEL 2</button>
<style>
 *{
     box-sizing:border-box;
     margin:0;
     padding:0;
}
 body{
     display:grid;
     place-items:center;
     background:hsl(250 100% 2%);
}
 table {
     border-collapse: collapse;
     width: 20%;
     margin: 5px auto;
}
 td {
     outline:2px solid grey;
     text-align: left;
     height: 150;
}
 #play-button {
     background-color: #87D20A;
     padding: 0.5rem 1rem;
     cursor: pointer;
     font-size: 2rem;
     width: 800px;
     font-family: 'Titan One';
}
 #heading {
     text-align: center;
     color: grey;
     padding: 0.5rem 1rem;
     cursor: pointer;
     width: 800px;
     font-family: 'Open Sans', sans-serif;
     background-color: #222222;
     background: repeating-linear-gradient( 160deg, #222, #222 10px, #333 10px, #444 20px );
}
 h1 {
     background: -webkit-linear-gradient(#ffdb29, #FFCC00);
     -webkit-background-clip: text;
     -webkit-text-fill-color: transparent;
     font-family: 'Titan One';
     font-size: 5.5rem;
}
 .stroke-text{
     -webkit-text-stroke:.2px gray;
}
 h2 {
     color: #FB667A;
     text-decoration: none;
     font-family: 'Rubik';
     font-size: 1.5rem;
}
 h3 {
     font-family: 'Rubik';
     font-size: 1.5rem;
     color: #FFF842;
     text-decoration: none;
}
 #myCanvas {
     background-color: black;
     cursor: none;
}
 * {
     font-family: 'Open Sans', sans-serif;
}
 .title {
     font-size: 3.5rem;
     font-family: 'Open Sans', sans-serif;
}
 .container {
     text-align: left;
     overflow: hidden;
     width: 800px;
     margin: 0 auto;
     display: table;
     padding: 0 0 8em 0;
}
 
</style>
<script>

    var canvas2 = document.getElementById("myCanvas");
    var ctx2 = canvas2.getContext("2d");
    var ballRadius = 10;
    var x = canvas2.width/2;
    var y = canvas2.height-30;
    var dx2 = 6;
    var dy2 = -6;
    var paddleHeight2 = 20;
    var paddleWidth2 = 100;
    var paddleX2 = (canvas2.width-paddleWidth2)/2;
    var rightPressed2 = false;
    var leftPressed2 = false;
    var brickRowCount2 = 24; //24
    var brickColumnCount2 = 13; //13
    var brickWidth2 = 26;
    var brickHeight2 = 20;
    var brickPadding2 = 2;
    var brickOffsetTop2 = 70;
    var brickOffsetLeft2 = 65;
    var score2 = '<?php echo $level1_score; ?>'; 
	var highscore = <?php echo $high_score_result; ?>;
    var lives = <?php echo $remaining_lives; ?>;
    var bX2=(brickWidth2+brickPadding2)+brickOffsetLeft2;
    
	var bricks2 = [];
    for(var c=0; c<brickColumnCount2; c++) {
        bricks2[c] = [];
        for(var r=0; r<brickRowCount2; r++) {
            bricks2[c][r] = { x: 0, y: 0, status: 1 };
        }
    }



    document.addEventListener("keydown", keyDownHandler, false);
    document.addEventListener("keyup", keyUpHandler, false);
    document.addEventListener("mousemove", mouseMoveHandler, false);

    function keyDownHandler(e) {
        if(e.code  == "ArrowRight") {
            rightPressed2 = true;
        }
        else if(e.code == 'ArrowLeft') {
            leftPressed2 = true;
        }
    }
    function keyUpHandler(e) {
        if(e.code == 'ArrowRight') {
            rightPressed2 = false;
        }
        else if(e.code == 'ArrowLeft') {
            leftPressed2 = false;
        }
    }
    function mouseMoveHandler(e) {
        var relativeX = e.clientX - canvas2.offsetLeft;
        if(relativeX > 0 && relativeX < canvas2.width) {
            paddleX2 = relativeX - paddleWidth2/2;
        }
    }
    function collisionDetection2() {
        for(var c=0; c<brickColumnCount2; c++) {
            for(var r=0; r<brickRowCount2; r++) {
                var b = bricks2[c][r];
                if(b.status == 1) {
                    if(x > b.x && x < b.x+brickWidth2 && y > b.y && y < b.y+brickHeight2) {
                      var random = Math.floor(Math.random() * 11);
						if(random % 2 == 0) {

						dy2 = -dy2; }
						else {
							dx2 = -dx2
						}
                        b.status = 0;
                        score2++;
                        if(score2 == 6400+brickRowCount2*brickColumnCount2) {
                            alert("All Levels Compeltes\nCONGRATULATIONS!!!");
							saveHighScore();
                           level1(); 
							
                        }
					
                    }
                }
            }
        }
    }
	
    function drawBall2() {
        ctx2.beginPath();
        ctx2.arc(x, y, ballRadius, 0, Math.PI*120);
        ctx2.fillStyle = "#FFBA00";
		ctx2.strokeStyle = 'white';
		//ctx2.fillStyle = "#" + Math.floor(Math.random()*16715).toString(16);
        ctx2.fill();
        ctx2.closePath();
		ctx2.stroke();
    }
	 function drawPaddle3() {
        ctx2.beginPath();
        ctx2.rect(paddleX2, canvas2.height-paddleHeight2, paddleWidth2, paddleHeight2);
        ctx2.fillStyle = "#d23351";
		ctx2.strokeStyle = 'white';
        ctx2.fill();
        ctx2.closePath();
		ctx2.stroke();
    }

	
    function drawbricks2() {
		const colors = ['#d23351', '#d23351', '#a9dd0e', '#a9dd0e', '#84d4f1', '#84d4f1','#ffffff', '#e8e4af', '#e8e4af', '#c4d7fa', '#c4d7fa', '#3eeb70', '#3eeb70'];
        for(var c=0; c<brickColumnCount2; c++) {
            for(var r=0; r<brickRowCount2; r++) {
                if(bricks2[c][r].status == 1) {
                    var brickX = (r*(brickWidth2+brickPadding2))+brickOffsetLeft2;
                    var brickY = (c*(brickHeight2+brickPadding2))+brickOffsetTop2;
                    bricks2[c][r].x = brickX;
                    bricks2[c][r].y = brickY;
                    ctx2.beginPath();
                    ctx2.rect(brickX, brickY, brickWidth2, brickHeight2);
                 
					ctx2.fillStyle = colors[c];				

                    ctx2.fill();
                    ctx2.closePath();
                }
            }
        }
    }
	
	function drawLevel2() {
        ctx2.font = "20px Ubuntu Mono";
        ctx2.fillStyle = "#eeeeee";
        ctx2.fillText("Level: 2", 8, 20);
    }
	
	function drawScore() {
        ctx2.font = "20px Ubuntu Mono";
        ctx2.fillStyle = "#eeeeee";
        ctx2.fillText("Score: "+score2, canvas2.width-600, 20);
    }
	
	function drawHighScore() {
        ctx2.font = "20px Ubuntu Mono";
        ctx2.fillStyle = "#eeeeee";
        ctx2.fillText("High Score: "+highscore, canvas2.width-350, 25);
    }
	
    function drawLives() {
        ctx2.font = "20px Ubuntu Mono";
        ctx2.fillStyle = "#eeeeee";
        ctx2.fillText("Lives: "+lives, canvas2.width-100, 20);
    }
	
	
	
    function drawScore2() {
        ctx2.font = "20px Ubuntu Mono";
        ctx2.fillStyle = "#eeeeee";
        ctx2.fillText("Score: "+score2,  canvas2.width-600, 20);
    }
	
	function drawHighScore() {
        ctx2.font = "20px Ubuntu Mono";
        ctx2.fillStyle = "#eeeeee";
        ctx2.fillText("High Score: "+highscore, canvas2.width-350, 20);
    }
	
    function drawLives2() {
        ctx2.font = "20px Ubuntu Mono";
        ctx2.fillStyle = "#eeeeee";
        ctx2.fillText("Lives: "+lives, canvas2.width-100, 20);
    }

    function draw2() {
	
        ctx2.clearRect(0, 0, canvas2.width, canvas2.height);
        drawbricks2();
		drawHighScore();
        drawBall2();
        drawPaddle3();
        drawScore();
        drawLives();
		drawLevel2();
        collisionDetection2();
        if(x + dx2 > canvas2.width-ballRadius || x + dx2 < ballRadius) {
            dx2 = -dx2;
        }
        if(y + dy2 < ballRadius) {
            dy2 = -dy2;
        }
        else if(y + dy2 > canvas2.height-ballRadius) {
            if(x > paddleX2 && x < paddleX2 + paddleWidth2) {
                dy2 = -dy2;
            }
            else {
                lives--;
                if(!lives) {
                    alert("Game Over!!! \nRestart the Game?");
					saveHighScore();
                   	level1();
                }
                 else {
					 //alert("Missed the Ball? \n Do not worry we got you covered!");
                    x = canvas2.width/2;
                    y = canvas2.height-30;
                    dx2 = 6;
                    dy2 = -6;
                    paddleX2 = (canvas2.width-paddleWidth2)/2;
                } 
            }
        }

        if(rightPressed2 && paddleX2 < canvas2.width-paddleWidth2) {
            paddleX2 += 7;
        }
        else if(leftPressed2 && paddleX2 > 0) {
            paddleX2 -= 7;
        }

        x += dx2;
        y += dy2;
        requestAnimationFrame(draw2);
    }

	drawLevel2();
	drawScore2();
	drawHighScore();
	drawLives2();
	pressStart2();

	
		function play2() {   
			draw2();
		}

	
		function saveHighScore() {
		 level1();
		 } 
		
		function level1() {
			window.location = "index.php"
		}
		
			function pressStart2() {
    ctx2.font = '50px Rubik';
    ctx2.fillStyle = 'white';
    ctx2.fillText('PRESS START\nfor Level 2', canvas2.width / 2 - 275, canvas2.height / 2);
};
</script>


</body2>
</html>