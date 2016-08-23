<?php
    session_start();
    if(!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }
?>
<html>
    <head>
        <title>CarPool</title>
        <link href="stylesheets/public.css" media="all" rel="stylesheet" type="text/css" />
        <link href="stylesheets/jquery-ui.css" media="all" rel="stylesheet" type="text/css" />
		<link href="stylesheets/jquery.tablesorter.pager.css" media="all" rel="stylesheet" type="text/css" />
        <link href="stylesheets/jquery.tablesorter.css" media="all" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="javascript/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="javascript/jquery-migrate-1.2.1.min.js"></script> <!-- Need this for jquery 1.9+ so that old apps/features are supported -->
        <script type="text/javascript" src="javascript/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="javascript/jquery.tablesorter.pager.js"></script>
		<script type="text/javascript" src="javascript/jquery-ui.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#joinedPools").tablesorter({widthFixed: true, widgets: ['zebra']}).tablesorterPager({container: $("#joinedPoolsPager"), size:5});
            });
        </script>
    </head>
    <body>
        <div>
            <h1 align="left">Pools joined by me</h1>
        <?php
            require_once 'DBUtil.php';
            $query = "SELECT pools.owner,pools.poolId,startTime,startFrom,upTo,via,vehicle,availability FROM pools,pools_users_membership WHERE pools.poolId=pools_users_membership.poolId AND pools_users_membership.username='".$_SESSION['username']."' ORDER BY availability DESC";
            $result = DBUtil::executeQuery($query);
            if(mysql_num_rows($result) === 0) {
                echo "None";
            } else {
        ?>
                <table style="text-align:center;" id="joinedPools">
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
                        while($result_array = mysql_fetch_row($result))
                        {
                            echo "<tr>";
                            // Get name of user from username
                            $query = "SELECT name FROM users WHERE username = '"."{$result_array[0]}"."'";
                            $owner_result = DBUtil::executeQuery($query);
                            $owner = mysql_fetch_row($owner_result)[0];
                            
                            // Get model of vehicle from id
                            $query = "SELECT model FROM vehicles WHERE vid = ".$result_array[6];
                            $vehicle_result = DBUtil::executeQuery($query);
                            $vehicle = mysql_fetch_row($vehicle_result)[0];
                            
                            $availability = ($result_array[7]>0)?"{$result_array[7]}":"<p style='color:red'><b>NA</b></p>";
                            echo "<td width=".'"100"'.">{$owner}</td>";
                            echo "<td width=".'"100"'.">{$result_array[2]}</td>";
                            echo "<td width=".'"150"'.">{$result_array[3]}</td>";
                            echo "<td width=".'"150"'.">{$result_array[4]}</td>";
                            echo "<td width=".'"250"'.">{$result_array[5]}</td>";
                            echo "<td width=".'"100"'.">{$vehicle}</td>";
                            echo "<td width=".'"100"'.">".$availability."</td>";
                            echo "<td width=".'"20"'.">
                                    <a title='Leave Pool' href='pool.php?op=leave&poolId={$result_array[1]}' class='img-link'>
                                        <span class='ui-icon ui-icon-cancel' style='display:inline-block;'></span>
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
        ?>
                    </tbody>
                </table>
                
                <form id="joinedPoolsPager">
                    <img src="stylesheets/images/first.png" class="first"/>
                    <img src="stylesheets/images/prev.png" class="prev"/>
                    <input type="text" class="pagedisplay"/>
                    <img src="stylesheets/images/next.png" class="next"/>
                    <img src="stylesheets/images/last.png" class="last"/>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    Display 
                    <select class="pagesize">
                        <option value="5">5</option>
                        <option value="13">13</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    rows per page
                </form>        
        <?php
            }
        ?>
        </div>
    </body>
</html>