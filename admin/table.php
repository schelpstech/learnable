<?php

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'lhp');

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Select all subject names from the subject table
$subject_query = "SELECT * FROM lhpsubject where classid = 2";
$subject_result = mysqli_query($db, $subject_query);

// Check if the query was successful
if ($subject_result) {
    // Build the table header with the subject names
    echo '<table>';
    echo '<tr>';
    while ($subject = mysqli_fetch_assoc($subject_result)) {
        echo '<th>' . $subject['sbjname'] . '</th>';
    }
    echo '</tr>';

    // Select all student scores from the scores table
    $scores_query = "SELECT * FROM lhpresultrecord where classid = 2 and subjid = '$subject[sbjid]'";
    $scores_result = mysqli_query($db, $scores_query);

    // Check if the query was successful
    if ($scores_result) {
        // Build the table rows with the student scores
        while ($score = mysqli_fetch_assoc($scores_result)) {
            
            echo '<tr>';
            echo '<td>' . $score['score'] . '</td>';
            echo '</tr>';
        }
    } else {
        echo 'Error: ' . mysqli_error($db);
    }

    echo '</table>';
} else {
    echo 'Error: ' . mysqli_error($db);
}

// Close the database connection
mysqli_close($db);

?>
