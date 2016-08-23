<?php
    include("header.php");
?>
<html>
    <head>
        <title>CarPool</title>
        <link href="stylesheets/public.css" media="all" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="javascript/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="javascript/jquery-migrate-1.2.1.min.js"></script> <!-- Need this for jquery 1.9+ so that old apps/features are supported -->
        <script>
            window.loadProfile=true;
        </script>
    </head>
    <body>
        <style>
            #header #viewProfileMenu {
                color:#fff;
                background:#939393;
            }
        </style>
        <br />
        <div id="location"><a href="profile.php">Home</a> > <a href="profile.php">My Profile</a></div>
        <br />
        <h1 align="left">My Profile</h1>
        <div align="center">
            <div id="updateForm">
            </div>
        </div>
        <script>
            $("#updateForm").load("userForm.php");
        </script>
    </body>
</html>