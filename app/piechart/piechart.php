<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body{
                background-color: #FFF;
            }
            canvas{

            }
        </style>
    </head>
    <body>
    <canvas id = "chart" width = "400px" height = "400px"></canvas>
    </body>

    <script>
        var canvas = document.getElementById("chart");
        var pieChrt = canvas.getContext("2d");

        var colors = ['#4caf50','#00bcd4','#e91e63','#FFc107','#CDDC39'];
        var angles = [Math.PI * 0.2, Math.PI * 0.3, Math.PI * 0.1, Math.PI * 0.8, Math.PI * 0.6, ];
        var beginAngle = 0;
        var endAngle = 0;

        var ang = 0;
        var text_x = 0;
        var text_y = 0;

        var x = 200;
        var y = 200;
        var r = 120;
        var r_t = r + 20;
        var str = ['hardisk','ssdadfadfsf','k','power','memory'];

        pieChrt.font = "16px Arial";
        pieChrt.lineWidth = 2;

        for(var i = 0; i < angles.length; i++) {
        var r_start = 0;
        var r_end = 0;
            beginAngle = endAngle;
            endAngle = endAngle + angles[i];

            pieChrt.beginPath();
            pieChrt.fillStyle = colors[i % colors.length];

            pieChrt.moveTo(x, y);
            pieChrt.arc(x, y, r, beginAngle, endAngle);
            pieChrt.lineTo(x, y);
            pieChrt.strokeStyle = "#FFF";
            pieChrt.stroke();

            pieChrt.fill();

            ang = beginAngle + angles[i] / 2;

            r_start = r + 20;
            r_end = r + 20 + pieChrt.measureText(str[i]).width;

            if((ang > Math.PI * 0.5) && (ang < Math.PI * 1.5)) {

                text_x = x + Math.cos(ang) * r_end;
            }
            else {
                text_x = x + Math.cos(ang) * r_start;
            }

            if((ang >= 0) && (ang <= Math.PI) ) {
                text_y = y + 13 + Math.sin(ang) * r_start;
            }
            else {
                text_y = y + Math.sin(ang) * r_start;
            }
            pieChrt.fillStyle = "#000";
            pieChrt.fillText(str[i],text_x, text_y);
        }

        //pieChrt.fillText("123",100,200);
    </script>
</html>
