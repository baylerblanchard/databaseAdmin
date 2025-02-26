<?php  // Bayler Blanchard Assignment 4
include 'connect.php';

$player1_id = isset($_POST['player1_id']) ? $_POST['player1_id'] : '';
$player2_id = isset($_POST['player2_id']) ? $_POST['player2_id'] : '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Insert Game Result</title>
</head>
<body>
    <h1>Insert Game Result</h1>
    <?php

    if (empty($player1_id) || empty($player2_id)) {
        echo "<p style='color:red;'>Please enter both Player 1 and Player 2 IDs.</p>";
    } else {
        $sql = "CALL proc_insert_game($1, $2, $3)";
        $params = array($player1_id, $player2_id, null); 

        $result = pg_query_params($dbconn, $sql, $params);

        if ($result) {
            $row = pg_fetch_row($result);
            $errlvl = $row[2];

            // I added colors for the messages so I can tell the error easier.
            if ($errlvl == 0) {
                echo "<p style='color:green;'>Game inserted successfully for players '" . htmlspecialchars($player1_id) . "' and '" . htmlspecialchars($player2_id) . "'.</p>";
            } elseif ($errlvl == 1) {
                echo "<p style='color:red;'>Error: Both Player 1 and Player 2 IDs must not be NULL or empty.</p>";
            } elseif ($errlvl == 2) {
                echo "<p style='color:red;'>Error: Player 1 and Player 2 IDs cannot be the same.</p>";
            } elseif ($errlvl == 3) {
                echo "<p style='color:red;'>Error: Game with players '" . htmlspecialchars($player1_id) . "' and '" . htmlspecialchars($player2_id) . "' already exists.</p>";
            } elseif ($errlvl == 4) {
                echo "<p style='color:red;'>Error: One or both of the Player IDs do not exist in the Players table.</p>";
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
    <a href="index.html">Insert Another Game</a> | <a href="index.html">Go to Insert Player Form</a>
</body>
</html>
