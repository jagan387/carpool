<?php
    session_start();
    session_destroy();
    header("Location: index.php");
    exit;
?> 
<html>
    <head>
        <title>CarPool</title>
        <meta http-equiv="REFRESH" content="0;url=index.php" />
    </head>
</html>