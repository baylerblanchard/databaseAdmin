<?php  // Bayler Blanchard Assignment 4
include 'connect.php';
$player_id = isset($_POST['player_id']) ? $_POST['player_id'] : '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Insert Player Result</title>
</head>
<body>
    <h1>Insert Player Result</h1>
    <?php

    if (empty($player_id)) {
        echo "<p style='color:red;'>Please enter a Player ID.</p>";
    } else {
        $sql = "CALL proc_insert_player($1, $2)";
        $params = array($player_id, null);

        $result = pg_query_params($dbconn, $sql, $params);

        if ($result) {
            $row = pg_fetch_row($result);
            $errlvl = $row[1]; 

            if ($errlvl == 0) { //added colors for error messages
                echo "<p style='color:green;'>Player ID '" . htmlspecialchars($player_id) . "' inserted successfully.</p>";
            } elseif ($errlvl == 1) {
                echo "<p style='color:red;'>Error: Player ID cannot be NULL or empty.</p>";
            } elseif ($errlvl == 2) {
                echo "<p style='color:red;'>Error: Player ID '" . htmlspecialchars($player_id) . "' already exists.</p>";
            } else {
                echo "<p style='color:red;'>Unknown error occurred. Error Level: " . htmlspecialchars($errlvl) . "</p>";
            }
             pg_free_result($result);
        } else {
            echo "<p style='color:red;'>Database query failed: " . pg_last_error($dbconn) . "</p>";
        }
    }

    pg_close($dbconn);
    ?>
    <br>
    <a href="index.html">Insert Another Player</a> | <a href="index.html">Go to Insert Game Form</a>
</body>
</html>
