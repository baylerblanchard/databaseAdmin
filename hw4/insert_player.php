<?php
include 'connect.php'; // Assuming connect.php is in the same directory and correctly configured

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
        $params = array($player_id, null); // $2 is for INOUT parm_errlvl

        $result = pg_query_params($dbconn, $sql, $params);

        if ($result) {
            $row = pg_fetch_row($result);
            $errlvl = $row[1]; // parm_errlvl is the second output parameter

            if ($errlvl == 0) {
                echo "<p style='color:green;'>Player ID '" . htmlspecialchars($player_id) . "' inserted successfully.</p>";
            } elseif ($errlvl == 1) {
                echo "<p style='color:red;'>Error: Player ID cannot be NULL or empty.</p>";
            } elseif ($errlvl == 2) {
                echo "<p style='color:red;'>Error: Player ID '" . htmlspecialchars($player_id) . "' already exists.</p>";
            } else {
                echo "<p style='color:red;'>Unknown error occurred. Error Level: " . htmlspecialchars($errlvl) . "</p>";
            }
             pg_free_result($result); // Free result set
        } else {
            echo "<p style='color:red;'>Database query failed: " . pg_last_error($dbconn) . "</p>";
        }
    }

    pg_close($dbconn);
    ?>
    <br>
    <a href="index_player.html">Insert Another Player</a> | <a href="index_game.html">Go to Insert Game Form</a>
</body>
</html>
