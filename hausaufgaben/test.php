<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rainbow test</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
    <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">
    <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>


    <style>

        .big-div-rainbow {
            width: 100%;
            height: 60px;
            margin-top: 2px;
            margin-bottom: 0px;
            padding: 4px;
            padding-left: 10px;
            border-radius: 5px;
            position: relative;
            z-index: 10;
        }

        .big-div-rainbow:after {
            content: '';
            display: block;
            position: absolute;
            top: 0px;
            left: 5px;
            right: 0px;
            bottom: 0px;
            z-index: -1;

            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ff3232), color-stop(16%,#fcf528), color-stop(32%,#28fc28), color-stop(50%,#28fcf8), color-stop(66%,#272ef9), color-stop(82%,#ff28fb), color-stop(100%,#ff3232));

            background-size: 1000%;
            -moz-background-size: 1000%;
            -webkit-background-size: 1000%;


            -webkit-animation-name: fun-time-awesome;
            -webkit-animation-duration: 1s;
            -webkit-animation-timing-function: linear;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-direction: alternate;
            -webkit-animation-play-state: running;
        }

        .big-div-on-mouse-rainbow {
            width: 100%;
            height: 60px;
            margin-top: 0px;
            margin-bottom: 2px;
            padding: 4px;
            padding-left: 10px;
            cursor: pointer;
            border-radius: 3px;
            position: relative;
            z-index: 10;
        }

        .big-div-on-mouse-rainbow:after {
            content: '';
            display: block;
            position: absolute;
            top: 0px;
            left: 5px;
            right: 0px;
            bottom: 0px;
            z-index: -1;

            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ff3232), color-stop(16%,#fcf528), color-stop(32%,#28fc28), color-stop(50%,#28fcf8), color-stop(66%,#272ef9), color-stop(82%,#ff28fb), color-stop(100%,#ff3232));

            background-size: 1000%;
            -moz-background-size: 1000%;
            -webkit-background-size: 1000%;


            -webkit-animation-name: fun-time-awesome;
            -webkit-animation-duration: 1s;
            -webkit-animation-timing-function: linear;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-direction: alternate;
            -webkit-animation-play-state: running;
        }

        @-webkit-keyframes fun-time-awesome {
            0% {background-position: left top;}
            100% {background-position: left bottom;}
        }

        /*
wahooo!!!!!!! It worked!!!


        html, body {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
        }
        body {
            font-family: 'Press Start 2P', cursive;

            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ff3232), color-stop(16%,#fcf528), color-stop(32%,#28fc28), color-stop(50%,#28fcf8), color-stop(66%,#272ef9), color-stop(82%,#ff28fb), color-stop(100%,#ff3232));

            background-size: 1000%;
            -moz-background-size: 1000%;
            -webkit-background-size: 1000%;

            -webkit-animation-name: fun-time-awesome;
            -webkit-animation-duration: 1s;
            -webkit-animation-timing-function: linear;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-direction: alternate;
            -webkit-animation-play-state: running;
        }

        @-webkit-keyframes fun-time-awesome {
            0% {background-position: left top;}
            100% {background-position: left bottom;}
        } */



    </style>

</head>
<body style="margin: 0">

<div style="margin: 5px">

<div><p style="margin-bottom: 0; margin-top: 0; margin-top: 10px; padding-bottom: 10px">Hausaufgaben zu erledigen:</p></div>

<div style="border: 3px solid #22a7e0; box-shadow: 0 5px 10px #22a7e0; background-color: #22a7e0" class="big-div" onmouseover="hover(this, 5.5)" onmouseleave="leave(this, 5.5)" onclick="hausaufgabe_bearbeiten()">
    <span class="big-span">Test</span>
    <span class="noten-span" style="float: right">Note bzw anzahl Tage</span>
    <br>
    <div class="small-div">
        <img style="height: 10px" src="/planer/icons/calendar.png"> datum
    </div>
</div>

</div>

</body>
</html>