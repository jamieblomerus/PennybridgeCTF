<?php
    header("HTTP/1.0 404 Not Found");
?>
<html>
    <head>
        <title>404</title>
        <style>
            body {
                text-align: center;
                font-family: arial;
                margin: 0;
                width: 100vw;
                height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            a {
                font-size: 12px;
                position: absolute;
                top: 0;
                right: 0;
                margin: 10px;
            }
            h1 {
                font-size: 50px;
                margin: 0;
            }
            p {
                font-size: 20px;
                color: #666;
            }
        </style>
    </head>
    <body>
        <a href="#" onclick="alert('No, you\'re not.')">Login as webmaster</a>
        <h1>404 - <i style="color: #404040">Sidan kunde inte hittas</i></h1>
        <p>Sidan du försökte nå kunde inte hittas. Kanske har den flyttat eller så har du skrivit in fel adress?</p>
    </body>
</html>