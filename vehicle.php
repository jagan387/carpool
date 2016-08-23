<?php
    include("header.php");
?>
<html>
    <head>
        <title>CarVehicle</title>
        <link href="stylesheets/public.css" media="all" rel="stylesheet" type="text/css" />
        <link href="stylesheets/jquery.tablesorter.pager.css" media="all" rel="stylesheet" type="text/css" />
        <link href="stylesheets/jquery.tablesorter.css" media="all" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="javascript/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="javascript/jquery-migrate-1.2.1.min.js"></script> <!-- Need this for jquery 1.9+ so that old apps/features are supported -->
        <script type="text/javascript" src="javascript/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="javascript/jquery.tablesorter.pager.js"></script>
		<script type="text/javascript" src="javascript/jquery-ui.min.js"></script>
		<script type="text/javascript" src="javascript/scripts.js"></script>
        <script>
            $(document).ready(function() { 
                $("#viewVehicles").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#viewVehiclesPager"), size:5});
            });
        </script>
    </head>
    <body>
        <?php
            $menuToSelect = null;
            switch($_GET['op']) {
                case 'create':              $menuToSelect = "reg";
                                            createVehicle();
                                            break;
                case 'view':                $menuToSelect = "my";
                                            viewVehicle();
                                            break;
                case 'edit':                $menuToSelect = "my";
                                            editVehicle();
                                            break;
                case 'delete':              $menuToSelect = "my";
                                            deleteVehicle();
                                            break;
            }
            
            echo "<style>#header #".$menuToSelect."VehicleMenu {color:#fff;background:#939393;}</style>";
            
            function createVehicle() {
                if(isset($_POST['input_vehicleForm_model'])) {
                    require_once 'DBUtil.php';
                    $query = "INSERT INTO vehicles (uid,model,color,regNo,occupancy) VALUES ('".$_SESSION[username]."','".ucwords($_POST[input_vehicleForm_model])."','".$_POST[input_vehicleForm_color]."','".$_POST[input_vehicleForm_regNo]."',".$_POST[input_vehicleForm_occupancy].")";
                    $result = DBUtil::executeQuery($query);
                    if ($result) {
                        echo "<div align='center'><br /><br /><br /><b>Registered Vehicle successfully.</b><br />Redirecting to My Vehicles</div>"; 
                        redirect('vehicle.php?op=view');
                    } else {
                        echo "<div align='center'><br /><br /><br /><b>Error occurred while registering vehicle. Please try again.</b><br />Redirecting back to Register Vehicle</div>";
                        redirect('vehicle.php?op=create');
                    }
                    unset($_POST['input_vehicleForm_model']);
                    unset($_POST);
                } else {
        ?>
                    <br />
                    <div id="location"><a href="profile.php">Home</a> > <a href="vehicle.php?op=create">Register Vehicle</a></div>
                    <br />
                    <h1 align="left">Register Vehicle</h1>
        <?php
                    vehicleForm(null, "createVehicle");
                }
            }
            
            function viewVehicle() {
        ?>
                <br />
                <div id="location"><a href="profile.php">Home</a> > <a href="vehicle.php?op=view">My Vehicles</a></div>
                <br />
                <h1 align="left">My Vehicles</h1>
                <div align="center">
        <?php
                    require_once 'DBUtil.php';
                    $query = "SELECT vid,model,color,regNo,occupancy FROM vehicles WHERE uid='".$_SESSION[username]."'  ORDER BY model";
                    $result = DBUtil::executeQuery($query);
                    if(mysql_num_rows($result) === 0) {
                        echo "No vehicles have been registered by you";
                    } else {
        ?>
                        <table style="text-align:center;" id="viewVehicles">
                            <thead>
                                <tr>
                                    <th width="150">Model</th>
                                    <th width="150">Color</th>
                                    <th width="150">Registration No</th>
                                    <th width="100">Occupancy</th>
                                </tr>
                            </thead>
                            <tbody>
        <?php
                                while($result_array = mysql_fetch_row($result))
                                {
                                    echo "<tr>";
                                    echo "<td width=".'"150"'.">{$result_array[1]}</td>";
                                    echo "<td width=".'"150"'.">{$result_array[2]}</td>";
                                    echo "<td width=".'"150"'.">{$result_array[3]}</td>";
                                    echo "<td width=".'"100"'.">{$result_array[4]}</td>";
                                    echo "<td width=".'"50"'.">
                                            <a title='Edit' href='vehicle.php?op=edit&vehicleId={$result_array[0]}'>
                                                <span class='ui-icon ui-icon-pencil' style='display:inline-block;'></span>
                                            </a>
                                            &nbsp;
                                            <a title='Delete' href='vehicle.php?op=delete&vehicleId={$result_array[0]}' class='img-link'>
                                                <span class='ui-icon ui-icon-trash' style='display:inline-block;'></span>
                                            </a>
                                          </td>";
                                    echo "</tr>";
                                }
        ?>
                            </tbody>
                        </table>
                        <form id="viewVehiclesPager">
                            <img src="stylesheets/images/first.png" class="first"/>
                            <img src="stylesheets/images/prev.png" class="prev"/>
                            <input type="text" class="pagedisplay"/>
                            <img src="stylesheets/images/next.png" class="next"/>
                            <img src="stylesheets/images/last.png" class="last"/>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            Display 
                            <select class="pagesize">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                            </select>
                            rows per page
                        </form>
        <?php
                    }
        ?>
                </div>
        <?php
            }
            
            function endsWith($haystack, $needle) {
                return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
            }
            
            function editVehicle() {
                if(isset($_POST['input_vehicleForm_model'])) {
                    $oldAttrs = $_SESSION['vehicleDetails'];
                    unset($_SESSION['vehicleDetails']);
                    $defQuery = "UPDATE vehicles SET ";
                    $query = $defQuery;
                    if(strcmp($_POST['input_vehicleForm_model'], $oldAttrs[1]) != 0) {
                        $query .= "model = '$_POST[input_vehicleForm_model]', ";
                    }
                    if(strcmp($_POST['input_vehicleForm_color'], $oldAttrs[2]) != 0) {
                        $query .= "color = '$_POST[input_vehicleForm_color]', ";
                    }
                    if(strcmp($_POST['input_vehicleForm_regNo'], $oldAttrs[3]) != 0) {
                        $query .= "regNo = '$_POST[input_vehicleForm_regNo]', ";
                    }
                    if(strcmp($_POST['input_vehicleForm_occupancy'], $oldAttrs[4]) != 0) {
                        $query .= "occupancy = '$_POST[input_vehicleForm_occupancy]'";
                    }
                    if(strcmp($query, $defQuery) != 0) {
                        if(endsWith($query, ", "))
                            $query = substr($query, 0, strlen($query)-strlen(", "));
                        $query .= " WHERE vid = ".$_GET[vehicleId];
                        $query .= ";";
                        require_once 'DBUtil.php';
                        $result = DBUtil::executeQuery($query);
                    }
                    unset($_POST['input_vehicleForm_model']);
                    echo "<div align='center'><br /><br /><br /><b>Vehicle updated successfully.</b><br />Redirecting back to My Vehicles</div>"; 
                    redirect('vehicle.php?op=view');
                } else {
                    $vehicleId = $_GET['vehicleId'];
                    require_once 'DBUtil.php';
                    $query = "SELECT vid,model,color,regNo,occupancy FROM vehicles WHERE vid=".$vehicleId;
                    $result = DBUtil::executeQuery($query);
                    $result_array = mysql_fetch_row($result);
                    $_SESSION['vehicleDetails'] = $result_array;
        ?>
                    <br />
                    <div id="location"><a href="profile.php">Home</a> > <a href="vehicle.php?op=view">My Vehicles</a> > <a href="vehicle.php?op=edit&vehicleId=<?php echo $vehicleId?>">Edit Vehicle</a></div>
                    <br />
                    <h1 align="left">Edit Vehicle</h1>
        <?php
                    vehicleForm($result_array, "editVehicle");
        ?>
                    <script>
                        var user_details = <?php echo json_encode($result_array); ?>;
                        
                        function fillForm() {
                            document.getElementById("input_vehicleForm_model").value = user_details[1];
                            document.getElementById("input_vehicleForm_color").value = user_details[2];
                            document.getElementById("input_vehicleForm_regNo").value = user_details[3];
                            document.getElementById("input_vehicleForm_occupancy").value = user_details[4];
                        }
                        
                        function reset() {
                            fillForm();
                            document.getElementById("update").disabled = true;
                            document.getElementById("reset").disabled = true;
                        }
                    </script>
        <?php
                }
            }
            
            function deleteVehicle() {
                $vehicleId = $_GET['vehicleId'];
                if(isset($_POST['delete'])) {
                    require_once 'DBUtil.php';
                    $query = "SELECT * FROM pools WHERE vehicle=".$vehicleId;
                    $result = DBUtil::executeQuery($query);
                    if (mysql_num_rows($result) == 0) {
                        $query = "DELETE FROM vehicles WHERE vid=".$vehicleId;
                        if(DBUtil::executeQuery($query)) {
                            echo "<div align='center'><br /><br /><br /><b>Deleted Vehicle successfully.</b><br />Redirecting back to My Vehicles</div>"; 
                            redirect('vehicle.php?op=view');
                        } else {
                            echo "<div align='center'><br /><br /><br /><b>Error occurred while deleting vehicle. Please try again.</b><br />Redirecting back to My Vehicles</div>";
                            redirect('vehicle.php?op=view');
                        }
                    } else {
                        echo "<div align='center'><br /><br /><br /><b>This vehicle is associated with 1 or more pools. Please delete all associated pools before deleting this vehicle.</b><br />Redirecting back to My Vehicles</div>";
                        redirect('vehicle.php?op=view');
                    }
                } else {
                    require_once 'DBUtil.php';
                    $query = "SELECT vid,model,color,regNo,occupancy FROM vehicles WHERE vid=".$vehicleId;
                    $result = DBUtil::executeQuery($query);
                    $result_array = mysql_fetch_row($result);
        ?>
                    <br />
                    <div id="location"><a href="profile.php">Home</a> > <a href="vehicle.php?op=view">My Vehicles</a> > <a href="vehicle.php?op=delete&vehicleId=<?php echo $vehicleId?>">Delete Vehicle</a></div>
                    <br />
                    <h1 align="left">Delete Vehicle</h1>
        <?php
                    vehicleForm($result_array, "deleteVehicle");
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
            
            function vehicleForm($result_array, $op) {
        ?>
                <div align="center">
                    <form id="vehicleForm" name="vehicleForm" action="vehicle.php?op=<?php if($op === "createVehicle") echo "create"; else if($op === "editVehicle") echo "edit&vehicleId={$result_array[0]}"; else if($op === "deleteVehicle") echo "delete&vehicleId={$result_array[0]}"; else ?>" method="post" onsubmit="return validateVehicleForm()" >
                        <table>
                            <tr>
                                <td><label>Model</label></td>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                                <td>
                                    <input type="text" name="input_vehicleForm_model" id="input_vehicleForm_model" value="<?php if($op === "createVehicle") echo ""; else echo $result_array[1]; ?>" placeholder="Vehicle Model (ex. Innova)" <?php if($op != "editVehicle" && $op != "createVehicle") echo "disabled"; ?>  /> *
                                </td>
                            </tr>
                            <tr></tr><tr></tr><tr></tr>
                            <tr>
                                <td><label>Color</label></td>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                                <td>
                                    <input type="text" name="input_vehicleForm_color" id="input_vehicleForm_color" value="<?php if($op === "createVehicle") echo ""; else echo $result_array[2] ?>" placeholder="Vehicle Color (ex. Black)" <?php if($op != "editVehicle" && $op != "createVehicle") echo "disabled" ?> /> *
                                </td>
                            </tr>
                            <tr></tr><tr></tr><tr></tr>
                            <tr>
                                <td><label>Registration No</label></td>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                                <td>
                                    <input type="text" name="input_vehicleForm_regNo" id="input_vehicleForm_regNo" value="<?php if($op === "createVehicle") echo ""; else echo $result_array[3] ?>" placeholder="Reg # (ex. AP 30 UJ 8055)" <?php if($op != "editVehicle" && $op != "createVehicle") echo "disabled" ?> /> *
                                </td>
                            </tr>
                            <tr></tr><tr></tr><tr></tr>
                            <tr>
                                <td><label>Occupancy</label></td>
                                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                                <td>
                                    <input type="text" name="input_vehicleForm_occupancy" id="input_vehicleForm_occupancy" value="<?php if($op === "createVehicle") echo ""; else echo $result_array[4] ?>" placeholder="Occupancy (Excluding driver)" <?php if($op != "editVehicle" && $op != "createVehicle") echo "disabled" ?> /> *
                                </td>
                            </tr>
                            <tr></tr><tr></tr><tr></tr>
                        </table>
                        <br />
                        <div align="center" id="buttons">
                            <?php
                                if($op === "createVehicle")
                                    echo '<input type="submit" name="create" id="create" value="Register" />&nbsp;&nbsp;&nbsp;
                                    <input type="reset" name="resetCreateForm" id="resetCreateForm" value="Reset"/>';
                                else if($op === "editVehicle")
                                    echo '<input type="submit" name="update" id="update" value="Update" disabled=""/>&nbsp;&nbsp;&nbsp;
                                    <input type="reset" name="reset" id="reset" value="Reset" disabled="" onclick="reset()"/>&nbsp;&nbsp;&nbsp;
                                    <a href="vehicle.php?op=view"><input type="button" name="cancel" id="cancel" value="Cancel"/></a>';
                                else if($op === "deleteVehicle")
                                    echo '<input type="submit" name="delete" id="delete" value="Delete"/>&nbsp;&nbsp;&nbsp;
                                    <a href="vehicle.php?op=view"><input type="button" name="cancel" id="cancel" value="Cancel"/></a>';
                            ?>
                        </div>
                    </form>
                </div>
                <script>
                    $("#input_vehicleForm_model").focus(function(){
                        if(this.value == "")
                            this.placeholder="Start typing for suggestions";
                    }).blur(function(){
                        if(this.value == "")
                            this.placeholder="Vehicle Model (ex. Innova)";
                    });
                    
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
                    $("#vehicleForm :input").each(function () {
                        var type = $(this).getType();
                        var tmp = {'type': type, 'value': $(this).val()};
                        if (type == 'radio') { tmp.checked = $(this).is(':checked'); }
                        orig[$(this).attr('id')] = tmp;
                    });
                    
                    // Check values on change
                    $('#vehicleForm').bind('keyup change', function () {
                        var disable = true;
                        $("#vehicleForm :input").each(function () {
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
                    
                    $vehCache = {};
                    $("#input_vehicleForm_model").autocomplete({
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
                </script>
        <?php
            }
        ?>
    </body>
</html>