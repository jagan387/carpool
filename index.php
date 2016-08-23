<?php
    session_start();
    if(isset($_POST[username])) {    
        require_once 'DBUtil.php';
        $query = "SELECT password,name FROM users WHERE username='$_POST[username]'";
        $result = DBUtil::executeQuery($query);
        $result_array = mysql_fetch_row($result);
        if($result_array[0] == $_POST[password]) {
            $_SESSION[username] = $_POST[username];
            $_SESSION[name] = $result_array[1];
            unset($_POST[username]);
            unset($_POST[password]);
            header("Location: profile.php");
        } else {
            unset($_POST[username]);
            unset($_POST[password]);
            $_SESSION[loginFailed] = true;
        }
    }
?>
<html>
    <head>
        <title>CarPool</title>
        <link href="stylesheets/public.css" media="all" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="javascript/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="javascript/jquery-migrate-1.2.1.min.js"></script> <!-- Need this for jquery 1.9+ so that old apps/features are supported -->
        <script type="text/javascript" src="javascript/scripts.js"></script>
    </head>
    <body>
        <br /><br /><br />
        <h1>CAR-POOL</h1>
        <h2>Simple car pooling site for corporates</h2>
        <br /><br /><br />
        <div align="center">
            <div align="center">
                <form action="index.php" method="post" onsubmit="return validateLogin()">
                    <table align="center">
                        <tr>
                            <td><label>Username</label></td>
                            <td> </td>
                            <td><input type="text" name="username" value="" id="username" placeholder="Your username" /></td>
                        </tr>
                        <tr></tr><tr></tr><tr></tr><tr></tr>
                        <tr>
                            <td><label>Password</label></td>
                            <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                            <td><input type="password" name="password" value=""  id="password" placeholder="Your password" /></td>
                        </tr>
                    </table>
                    <br />
                    <div style="width:175px" align="center" >
                        <input style="width:85;float:left" type="submit" name="login" value="LogIn" id="login"></input>
                        <input style="width:85;float:right" type="reset" name="reset" value="Reset" id="reset"></input>
                    </div>
                        
                </form>
            </div>
            <div>
                <?php
                    if($_SESSION[loginFailed]) {
                        echo '<br /><p style="color:red">Invalid Username and/or Password</p>';
                        unset($_SESSION[loginFailed]);
                    }
                ?>
            </div>
            <br />
            <div align="center">
                <input style="width:175px" type="button" id="showReg" class="showReg" value="Create my account"/>
                <div>
                    <br/>
                    <div id="registrationForm">
                    </div>
                        <?php
                            if($_SESSION[registrationFailed]) {
                                echo '<script>window.onload = function (){document.getElementById("showReg").click();};</script>';
                                echo '<br /><p style="color:red">One(or more) error(s) occurred while creating your account</p>';
                                unset($_SESSION[registrationFailed]);
                            }
                        ?>
                </div>
            </div>
        </div>
        <?php include("footer.php"); ?>
    </body>
</html>