<?php
    session_start();
    if(!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }
?>
<?php
    //Get our database abstraction file
    require('DBUtil.php');
    //Make sure that a value was sent.
    if (isset($_GET['term']) && $_GET['term'] != '') {
        //Add slashes to any quotes to avoid SQL problems.
        $term = addslashes($_GET['term']);
        $table = $_GET[table];
        $labelCol = $_GET[labelCol];
        $valueCol = $_GET[valueCol];
        $suggestions = array();
        if($valueCol)
            $suggest_query = DBUtil::executeQuery("SELECT ". $labelCol . "," . $valueCol ." FROM ". $table . " WHERE ". $labelCol ." LIKE('%" . $term . "%') ORDER BY ". $labelCol);
        else
            $suggest_query = DBUtil::executeQuery("SELECT DISTINCT ". $labelCol ." FROM ". $table . " WHERE ". $labelCol ." LIKE('%" . $term . "%') ORDER BY ". $labelCol);
        while($suggest = mysql_fetch_row($suggest_query)) {
            array_push($suggestions, array("label" => $suggest[0], "value" => $suggest[1]));
        }
        echo json_encode($suggestions);
    }
?>