<!DOCTYPE html>
<html>
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<head>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Titan+One&family=Rubik">
    <meta charset="utf-8" />
    <title>Block Buster - Level 1</title>
</head>
<body style="background: url('https://media.tenor.com/TZaIBNauQfAAAAAC/stars-galaxy.gif')">
	<div  id="heading" >
         <h1 class="stroke-text">BLOCK BUSTER</h1> 
    </div>
<?php

echo "<table class='container'>
                  <tr>
                      <td style='background-color:#222222; width:25%'><h2>&nbsp&nbsp Pod Name</h2></td>
                      <td style='background-color:#222222'><h3>&nbsp&nbsp " . getenv("MY_POD_NAME") . "</h3></td>
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
                      <td style='background-color:#3C3C3C'><h2>&nbsp&nbsp K8S Node</h3></td>
                      <td style='background-color:#3C3C3C'><h3>&nbsp&nbsp " . getenv("MY_NODE_NAME") . "</h3></td>
                  </tr>
				   <tr>
                      <td style='background-color:#222222'><h2>&nbsp&nbsp App Version</h2></td>
                      <td style='background-color:#222222'><h3>&nbsp&nbsp " . '7.10.0' . "</h3></td>
                  </tr>
				  <tr>
                      <td style='background-color:#3C3C3C'><h2>&nbsp&nbsp What's new</h2></td>
                      <td style='background-color:#3C3C3C'><h3>&nbsp&nbsp " . 'Updated the background - Starry Night ' . "</h3></td>
                  </tr>";
echo "</table>";

// $servername = "localhost";
$servername = "mysql.database.svc.cluster.local";
$username = "root";
$password = "mysql-password-0123456789";
$dbname = "bricks";

$conn = new mysqli($servername, $username, $password, $dbname); // Create connection
if ($conn->connect_error) {     // Check connection
    die("Connection failed: " . $conn->connect_error);
} 

$sql = 'SELECT max(score) FROM highscore';

$result = $conn->query($sql);
$high_score_result = $result->fetch_array()[0] ?? '';

