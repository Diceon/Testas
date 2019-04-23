<?php

if ($_SERVER["PHP_AUTH_USER"] != '123' & $_SERVER["PHP_AUTH_PW"] != '123') {
    header('WWW-Authenticate: Basic realm:\"Prime\""');
    header('HTTP:\ 1.0 401 Unauthorized');
    echo "Curious one, arent't you?";
    exit();
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>I'm in!</title>
    </head>
    <body>
        <h1>Nice one!</h1>
    </body>
</html>