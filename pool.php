<?php
    include("header.php");
?>
<html>
    <head>
        <title>CarPool</title>
        <link href="stylesheets/public.css" media="all" rel="stylesheet" type="text/css" />
        <link href="stylesheets/jquery-ui.css" media="all" rel="stylesheet" type="text/css" />
		<link href="stylesheets/jquery-ui-timepicker-addon.css" media="all" rel="stylesheet" type="text/css" />
        <link href="stylesheets/jquery.tablesorter.pager.css" media="all" rel="stylesheet" type="text/css" />
        <link href="stylesheets/jquery.tablesorter.css" media="all" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="javascript/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="javascript/jquery-migrate-1.2.1.min.js"></script> <!-- Need this for jquery 1.9+ so that old apps/features are supported -->
        <script type="text/javascript" src="javascript/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="javascript/jquery.tablesorter.pager.js"></script>
		<script type="text/javascript" src="javascript/jquery-ui.min.js"></script>
		<script type="text/javascript" src="javascript/jquery-ui-timepicker-addon.js"></script>
        <script type="text/javascript" src="javascript/jquery.livequery-1.1.1.min.js"></script>
        <script type="text/javascript" src="javascript/scripts.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#input_poolForm_startTime").datetimepicker({dateFormat: "yy-m-dd", timeFormat: "HH:mm:ss"});
            });
            
            $(document).ready(function() {
                $("#viewPools").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#viewPoolsPager"), size:13});
            });
            
            function showPoolsStarted() {
                $("#target").load("poolsStarted.php");
            }

            function showPoolsJoined() {
                $("#target").load("poolsJoined.php");
            }
            
            function toggleVehSelectDetails(set, $selectedOption) {
                $model = document.getElementById("input_poolForm_vehicle_model");
                $color = document.getElementById("input_poolForm_vehicle_color");
                $regNo = document.getElementById("input_poolForm_vehicle_regNo");
                $occupancy = document.getElementById("input_poolForm_vehicle_occupancy");
                $model.value = (set) ? $selectedOption.text : "";
                $color.value = (set) ? $selectedOption.getAttribute("color") : "";
                $regNo.value = (set) ? $selectedOption.getAttribute("regNo") : "";
                $occupancy.value = (set) ? $selectedOption.getAttribute("occupancy") : "";
                $model.disabled = (set) ? true : "";
                $color.disabled = (set) ? true : "";
                $regNo.disabled = (set) ? true : "";
                $occupancy.disabled = (set) ? true : "";
                document.getElementById("selVehButton").style.display = (set) ? "none" : "block";
                document.getElementById("vehReg").style.display = (set) ? "block" : "none";
            }
            
            function showVehReg() {
                toggleVehSelectDetails(false, null);
                document.getElementById("vehReg").style.display = "block";
                document.getElementById("vehSelect").style.display = "none";
                setVehType("registration");
            }

            function showVehSelect() {
                document.getElementById("vehReg").style.display = "none";
                document.getElementById("vehSelect").style.display = "block";
                setVehType("select");
                selectedVehicle = document.getElementById("input_poolForm_vehicle");
                if(selectedVehicle.value != "")
                    selectVehicle(selectedVehicle);
            }

            function setVehType(type) {
                document.getElementById("input_poolForm_vehicle_type").value=type;
            }

            function showVehSelectDetails() {
                document.getElementById("vehReg").style.display = "block";
            }
        </script>
    </head>
    <body>
        <?php
            $menuToSelect = null;
            switch($_GET['op']) {
                case 'create':              $menuToSelect = "create";           
                                            createPool();
                                            break;
                case 'view':                $menuToSelect = "view";
                                            viewPool();
                                            break;
                case 'join':                $menuToSelect = "view";
                                            joinPool();
                                            break;
                case 'leave':               $menuToSelect = "edit";
                                            leavePool();
                                            break;
                case 'edit':                $menuToSelect = "edit";
                                            editPool();
                                            break;
                case 'delete':              $menuToSelect = "edit";
                                            deletePool();
                                            break;
            }
            
            echo "<style>#header #".$menuToSelect."PoolMenu {color:#fff;background:#939393;}</style>";
            
            function createLocation($location) {
                require_once 'DBUtil.php';
                $query = "INSERT INTO locations (name) VALUES ('".$location."')";
                $result = DBUtil::executeQuery($query);
                return $result;
            }
            
            function addVehicle($vehicle) {
                require_once 'DBUtil.php';
                $query = "INSERT INTO vehicles (uid,model,color,regNo,occupancy) VALUES ('".$_SESSION[username]."','".$vehicle."','".$_POST[input_poolForm_vehicle_color]."','".$_POST[input_poolForm_vehicle_regNo]."',".$_POST[input_poolForm_vehicle_occupancy].")";
                $result = DBUtil::executeQuery($query);
                return $result;
            }
            
            function isEmptyArray($array) {
                if(!$array || $array === null || $array == null || count($array) == 0)
                    return true;
                else {
                    foreach($array as $val) {
                        if(!isEmptyString($val)) {
                            return false;
                        }
                    }
                    return true;
                }
            }
            
            function isEmptyString($string) {
                if($string && $string != null && $string != "") {
                    return false;
                } else
                    return true;
            }
            
            function createPool() {
                if(isset($_POST['input_poolForm_startTime'])) {
                    $startFrom = $_POST[input_poolForm_from];
                    if($_POST[input_poolForm_from_id] == null) {
                        $startFrom = ucwords($startFrom);
                        if(!createLocation($startFrom)) {
                            echo "<div align='center'><br /><br /><br /><b>Error occurred while creating a new location '{$startFrom}' for 'From' field. Please try again</b><br />Redirecting back to Create Pool</div>"; 
                            redirect('pool.php?op=create');
                        }
                    }
                    
                    $upTo = $_POST[input_poolForm_to];
                    if($_POST[input_poolForm_to_id] == null) {
                        $upTo = ucwords($upTo);
                        if(!createLocation($upTo)) {
                            echo "<div align='center'><br /><br /><br /><b>Error occurred while creating a new location '{$upTo}' for 'To' field. Please try again</b><br />Redirecting back to Create Pool</div>"; 
                            redirect('pool.php?op=create');
                        }
                    }
                    
                    $viaArray = $_POST[input_poolForm_via];
                    $viaIdArray = $_POST[input_poolForm_via_id];
                    $via = array();
                    if(!isEmptyArray($viaArray)) {
                        $allNewLoc = isEmptyArray($viaIdArray);
                        for($i=0; $i<count($viaArray); $i++) {
                            $loc = $viaArray[$i];
                            if(isEmptyString($loc))
                                continue;
                            else {
                                if($allNewLoc || isEmptyString($viaIdArray[$i])) {
                                    $loc = ucwords($loc);
                                    if(createLocation($loc))
                                        array_push($via, $loc);
                                    else {
                                        echo "<div align='center'><br /><br /><br /><b>Error occurred while creating a new location '{$loc}' for 'Via' field. Please try again</b><br />Redirecting back to Create Pool</div>"; 
                                        redirect('pool.php?op=create');
                                    }
                                } else
                                    array_push($via, $loc);
                            }
                        }   
                    }
                    
                    if(count($via) > 0)
                        $via = implode(",",$via);
                    else
                        $via = "";
                    
                    $vehType = $_POST[input_poolForm_vehicle_type];
                    $vehicle = null;
                    if($vehType === "select") {
                        $vehicle = $_POST[input_poolForm_vehicle];
                    } else {
                        $vehicle = $_POST[input_poolForm_vehicle_model];
                        $vehicle = ucwords($vehicle);
                        if(!addVehicle($vehicle)) {
                            echo "<div align='center'><br /><br /><br /><b>Error occurred while adding a new vehicle '{$vehicle}'. Please try again</b><br />Redirecting back to Create Pool</div>"; 
                            redirect('pool.php?op=create');
                        }
                        $vehicle = mysql_fetch_row(DBUtil::executeQuery("SELECT LAST_INSERT_ID()"))[0];
                    }
                    
                    require_once 'DBUtil.php';
                    $query = "INSERT INTO pools (owner,startTime,startFrom,upTo,via,vehicle,occupancy,availability) VALUES('$_SESSION[username]','$_POST[input_poolForm_startTime]','$startFrom','$upTo','$via',$vehicle,$_POST[input_poolForm_availability],$_POST[input_poolForm_availability])";
                    $result = DBUtil::executeQuery($query);
                    if ($result) {
                        echo "<div align='center'><br /><br /><br /><b>Created Pool successfully.</b><br />Redirecting to My Pools</div>"; 
                        redirect('pool.php?op=edit');
                    } else {
                        echo "<div align='center'><br /><br /><br /><b>Error occurred while creating pool. Please try again.</b><br />Redirecting back to Create Pool</div>";
                        redirect('pool.php?op=create');
                    }
                    unset($_POST['input_poolForm_startTime']);
                    unset($_POST);
                } else {
        ?>
                    <br />
                    <div id="location"><a href="profile.php">Home</a> > <a href="pool.php?op=create">Create Pool</a></div>
                    <br />
                    <h1 align="left">Create Pool</h1>
        <?php
                    poolForm(null, "createPool");
                }
            }
            
            function viewPool() {
        ?>
                <br />
                <div id="location"><a href="profile.php">Home</a> > <a href="pool.php?op=view">View Pools</a></div>
                <br />
                <h1 align="left">All Pools</h1>
                <div align="center">
                    <table style="text-align:center;" id="viewPools">
                        <thead>
                            <tr>
                                <th width="100">Owner</th>
                                <th width="100">Start Time</th>
                                <th width="150">From</th>
                                <th width="150">To</th>
                                <th width="250">Via</th> 
                                <th width="100">Vehicle</th>
                                <th width="100">Availability</th>
                            </tr>
                        </thead>
                        <tbody>
        <?php
                            require_once 'DBUtil.php';
                            $query = "SELECT owner,poolId,startTime,startFrom,upTo,via,vehicle,availability FROM pools ORDER BY availability DESC";
                            $result = DBUtil::executeQuery($query);
                            while($result_array = mysql_fetch_row($result))
                            {
                                echo "<tr>";
                                // Get name of user from username
                                $query = "SELECT name FROM users WHERE username = '"."{$result_array[0]}"."'";
                                $owner_result = DBUtil::executeQuery($query);
                                $owner_row = mysql_fetch_row($owner_result);
                                $owner = ($result_array[0]==$_SESSION[username])?"You":$owner_row[0];
                                
                                // Get model of vehicle from id
                                $query = "SELECT model FROM vehicles WHERE vid = ".$result_array[6];
                                $vehicle_result = DBUtil::executeQuery($query);
                                $vehicle = mysql_fetch_row($vehicle_result)[0];
                                
                                $availability = ($result_array[7]>0)?(($result_array[0]==$_SESSION[username])?"<b>{$result_array[7]}</b>":"<a style='color:inherit' href='pool.php?op=join&poolId={$result_array[1]}'><b>{$result_array[7]}</b></a>"):"<p style='color:red'><b>NA</b></p>";
                                
                                echo "<td width=".'"100"'.">{$owner}</td>";
                                echo "<td width=".'"100"'.">{$result_array[2]}</td>";
                                echo "<td width=".'"150"'.">{$result_array[3]}</td>";
                                echo "<td width=".'"150"'.">{$result_array[4]}</td>";
                                echo "<td width=".'"250"'.">{$result_array[5]}</td>";
                                echo "<td width=".'"100"'.">{$vehicle}</td>";
                                echo "<td width=".'"100"'.">".$availability."</td>";
                                echo "</tr>";
                            }
        ?>
                        </tbody>
                    </table>
                    <form id="viewPoolsPager">
                        <img src="stylesheets/images/first.png" class="first"/>
                        <img src="stylesheets/images/prev.png" class="prev"/>
                        <input type="text" class="pagedisplay"/>
                        <img src="stylesheets/images/next.png" class="next"/>
                        <img src="stylesheets/images/last.png" class="last"/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Display 
                        <select class="pagesize">
                            <option value="13">13</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        rows per page
                    </form>
                </div>
        <?php
            }
            
            function joinPool() {
                $poolId = $_GET['poolId'];
                if(isset($_POST['join'])) {
                    require_once 'DBUtil.php';
                    $query = "SELECT availability FROM pools WHERE poolId=".$poolId;
                    $result = DBUtil::executeQuery($query);
                    $result_array = mysql_fetch_row($result);
                    $availability = $result_array[0];
                    if ($availability > 0) {
                        $query = "UPDATE pools SET availability=".($availability-1)." WHERE poolId=".$poolId;
                        if(DBUtil::executeQuery($query)) {
                            $query = "INSERT INTO pools_users_membership(poolId,username) VALUES($poolId,'$_SESSION[username]')";
                            if(DBUtil::executeQuery($query)) {
                                echo "<div align='center'><br /><br /><br /><b>Joined Pool successfully.</b><br />Redirecting back to View Pools</div>"; 
                                redirect('pool.php?op=view');
                                
                            } else {
                                $query = "UPDATE pools SET availability=".$availability." WHERE poolId=".$poolId;
                                DBUtil::executeQuery($query);
                                echo "<div align='center'><br /><br /><br /><b>Error occurred while joining pool. Please try again.</b><br />Redirecting back to View Pools</div>";
                                redirect('pool.php?op=view');
                            }
                        }
                        else {
                            echo "<div align='center'><br /><br /><br /><b>Error occurred while joining pool. Please try again.</b><br />Redirecting back to View Pools</div>"; 
                            redirect('pool.php?op=view');
                        }
                    } else {
                        echo "<div align='center'><br /><br /><br /><b>The pool you requested is no longer available. Try another pool.</b><br />Redirecting back to View Pools</div>"; 
                        redirect('pool.php?op=view');
                    }
                    unset($_POST['join']);
                    unset($_POST);
                } else {
                    require_once 'DBUtil.php';
                    $query = "SELECT owner,poolId,startTime,startFrom,upTo,via,vehicle,availability FROM pools WHERE poolId=".$poolId;
                    $result = DBUtil::executeQuery($query);
                    $result_array = mysql_fetch_row($result);
                    // Check if user is the owner of the pool
                    if ($result_array[0] == $_SESSION[username]) {
                        echo "<div align='center'><br /><br /><br /><b>You cannot join a pool created by you. Try another pool.</b><br />Redirecting back to View Pools</div>"; 
                        redirect('pool.php?op=view');
                    } else {
                        // Check if user is already member of the pool
                        $query = "SELECT poolId FROM pools_users_membership WHERE poolId=".$poolId." AND username='".$_SESSION[username]."'";
                        $result = DBUtil::executeQuery($query);
                        $member_array = mysql_fetch_row($result);
                        if ($member_array) {
                            echo "<div align='center'><br /><br /><br /><b>You are already a member of this pool. Try another pool.</b><br />Redirecting back to View Pools</div>"; 
                            redirect('pool.php?op=view');
                        } else if ($result_array[7] > 0) {
        ?>
                            <br />
                            <div id="location"><a href="profile.php">Home</a> > <a href="pool.php?op=view">View Pools</a> > <a href="pool.php?op=join&poolId=<?php echo $poolId?>">Join Pool</a></div>
                            <br />
                            <h1 align="left">Join Pool</h1>
        <?php
                            poolForm($result_array, "joinPool");
                        } else {
                            echo "<div align='center'><br /><br /><br /><b>The pool you requested is no longer available. Try another pool.</b><br />Redirecting back to View Pools</div>"; 
                            redirect('pool.php?op=view');
                        }
                    }
                    unset($_POST['input_poolForm_startTime']);
                    unset($_POST);
                }
            }
            
            function leavePool() {
                $poolId = $_GET['poolId'];
                if(isset($_POST['leave'])) {
                    require_once 'DBUtil.php';
                    $query = "DELETE FROM pools_users_membership WHERE poolId=".$poolId." AND username='".$_SESSION['username']."'";
                    $result = DBUtil::executeQuery($query);
                    if ($result) {
                        $query = "SELECT availability FROM pools WHERE poolId=".$poolId;
                        $result = DBUtil::executeQuery($query);
                        $result_array = mysql_fetch_row($result);
                        $availability = $result_array[0];
                        $query = "UPDATE pools SET availability=".($availability+1)." WHERE poolId=".$poolId;
                        if(DBUtil::executeQuery($query)) {
                            echo "<div align='center'><br /><br /><br /><b>Left Pool successfully.</b><br />Redirecting back to My Pools</div>";
                            redirect('pool.php?op=edit');
                        } else {
                            echo "<div align='center'><br /><br /><br /><b>Error occurred while leaving pool. Please try again.</b><br />Redirecting back to My Pools</div>";
                            redirect('pool.php?op=edit');
                        }
                    } else {
                        echo "<div align='center'><br /><br /><br /><b>Error occurred while leaving pool. Please try again.</b><br />Redirecting back to My Pools</div>";
                        redirect('pool.php?op=edit');
                    }
                } else {
                    require_once 'DBUtil.php';
                    $query = "SELECT owner,poolId,startTime,startFrom,upTo,via,vehicle,availability FROM pools WHERE poolId=".$poolId;
                    $result = DBUtil::executeQuery($query);
                    $result_array = mysql_fetch_row($result);
                    if ($result_array[0] == $_SESSION[username]) {
                        echo "<div align='center'><br /><br /><br /><b>You cannot leave a pool you created. Instead you need to delete it.</b><br />Redirecting back to My Pools</div>"; 
                        redirect('pool.php?op=edit');
                    } else {
                        // Check if user is already member of the pool
                        $query = "SELECT poolId FROM pools_users_membership WHERE poolId=".$poolId." AND username='".$_SESSION[username]."'";
                        $result = DBUtil::executeQuery($query);
                        $member_array = mysql_fetch_row($result);
                        if (!$member_array) {
                            echo "<div align='center'><br /><br /><br /><b>You are not a member of this pool. Need to join a pool before leaving it.</b><br />Redirecting back to My Pools</div>"; 
                            redirect('pool.php?op=edit');
                        } else {
        ?>
                            <br />
                            <div id="location"><a href="profile.php">Home</a> > <a href="pool.php?op=edit">My Pools</a> > <a href="pool.php?op=leave&poolId=<?php echo $poolId?>">Leave Pool</a></div>
                            <br />
                            <h1 align="left">Leave Pool</h1>
        <?php
                            poolForm($result_array, "leavePool");
                        }
                    }
                }
            }
            
            function endsWith($haystack, $needle) {
                return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
            }
            
            function editPool() {
                if(isset($_GET['poolId'])) {
                    if(isset($_POST['input_poolForm_startTime'])) {
                        $oldAttrs = $_SESSION['poolDetails'];
                        unset($_SESSION['poolDetails']);
                        $defQuery = "UPDATE pools SET ";
                        $query = $defQuery;
                        if(strcmp($_POST['input_poolForm_startTime'], $oldAttrs[2]) != 0) {
                            $query .= "startTime = '$_POST[input_poolForm_startTime]', ";
                        }
                        if(strcmp($_POST['input_poolForm_from'], $oldAttrs[3]) != 0) {
                            $query .= "from = '$_POST[input_poolForm_from]', ";
                        }
                        if(strcmp($_POST['input_poolForm_to'], $oldAttrs[4]) != 0) {
                            $query .= "to = '$_POST[input_poolForm_to]', ";
                        }
                        $newViaArray = $_POST['input_poolForm_via'];
                        $oldViaArray = null;
                        $oldValue = $oldAttrs[5];
                        if($oldValue != null) {
                            $oldViaArray = explode(",",$oldValue);
                        }
                        $isNewEmpty = isEmptyArray($newViaArray);
                        if($isNewEmpty && $oldViaArray != null) {
                            $query .= "via = '', ";
                        } else if((!$isNewEmpty && $oldViaArray == null) || (count(array_diff($newViaArray,$oldViaArray)) > 0)) {
                            $newViaArray = implode(",",$newViaArray);
                            $query .= "via = '$newViaArray', ";
                        }
                        if(strcmp($_POST['input_poolForm_vehicle'], $oldAttrs[6]) != 0) {
                            $query .= "vehicle = '$_POST[input_poolForm_vehicle]', ";
                        }
                        if(strcmp($_POST['input_poolForm_availability'], $oldAttrs[7]) != 0) {
                            $query .= "availability = '$_POST[input_poolForm_availability]'";
                        }
                        if(strcmp($query, $defQuery) != 0) {
                            if(endsWith($query, ", "))
                                $query = substr($query, 0, strlen($query)-strlen(", "));
                                
                            $query .= " WHERE poolId = "."'$_GET[poolId]'";
                            $query .= ";";
                            require_once 'DBUtil.php';
                            $result = DBUtil::executeQuery($query);
                        }
                        unset($_POST['input_poolForm_startTime']);
                        echo "<div align='center'><br /><br /><br /><b>Pool updated successfully.</b><br />Redirecting back to My Pools</div>"; 
                        redirect('pool.php?op=edit');
                    } else {
                        $poolId = $_GET['poolId'];
                        require_once 'DBUtil.php';
                        $query = "SELECT owner,poolId,startTime,startFrom,upTo,via,vehicle,availability FROM pools WHERE poolId=".$poolId;
                        $result = DBUtil::executeQuery($query);
                        $result_array = mysql_fetch_row($result);
                        // Check if user is the owner of the pool
                        if ($result_array[0] != $_SESSION[username]) {
                            echo "<div align='center'><br /><br /><br /><b>You can only edit pools created by you. Try another pool.</b><br />Redirecting back to My Pools</div>"; 
                            redirect('pool.php?op=edit');
                        } else {
                            $_SESSION['poolDetails'] = $result_array;
        ?>
                            <br />
                            <div id="location"><a href="profile.php">Home</a> > <a href="pool.php?op=edit">My Pools</a> > <a href="pool.php?op=edit&poolId=<?php echo $poolId?>">Edit Pool</a></div>
                            <br />
                            <h1 align="left">Edit Pool</h1>
        <?php
                            poolForm($result_array, "editPool");
        ?>
                            <script>
                                var user_details = <?php echo json_encode($result_array); ?>;
                                
                                function fillForm() {
                                    document.getElementById("input_poolForm_startTime").value = user_details[2];
                                    document.getElementById("input_poolForm_from").value = user_details[3];
                                    document.getElementById("input_poolForm_to").value = user_details[4];
                                    $viaArray = user_details[5].split(",");
                                    $i = 0;
                                    $(function() {
                                        $.each($('input[name^="input_poolForm_via[]"]'), function() {        
                                            $(this).val($viaArray[$i++]);
                                            if($i == $viaArray.length)
                                                return;
                                        });
                                    });
                                    
                                    document.getElementById("input_poolForm_vehicle").value = user_details[6];
                                    document.getElementById("input_poolForm_availability").value = user_details[7];
                                }
                                
                                $(document).ready(function(){
                                    $("#reset").click(function(){
                                        fillForm();
                                        $selectedVehicle = document.getElementById('input_poolForm_vehicle');
                                        $selectedOption = $selectedVehicle[$selectedVehicle.selectedIndex];
                                        toggleVehSelectDetails(true, $selectedOption);
                                        document.getElementById("update").disabled = true;
                                        document.getElementById("reset").disabled = true;
                                    });
                                });
                            </script>
        <?php            
                        }
                    }
                } else {
        ?>
                    <br />
                    <div id="location"><a href="profile.php">Home</a> > <a href="pool.php?op=edit">My Pools</a></div>
                    <br />
                    <h1 align="left">My Pools</h1>
                    <br /><br />
                    <div align="center">
                        <form action="">
                            <label><input type="radio" name="myPools" value="poolsStarted" onclick="showPoolsStarted()" checked>Pools owned/started by me</input></label>
                            <label><input type="radio" name="myPools" value="poolsJoined" onclick="showPoolsJoined()">Pools joined by me</input></label>
                        </form>
                        <br />
                        <div id="target"></div>
                    </div>
                    <script>
                        showPoolsStarted();
                    </script>
        <?php
                }
            }
            
            function deletePool() {
                $poolId = $_GET['poolId'];
                if(isset($_POST['delete'])) {
                    require_once 'DBUtil.php';
                    $query = "DELETE FROM pools_users_membership WHERE poolId=".$poolId;
                    $result = DBUtil::executeQuery($query);
                    if ($result) {
                        $query = "DELETE FROM pools WHERE poolId=".$poolId;
                        if(DBUtil::executeQuery($query)) {
                            echo "<div align='center'><br /><br /><br /><b>Deleted Pool successfully.</b><br />Redirecting back to My Pools</div>"; 
                            redirect('pool.php?op=edit');
                        } else {
                            echo "<div align='center'><br /><br /><br /><b>Error occurred while deleting pool. Please try again.</b><br />Redirecting back to My Pools</div>";
                            redirect('pool.php?op=edit');
                        }
                    } else {
                        echo "<div align='center'><br /><br /><br /><b>Error occurred while deleting pool. Please try again.</b><br />Redirecting back to My Pools</div>";
                        redirect('pool.php?op=edit');
                    }
                } else {
                    require_once 'DBUtil.php';
                    $query = "SELECT owner,poolId,startTime,startFrom,upTo,via,vehicle,availability FROM pools WHERE poolId=".$poolId;
                    $result = DBUtil::executeQuery($query);
                    $result_array = mysql_fetch_row($result);
                    // Check if user is the owner of the pool
                    if ($result_array[0] != $_SESSION[username]) {
                        echo "<div align='center'><br /><br /><br /><b>You can only delete pools created by you. Try another pool.</b><br />Redirecting back to My Pools</div>"; 
                        redirect('pool.php?op=edit');
                    } else {
        ?>
                        <br />
                        <div id="location"><a href="profile.php">Home</a> > <a href="pool.php?op=edit">My Pools</a> > <a href="pool.php?op=delete&poolId=<?php echo $poolId?>">Delete Pool</a></div>
                        <br />
                        <h1 align="left">Delete Pool</h1>
        <?php
                        poolForm($result_array, "deletePool");
                    }
                }
            }
            
            function redirect($url) {
                echo '<script type="text/javascript">';
                echo 'setTimeout(function(){window.location.href="'.$url.'"},3000);';
                echo '</script>';
                echo '<noscript>';
                echo '<meta http-equiv="refresh" content="3;url='.$url.'" />';
                echo '</noscript>';
                exit;
            }
            
            function poolForm($result_array, $op) {
        ?>
                <div align="center">
                    <form id="poolForm" name="poolForm" action="pool.php?op=<?php if($op === "joinPool") echo "join&poolId={$result_array[1]}"; else if($op === "leavePool") echo "leave&poolId={$result_array[1]}"; else if($op === "deletePool") echo "delete&poolId={$result_array[1]}"; else if($op === "createPool") echo "create"; else if($op === "editPool") echo "edit&poolId={$result_array[1]}";?>" method="post" onsubmit="return validatePoolForm()" >
                        <table>
                            <tr>
                                <td><label>Start Time</label></td>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                                <td>
                                    <input type="text" name="input_poolForm_startTime" id="input_poolForm_startTime" value="<?php if($op === "createPool") echo ""; else echo $result_array[2]; ?>" placeholder="Scheduled departure time" <?php if($op != "editPool" && $op != "createPool") echo "disabled"; ?>  /> *
                                </td>
                            </tr>
                            <tr></tr><tr></tr><tr></tr>
                            <tr>
                                <td><label>From</label></td>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                                <td>
                                    <input type="text" name="input_poolForm_from" id="input_poolForm_from" value="<?php if($op === "createPool") echo ""; else echo $result_array[3] ?>" placeholder="Starting location" <?php if($op != "editPool" && $op != "createPool") echo "disabled" ?> />
                                    <input type="hidden" name="input_poolForm_from_id" id="input_poolForm_from_id" value="" /> *
                                </td>
                            </tr>
                            <tr></tr><tr></tr><tr></tr>
                            <tr>
                                <td><label>To</label></td>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                                <td>
                                    <input type="text" name="input_poolForm_to" id="input_poolForm_to" value="<?php if($op === "createPool") echo ""; else echo $result_array[4] ?>" placeholder="Destination" <?php if($op != "editPool" && $op != "createPool") echo "disabled" ?> />
                                    <input type="hidden" name="input_poolForm_to_id" id="input_poolForm_to_id" value="" /> *
                                </td>
                            </tr>
                            <tr></tr><tr></tr><tr></tr>
                            <tr>
                                <td><label>Via</label></td>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                                <td>
                                    <div class="multi-field-wrapper">
                                        <div class="multi-fields">
                                            <?php
                                                $viaArray = null;
                                                $size = 0;
                                                $i = 0;
                                                if(!isEmptyString($result_array[5])) {
                                                    $viaArray = explode(",",$result_array[5]);
                                                    $size = count($viaArray);
                                                }
                                                do {
                                                    echo '<div class="multi-field"><input type="text" name="input_poolForm_via[]" id="input_poolForm_via" class="input_poolForm_via" value="'. ((($op === "createPool") || ($size == 0)) ? "" : $viaArray[$i]) .'"  placeholder="Passing through / can stop at" '. (($op != "editPool" && $op != "createPool") ? "disabled" : "") .' /><input type="hidden" name="input_poolForm_via_id[]" id="input_poolForm_via_id"  class="input_poolForm_via_id" value="" />'. (($op != "editPool" && $op != "createPool") ? "" : ' <a title="Remove location" href="#" class="remove-field"><span class="ui-icon ui-icon-closethick" style="display:inline-block;vertical-align:middle;"></span></a>') .'</div>';
                                                    $i++;
                                                } while($i < $size);
                                            ?>  
                                        </div>
                                        <?php
                                            if($op == "editPool" || $op == "createPool") {
                                                echo '<div style="margin-left:150px;"><a style="text-decoration:none;color:inherit" title="Add location" href="#" class="add-field"><span class="ui-icon ui-icon-plusthick" style="display:inline-block;vertical-align:bottom;"></span><b>More</b></a></div>';
                                            }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr></tr><tr></tr><tr></tr>
                            <tr>
                                <td><label>Vehicle</label></td>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                                <td>
                                    <input type="hidden" name="input_poolForm_vehicle_type" id="input_poolForm_vehicle_type" value="" />
                                    <?php
                                        $vehRegDispStyle = "display:none";
                                        require_once 'DBUtil.php';
                                        $query = "SELECT * FROM vehicles WHERE uid='".$_SESSION[username]."' ORDER BY model";
                                        $result = DBUtil::executeQuery($query);
                                        $veh_array = null;
                                        if($op === "joinPool" || $op === "leavePool" || $veh_array = mysql_fetch_row($result)) {
                                            if(!$veh_array) {
                                                require_once 'DBUtil.php';
                                                $query = "SELECT * FROM vehicles WHERE vid='".$result_array[6]."'";
                                                $result = DBUtil::executeQuery($query);
                                                $veh_array = mysql_fetch_row($result);
                                            }
                                    ?>
                                            <script>setVehType("select");</script>
                                            <div id="vehSelect">
                                            <select type="text" name="input_poolForm_vehicle" value="<?php if($op === "createPool") echo ""; else echo $result_array[6] ?>" id="input_poolForm_vehicle" onchange="selectVehicle(this)" <?php if($op != "editPool" && $op != "createPool") echo "hidden" ?> style="text-align:left;">
                                            <option value="" <?php if($op === "createPool") echo "selected"; else echo ""; ?> disabled>Choose your vehicle</option>
                                    <?php        
                                            do {
                                                echo '<option value="'.$veh_array[0].'" regNo="'.$veh_array[3].'" color="'.$veh_array[4].'" occupancy="'.$veh_array[5].'">'.$veh_array[2].'</option>';
                                            } while($veh_array = mysql_fetch_row($result));
                                    ?>
                                            </select> <?php if($op == "editPool" || $op == "createPool") echo "*" ?>
                                            
                                    <?php
                                            if($op == "createPool") {
                                                echo '<div style="margin-left:153px;"><a style="text-decoration:none;color:inherit" title="Add new vehicle" href="#" class="add-field" onclick="showVehReg()"><span class="ui-icon ui-icon-plusthick" style="display:inline-block;vertical-align:bottom;"></span><b>New</b></a></div></div>';
                                            }
                                        } else {
                                            echo "<b>You have not registered any<br />vehicles yet. Please provide the<br />below details to register a vehicle<br />and automatically add to this pool<br /></b>";
                                            $vehRegDispStyle = "display:block";
                                            echo '<script>setVehType("registration")</script>';
                                        }
                                    ?>
                                    <div id="vehReg" style=<?php echo $vehRegDispStyle ?> >
                                        <table>
                                            <tr>
                                                <td><label>Model</label></td>
                                                <td>&nbsp;&nbsp;<input type="text" name="input_poolForm_vehicle_model" id="input_poolForm_vehicle_model" value="" placeholder="Model" /> *</td>
                                            </tr>
                                            <tr>
                                                <td><label>Color</label></td>
                                                <td>&nbsp;&nbsp;<input type="text" name="input_poolForm_vehicle_color" id="input_poolForm_vehicle_color" value="" placeholder="Color" /> *</td>
                                            </tr>
                                            <tr>
                                                <td><label>Reg #</label></td>
                                                <td>&nbsp;&nbsp;<input type="text" name="input_poolForm_vehicle_regNo" id="input_poolForm_vehicle_regNo" value="" placeholder="Registration Number" /> *</td>
                                            </tr>
                                            <tr>
                                                <td><label>Space</label></td>
                                                <td>&nbsp;&nbsp;<input type="text" name="input_poolForm_vehicle_occupancy" id="input_poolForm_vehicle_occupancy" value="" placeholder="Occupancy (Excluding driver)" onchange="copyDefOccupancy(this)" onkeyup="copyDefOccupancy(this)" /> *</td>
                                            </tr>
                                        </table>
                                        <?php
                                            if($vehRegDispStyle != "display:block") {
                                                echo '<div id="selVehButton" style="margin-left:193px;"><a style="text-decoration:none;color:inherit" title="Select existing vehicle" href="#" class="add-field" onclick="showVehSelect()"><span class="ui-icon ui-icon-arrowreturnthick-1-w" style="display:inline-block;vertical-align:bottom;"></span><b>Select</b></a></div>';
                                            }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr></tr><tr></tr><tr></tr>
                            <tr>
                                <td><label><?php if($op === "createPool") echo "Occupancy"; else echo "Availability"; ?></label></td>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                                <td>
                                    <input type="text" name="input_poolForm_availability" id="input_poolForm_availability" value="<?php if($op === "createPool") echo ""; else echo $result_array[7] ?>" placeholder="Can take (not including you)" <?php if($op != "editPool" && $op != "createPool") echo "disabled" ?> /> *
                                </td>
                            </tr>
                            <tr></tr><tr></tr><tr></tr>
                        </table>
                        <br />
                        <div align="center" id="buttons">
                            <?php
                                if($op === "createPool")
                                    echo '<input type="submit" name="create" id="create" value="Create" />&nbsp;&nbsp;&nbsp;
                                    <input type="reset" name="resetCreateForm" id="resetCreateForm" value="Reset"/>';
                                else if($op === "joinPool")
                                    echo '<input type="submit" name="join" id="join" value="Join"/>&nbsp;&nbsp;&nbsp;
                                    <a href="pool.php?op=view"><input type="button" name="cancel" id="cancel" value="Cancel"/></a>';
                                else if($op === "leavePool")
                                    echo '<input type="submit" name="leave" id="leave" value="Leave"/>&nbsp;&nbsp;&nbsp;
                                    <a href="pool.php?op=edit"><input type="button" name="cancel" id="cancel" value="Cancel"/></a>';
                                else if($op === "deletePool")
                                    echo '<input type="submit" name="delete" id="delete" value="Delete"/>&nbsp;&nbsp;&nbsp;
                                    <a href="pool.php?op=edit"><input type="button" name="cancel" id="cancel" value="Cancel"/></a>';
                                else if($op === "editPool")
                                    echo '<input type="submit" name="update" id="update" value="Update" disabled=""/>&nbsp;&nbsp;&nbsp;
                                    <input type="reset" name="reset" id="reset" value="Reset" disabled=""/>&nbsp;&nbsp;&nbsp;
                                    <a href="pool.php?op=edit"><input type="button" name="cancel" id="cancel" value="Cancel"/></a>';
                                    
                                if($op != "createPool") {
                            ?>
                                    <script>
                                        document.getElementById('input_poolForm_vehicle').value = <?php echo $result_array[6]; ?>;
                                        $selectedVehicle = document.getElementById('input_poolForm_vehicle');
                                        $selectedOption = $selectedVehicle[$selectedVehicle.selectedIndex];
                                        toggleVehSelectDetails(true, $selectedOption);
                                    </script>
                            <?php
                                }
                            ?>
                        </div>
                    </form>
                </div>
                <script>
                    $("#input_poolForm_from").focus(function(){
                        if(this.value == "")
                            this.placeholder="Start typing for suggestions";
                    }).blur(function(){
                        if(this.value == "")
                            this.placeholder="Starting location";
                    });
                    
                    $("#input_poolForm_to").focus(function(){
                        if(this.value == "")
                            this.placeholder="Start typing for suggestions";
                    }).blur(function(){
                        if(this.value == "")
                            this.placeholder="Destination";
                    });
                    
                    $('.multi-field').livequery(function() {
                        $(this).children('.input_poolForm_via').focus(function(){
                            if(this.value == "")
                                this.placeholder="Start typing for suggestions";
                        }).blur(function(){
                            if(this.value == "")
                                this.placeholder="Passing through / can stop at";
                        })
                    });
                    
                    $("#input_poolForm_vehicle_model").focus(function(){
                        if(this.value == "")
                            this.placeholder="Start typing for suggestions";
                    }).blur(function(){
                        if(this.value == "")
                            this.placeholder="Model";
                    });
                    
                    Array.prototype.contains = function(obj) {
                        var i = this.length;
                        if (i == 0 && obj.length == 0)
                            return true;
                        while (i--) {
                            if (this[i] === obj) {
                                return true;
                            }
                        }
                        return false;
                    }
                    
                    Array.prototype.compare = function(rightArray) {
                        var leftArray = this.sort();
                        rightArray = rightArray.sort();
                        if (leftArray.length != rightArray.length) return false;
                        if (leftArray.length == 0 && rightArray.length == 0) return true;
                        for (var i = 0; i < rightArray.length; i++) {
                            if (leftArray[i].compare) { 
                                if (!leftArray[i].compare(rightArray[i])) return false;
                            }
                            if (leftArray[i] !== rightArray[i]) return false;
                        }
                        return true;
                    }
                    
                    function getAllVia() {
                        $inputs = document.getElementsByClassName( 'input_poolForm_via' );
                        $allVia = [];
                        for($i=0; $i<$inputs.length; $i++) {
                            $value = $inputs[$i].value;
                            if(!isEmptyString($value))
                                $allVia.push($value);
                        }
                        return $allVia;
                    }
                    
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
                    var origVia = [];
                    var newVia = [];
                    $("form :input").each(function () {
                        if($(this).attr('id') == 'input_poolForm_from_id'
                            || $(this).attr('id') == 'input_poolForm_to_id'
                            || $(this).attr('id') == 'input_poolForm_via_id')
                            return;
                        var type = $(this).getType();
                        var tmp = {'type': type, 'value': $(this).val()};
                        if (type == 'radio') { tmp.checked = $(this).is(':checked'); }
                        if ($(this).attr('id') == 'input_poolForm_via') {
                            $value = $(this).val();
                            if(!isEmptyString($value))
                                origVia.push($value);
                        }
                        else
                            orig[$(this).attr('id')] = tmp;
                    });
                    
                    // Check values on change
                    $('form').bind('keyup change', function () {
                        var disable = true;
                        $("form :input").each(function () {
                            if($(this).attr('id') == 'input_poolForm_from_id'
                            || $(this).attr('id') == 'input_poolForm_to_id'
                            || $(this).attr('id') == 'input_poolForm_via_id')
                                return;
                            
                            var type = $(this).getType();
                            var id = $(this).attr('id');
                            if($(this).attr('id') == 'input_poolForm_via') {
                                disable = origVia.contains($(this).val());
                                if(!disable)
                                    disable = origVia.compare(getAllVia());
                            } else if (type == 'text' || type == 'select') {
                                disable = (orig[id].value == $(this).val());
                            } else if (type == 'radio') {
                                disable = (orig[id].checked == $(this).is(':checked'));
                            }    
                            if (!disable) { return false;} // break out of loop
                        });
                        $('#update').prop('disabled', disable); // update disabled property for update button
                        $('#reset').prop('disabled', disable); // update disabled property for reset button
                    });
                    
                    $locCache = {};
                    $("#input_poolForm_from").autocomplete({
                        minLength:0,
                        source: function( request, response ) {
                            var term = request.term;
                            if ( term in $locCache ) {
                                response( $locCache[ term ] );
                                return;
                            }
                     
                            $.getJSON( "searchSuggest.php?table=locations&labelCol=name&valueCol=id", request, function( data, status, xhr ) {
                                $locCache[ term ] = data;
                                response( data );
                            });
                        },
                        focus: function( event, ui ) {
                            $(this).val( ui.item.label );
                            return false;
                        },
                        select: function( event, ui ) {
                            $(this).val( ui.item.label );
                            $("#input_poolForm_from_id").val( ui.item.value );
                            return false;
                        }
                    });
                    
                    $("#input_poolForm_to").autocomplete({
                        source: function( request, response ) {
                            var term = request.term;
                            if ( term in $locCache ) {
                                response( $locCache[ term ] );
                                return;
                            }
                     
                            $.getJSON( "searchSuggest.php?table=locations&labelCol=name&valueCol=id", request, function( data, status, xhr ) {
                                $locCache[ term ] = data;
                                response( data );
                            });
                        },
                        focus: function( event, ui ) {
                            $(this).val( ui.item.label );
                            return false;
                        },
                        select: function( event, ui ) {
                            $(this).val( ui.item.label );
                            $("#input_poolForm_to_id").val( ui.item.value );
                            return false;
                        }
                    });
                    
                    $('.multi-field').livequery(function() {
                        $(this).children('.input_poolForm_via').autocomplete({
                            source: function( request, response ) {
                                var term = request.term;
                                if ( term in $locCache ) {
                                    response( $locCache[ term ] );
                                    return;
                                }
                         
                                $.getJSON( "searchSuggest.php?table=locations&labelCol=name&valueCol=id", request, function( data, status, xhr ) {
                                    $locCache[ term ] = data;
                                    response( data );
                                });
                            },
                            focus: function( event, ui ) {
                                $(this).val( ui.item.label );
                                return false;
                            },
                            select: function( event, ui ) {
                                $(this).val( ui.item.label );
                                $(this).parent('div').children('.input_poolForm_via_id').val( ui.item.value );
                                return false;
                            }
                        });
                    });
                    
                    $vehCache = {};
                    $("#input_poolForm_vehicle_model").autocomplete({
                        minLength:0,
                        source: function( request, response ) {
                            var term = request.term;
                            if ( term in $vehCache ) {
                                response( $vehCache[ term ] );
                                return;
                            }
                     
                            $.getJSON( "searchSuggest.php?table=vehicles&labelCol=model", request, function( data, status, xhr ) {
                                $vehCache[ term ] = data;
                                response( data );
                            });
                        },
                        focus: function( event, ui ) {
                            $(this).val( ui.item.label );
                            return false;
                        },
                        select: function( event, ui ) {
                            $(this).val( ui.item.label );
                            return false;
                        }
                    });
                    
                    function isEmptyString($string) {
                        if($string && $string != null && $string != "") {
                            return false;
                        } else
                            return true;
                    }
            
                    $('.multi-field-wrapper').each(function() {
                        var $wrapper = $('.multi-fields', this);
                        $(".add-field", $(this)).click(function(e) {
                            $($wrapper).append('<div class="multi-field"><input type="text" name="input_poolForm_via[]" id="input_poolForm_via" class="input_poolForm_via" value="" placeholder="Passing through / can stop at" /><input type="hidden" name="input_poolForm_via_id[]" id="input_poolForm_via_id" class="input_poolForm_via_id" value="" /> <a title="Remove location" href="#" class="remove-field"><span class="ui-icon ui-icon-closethick" style="display:inline-block;vertical-align:middle;"></span></a></div>').find('input').focus();
                        });
                        $($wrapper).on("click",".remove-field", function(e){ //user click on remove text
                            e.preventDefault();
                            if ($('.multi-field', $wrapper).length > 1) {
                                $(this).parent('div').remove();
                            } else {
                                $(this).parent('div').children('.input_poolForm_via').val('');
                            }
                            var disable = origVia.compare(getAllVia());
                            $('#update').prop('disabled', disable); // update disabled property for update button
                            $('#reset').prop('disabled', disable); // update disabled property for reset button};
                        });
                    });
                    
                    function selectVehicle(selectedVehicle) {
                        $selectedOption = selectedVehicle[selectedVehicle.selectedIndex];
                        toggleVehSelectDetails(true, $selectedOption);
                        copyDefOccupancyFromDB(selectedVehicle);
                    }
                    
                    function copyDefOccupancyFromDB(selectedVehicle) {
                        $occupancy = document.getElementById("input_poolForm_availability");
                        if($occupancy.value == "")
                            $occupancy.value = selectedVehicle[selectedVehicle.selectedIndex].getAttribute("occupancy");
                    }
                    
                    function copyDefOccupancy(enteredOccupancy) {
                        document.getElementById('input_poolForm_availability').value = enteredOccupancy.value;
                    }
                </script>
        <?php
            }
        ?>
    </body>
</html>