$conn->close();     
?>
<canvas id="myCanvas" width="800" height="600"  style="background: url('images/level1.png')"></canvas>
    <button id="play-button" onclick="play()">START GAME</button>
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
    var canvas = document.getElementById("myCanvas");
    var ctx = canvas.getContext("2d");
    var ballRadius = 10;
    var x = canvas.width/2;
    var y = canvas.height-30;
    var dx = 5;
    var dy = -5;
    var paddleHeight = 30;
    var paddleWidth = 175;
    var paddleX = (canvas.width-paddleWidth)/2;
    var rightPressed = false;
    var leftPressed = false;
    var brickRowCount = 8;
    var brickColumnCount = 8;
    var brickWidth = 80;
    var brickHeight = 30;
    var brickPadding = 5;
    var brickOffsetTop = 70;
    var brickOffsetLeft = 65;
    var score = 0;
	var highscore = <?php echo $high_score_result; ?>;
    var lives = 3;
	
	var bX=(brickWidth+brickPadding)+brickOffsetLeft;

    var bricks = [];
    for(var c=0; c<brickColumnCount; c++) {
        bricks[c] = [];
        for(var r=0; r<brickRowCount; r++) {
            bricks[c][r] = { x: 0, y: 0, status: 1 };
        }
    }



    document.addEventListener("keydown", keyDownHandler, false);
    document.addEventListener("keyup", keyUpHandler, false);
    document.addEventListener("mousemove", mouseMoveHandler, false);

    function keyDownHandler(e) {
        if(e.code  == "ArrowRight") {
            rightPressed = true;
        }
        else if(e.code == 'ArrowLeft') {
            leftPressed = true;
        }
    }
    function keyUpHandler(e) {
        if(e.code == 'ArrowRight') {
            rightPressed = false;
        }
        else if(e.code == 'ArrowLeft') {
            leftPressed = false;
        }
    }
    function mouseMoveHandler(e) {
        var relativeX = e.clientX - canvas.offsetLeft;
        if(relativeX > 0 && relativeX < canvas.width) {
            paddleX = relativeX - paddleWidth/2;
        }
    }
    function collisionDetection() {
        for(var c=0; c<brickColumnCount; c++) {
            for(var r=0; r<brickRowCount; r++) {
                var b = bricks[c][r];
                if(b.status == 1) {
                    if(x > b.x && x < b.x+brickWidth && y > b.y && y < b.y+brickHeight) {
                      
var random = Math.floor(Math.random() * 11);
if(random % 2 == 0) {

dy = -dy; }
else {
	dx = -dx
}
                        b.status = 0;
                        score++;
                        if(score == brickRowCount*brickColumnCount) {
                        alert("Level 1 Completed!");
							//saveHighScore();
                            //document.location.reload();
							level2();
                        } 
                    }
                }
            }
        }
    }



    function drawBall() {
        ctx.beginPath();
        ctx.arc(x, y, ballRadius, 0, Math.PI*120);
        ctx.fillStyle = "#FFBA00";
		ctx.strokeStyle = 'black';
		//ctx.fillStyle ='hsl(' + 360 * Math.random() + ', 20%, 50%)';
        ctx.fill();
        ctx.closePath();
		ctx.stroke();
    }
	
	
	
	let images = {
	  paddle: new Image(),
	  heart: new Image(),
	  podium: new Image(),
	  score: new Image() 
	  }

	images.paddle.src = 'images/paddle.png'; 
	images.heart.src = 'images/heart.png';
	images.podium.src = 'images/podium.png';
	images.score.src = 'images/score.png';

	function drawPaddle() {
       ctx.drawImage(images.paddle, paddleX, canvas.height-paddleHeight,paddleWidth, paddleHeight);

    }
    function drawBricks() {
		const colors = ['#00BD8D', '#00BD8D', '#fc6e22', '#fc6e22', '#c24cf6', '#c24cf6','#ffff66', '#ffff66'];
        for(var c=0; c<brickColumnCount; c++) {
            for(var r=0; r<brickRowCount; r++) {
                if(bricks[c][r].status == 1) {
                    var brickX = (r*(brickWidth+brickPadding))+brickOffsetLeft;
                    var brickY = (c*(brickHeight+brickPadding))+brickOffsetTop;
                    bricks[c][r].x = brickX;
                    bricks[c][r].y = brickY;
                    ctx.beginPath();
					// ctx.drawImage(images.brick, brickX, brickY, brickWidth, brickHeight);

                  ctx.rect(brickX, brickY, brickWidth, brickHeight);
						//ctx.fillStyle = "#eeeeee";
					ctx.fillStyle = colors[c];
						//ctx.fillStyle = "#" + Math.floor(Math.random()*16777215).toString(16);
                  ctx.fill();
                 ctx.closePath();
                }
            }
        }
    }
    function drawLevel() {
		ctx.font = "20px Ubuntu Mono";
        ctx.fillStyle = "#eeeeee";
        ctx.fillText("Level: 1", 8, 20); 
    }
	function drawScore() {
		ctx.drawImage(images.score, canvas.width-550, 5);
        ctx.font = "20px Ubuntu Mono";
        ctx.fillStyle = "#eeeeee";
        ctx.fillText(score*100, canvas.width-500, 25);
    }
	
	function drawHighScore() {
		ctx.drawImage(images.podium, canvas.width-300, 5);
        ctx.font = "20px Ubuntu Mono";
        ctx.fillStyle = "#eeeeee";
        ctx.fillText(highscore, canvas.width-250, 25);
    }
	
    function drawLives() {
		ctx.drawImage(images.heart, canvas.width-85, 5);
        ctx.font = "20px Ubuntu Mono";
        ctx.fillStyle = "#eeeeee";
        ctx.fillText(lives, canvas.width-50, 25);
    }

	function drawScore_text() {
        ctx.font = "20px Ubuntu Mono";
        ctx.fillStyle = "#eeeeee";
        ctx.fillText('Score '+score*100, canvas.width-600, 20);
    }
	
	function drawHighScore_text() {
        ctx.font = "20px Ubuntu Mono";
        ctx.fillStyle = "#eeeeee";
        ctx.fillText('HighScore '+highscore, canvas.width-350, 20);
    }
	
    function drawLives_text() {
        ctx.font = "20px Ubuntu Mono";
        ctx.fillStyle = "#eeeeee";
        ctx.fillText('Lives '+lives, canvas.width-100, 20);
    }


    function draw() {
	    ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawBricks();
		drawHighScore();
        drawBall();
        drawPaddle();
        drawScore();
        drawLives();
		drawLevel();
        collisionDetection();
        if(x + dx > canvas.width-ballRadius || x + dx < ballRadius) {
            dx = -dx;
        }
        if(y + dy < ballRadius) {
            dy = -dy;
        }
        else if(y + dy > canvas.height-ballRadius) {
            if(x > paddleX && x < paddleX + paddleWidth) {
                dy = -dy;
            }
            else {
                lives--;
				/* if(lives) {
				       alert("Missed the Ball? \n Do not worry we got you covered!");
						//document.location.reload();
				  } */
                if(!lives) {
					alert("Game Over!!! \nRestart the Game?");
					saveHighScore();
                    document.location.reload();
                }
                else {
					// alert("Missed the Ball? \nDo not worry we got you covered!");
					// saveHighScore();
                    x = canvas.width/2;
                    y = canvas.height-30;
                    dx = 5;
                    dy = -5;
                    paddleX = (canvas.width-paddleWidth)/2;
                }
            }
        }

        if(rightPressed && paddleX < canvas.width-paddleWidth) {
            paddleX += 7;
        }
        else if(leftPressed && paddleX > 0) {
            paddleX -= 7;
        }

        x += dx;
        y += dy;
        requestAnimationFrame(draw);
    }


		pressStart();
		drawLevel();
		drawScore_text();
		drawHighScore_text();
		drawLives_text();
		function play() {   
			draw();
		}
	
		function saveHighScore() {
		$.post("highscore.php",
		{
			highScore: score*100
		});}
		
		function level2() {
		window.location = "level2.php?s="+score*100+"&l="+lives
		}
		
		
		function pressStart() {
    ctx.font = '50px Rubik';
    ctx.fillStyle = 'white';
    ctx.fillText('PRESS START', canvas.width / 2 - 150, canvas.height / 2);
};


</script>


</body>
</html>