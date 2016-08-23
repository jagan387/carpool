<?php
    session_start();
    if (!isset($_SESSION['username']) && isset($_POST['uname'])) {
        require_once 'DBUtil.php';
        $query = "INSERT INTO users (username,password,name,phone,email,gender) VALUES('$_POST[uname]','$_POST[pwd]','$_POST[name]','$_POST[phone]','$_POST[email]','$_POST[gender]')";
        $result = DBUtil::executeQuery($query);
        if ($result) {
            echo "<form id='autologin' action='index.php' method='post' style='display:none'>";
            echo "<input type='text' name='username' value='$_POST[uname]' id='username' />";
            echo "<input type='password' name='password' value='$_POST[pwd]'  id='password' />";
            echo "</form>";
            unset($_POST['uname']);
            echo "<script type='text/javascript'>document.getElementById('autologin').submit();</script>";
            exit;
        } else {
            $_SESSION['registrationFailed'] = true;
            echo '<script> $(document).ready(function(){document.getElementById("showReg").click();});</script>';
            unset($_POST['uname']);
            redirect("index.php");
        }
    } else if (isset($_SESSION['username']) && isset($_POST['uname'])) {
        $oldAttrs = $_SESSION['userDetails'];
        unset($_SESSION['userDetails']);
        $defQuery = "UPDATE users SET ";
        $query = $defQuery;
        echo $query;
        if(strcmp($_POST['name'], $oldAttrs[0]) != 0) {
            $query .= "name = '$_POST[name]', ";
        }
        if(strcmp($_POST['uname'], $oldAttrs[1]) != 0) {
            $query .= "username = '$_POST[uname]', ";
        }
        if(strcmp($_POST['pwd'], $oldAttrs[2]) != 0) {
            $query .= "password = '$_POST[pwd]', ";
        }
        if(strcmp($_POST['gender'], $oldAttrs[3]) != 0) {
            $query .= "gender = '$_POST[gender]', ";
        }
        if(strcmp($_POST['phone'], $oldAttrs[4]) != 0) {
            $query .= "phone = '$_POST[phone]', ";
        }
        if(strcmp($_POST['email'], $oldAttrs[5]) != 0) {
            $query .= "email = '$_POST[email]'";
        }
        if(strcmp($query, $defQuery) != 0) {
            if(endsWith($query, ", "))
                $query = substr($query, 0, strlen($query)-strlen(", "));
            $query .= " WHERE username = "."'$_SESSION[username]'";
            $query .= ";";
            require_once 'DBUtil.php';
            $result = DBUtil::executeQuery($query);
        }
        
        $_SESSION['name'] = $_POST['name'];
        $_SESSION['username'] = $_POST['uname'];
        unset($_POST['uname']);
        redirect("profile.php");
    }
    
    function redirect($url) {
        if (!headers_sent()) {    
            header('Location: '.$url);
            exit;
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="'.$url.'";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
            echo '</noscript>';
            exit;
        }
    }
    
    function endsWith($haystack, $needle) {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
?>
<html>
    <head>
        <title>CarPool</title>
        <link href="stylesheets/public.css" media="all" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="javascript/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="javascript/jquery-migrate-1.2.1.min.js"></script> <!-- Need this for jquery 1.9+ so that old apps/features are supported -->
        <script type="text/javascript" src="javascript/scripts.js"></script>
        <script>
            var user_details;
            function registrationToUpdate() {
                user_details = <?php
                                    require_once 'DBUtil.php';
                                    $query = "SELECT password,gender,phone,email FROM users where username='$_SESSION[username]'";
                                    $result = DBUtil::executeQuery($query);
                                    $result_array = mysql_fetch_array($result);
                                    array_unshift($result_array, $_SESSION['username']);
                                    array_unshift($result_array, $_SESSION['name']);
                                    $_SESSION['userDetails'] = $result_array;
                                    echo json_encode($result_array);
                               ?>;
                fillForm();
                document.registrationForm.action="userForm.php";
                document.getElementById("buttons").innerHTML='<input type="submit" disabled="" value="Update" name="update" id="update"/>&nbsp;&nbsp;&nbsp;<input type="button" disabled="" value="Reset" name="reset" id="reset"/>';
            }
            
            function fillForm() {
                document.getElementById("name").value = user_details[0];
                document.getElementById("uname").value = user_details[1];
                document.getElementById("pwd").value = user_details[2];
                document.getElementById("gender").value = user_details[3];
                document.getElementById("phone").value = user_details[4];
                document.getElementById("email").value = user_details[5];
            }
            
            $(document).ready(function(){
                    $("#reset").click(function(){
                        fillForm();
                        document.getElementById("update").disabled = true;
                        document.getElementById("reset").disabled = true;
                    });
            });
        </script>
    </head>
    <body>
        <div align="center">
            <form id="registrationForm" name="registrationForm" action="userForm.php" method="post" onsubmit="return validateRegistration()" >
                <table>
                    <tr>
                        <td><label>Name</label></td>
                        <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                        <td><input type="text" name="name" value="" id="name" placeholder="Full Name"></td>
                        <td>*</td>
                    </tr>
                    <tr></tr><tr></tr><tr></tr>
                    <tr>
                        <td><label>Username</label></td>
                        <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                        <td><input type="text" name="uname" value="" id="uname" placeholder="Choose your username"/></td>
                        <td>*</td>
                    </tr>
                    <tr></tr><tr></tr><tr></tr>
                    <tr>
                        <td><label>Password</label></td>
                        <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                        <td><input type="password" name="pwd" value="" id="pwd" placeholder="Create a password"/></td>
                        <td>*</td>
                    </tr>
                    <tr></tr><tr></tr><tr></tr>
                    <tr>
                        <td><label>Gender</label></td>
                        <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                        <td><select type="text" name="gender" value="" id="gender">
                                <option value="" selected disabled>Select your gender</option>
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                        </td>
                        <td>*</td>
                    </tr>
                    <tr></tr><tr></tr><tr></tr>
                    <tr>
                        <td><label>Phone</label></td>
                        <td><p align="right">+91-</p></td>
                        <td><input type="text" name="phone" value="" id="phone" placeholder="10 digit phone number"/></td>
                        <td> </td>
                    </tr>
                    <tr></tr><tr></tr><tr></tr>
                    <tr>
                        <td><label>E-mail</label></td>
                        <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                        <td><input type="text" name="email" value="" id="email" placeholder="Current e-mail address"/></td>
                        <td>*</td>
                    </tr>
                    <tr></tr><tr></tr><tr></tr>
                </table>
                <br />
                <div align="center" id="buttons">
                    <input type="submit" value="Register" name="register" id="register"/>&nbsp;&nbsp;&nbsp;
                    <input type="reset" value="Reset" name="reset" id="reset"/>&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Cancel" class="hideReg" id="cancel"/>
                </div>
            </form>
        </div>
        <script>
            // Convert registration form to update form if the form is being loaded from profile page
            if(window.loadProfile)
                registrationToUpdate();
            
            // Enable submit button only if form data is modified
            // Get form element type
            $.fn.getType = function () { 
                if(this[0].tagName == "INPUT")
                    return $(this[0]).attr("type").toLowerCase();
                else
                    return this[0].tagName.toLowerCase();        
            }
            
            // Store original values
            var orig = [];             
            $("form :input").each(function () {
                var type = $(this).getType();
                var tmp = {'type': type, 'value': $(this).val()};
                if (type == 'radio') { tmp.checked = $(this).is(':checked'); }
                orig[$(this).attr('id')] = tmp;
            });
            
            // Check values on change
            $('form').bind('keyup change', function () {
                var disable = true;
                $("form :input").each(function () {
                    var type = $(this).getType();
                    var id = $(this).attr('id');    
                    if (type == 'text' || type == 'select') {
                        disable = (orig[id].value == $(this).val());
                    } else if (type == 'radio') {
                        disable = (orig[id].checked == $(this).is(':checked'));
                    }    
                    if (!disable) { return false;} // break out of loop
                });
                $('#update').prop('disabled', disable); // update disabled property for update button
                $('#reset').prop('disabled', disable); // update disabled property for reset button
            });
        </script>
    </body>
</html>