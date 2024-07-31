<?php
// Function to get average score for a term
function getCumAverageScore(mysqli $con, string $lname, string $term): array
{
    // Prepare the SQL statement
    $stmt = $con->prepare("SELECT AVG(totalscore) AS score FROM lhpresultrecord WHERE lid = ? AND term = ?");
    $stmt->bind_param('ss', $lname, $term);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Close the statement
    $stmt->close();

    // Return the result
    return [
        'score' => $row['score'] ?? 0,
        'exists' => !empty($row['score'])
    ];
}



// Define the function to calculate grade, remarks, and term remarks
function evaluatePerformance(float $marksObtained, float $totalMarks): array
{
    // Calculate percentage
    $percentage = round(($marksObtained / $totalMarks),2);

    // Initialize result array
    $result = [
        'score' => '',
        'grade' => '',
        'remarks' => '',
        'termRemarks' => ''
    ];

    // Determine the grade
    if ($percentage >= 75) {
        $result['score'] = $percentage;
        $result['grade'] = 'A';
        $result['remarks'] = 'Excellent';
        $result['termRemarks'] = 'Congratulations! You have been promoted to the next class. Your academic performance this term is excellent; keep up the good work to sustain this excellent performance in subsequent terms. Keep it up!';
    } elseif ($percentage >= 65) {
        $result['score'] = $percentage;
        $result['grade'] = 'B';
        $result['remarks'] = 'Very Good';
        $result['termRemarks'] = 'Congratulations! You have been promoted to the next class. Your academic performance this term is impressive, but you need to work harder to achieve higher grades next term. Well done!';
    } elseif ($percentage >= 50) {
        $result['score'] = $percentage;
        $result['grade'] = 'C';
        $result['remarks'] = 'Moderate';
        $result['termRemarks'] = 'Congratulations! You have been promoted to the next class. Your academic performance this term is moderate, but with more effort towards studying, you will achieve higher grades next term. Cheer up!';
    } elseif ($percentage >= 45) {
        $result['score'] = $percentage;
        $result['grade'] = 'D';
        $result['remarks'] = 'Fair';
        $result['termRemarks'] = 'Congratulations! You have been promoted to the next class. Your academic performance this term is fair. You can do better if you commit more effort and time to studying thoroughly next term.';
    } elseif ($percentage >= 40) {
        $result['score'] = $percentage;
        $result['grade'] = 'E';
        $result['remarks'] = 'Needs Help';
        $result['termRemarks'] = 'Congratulations! You have been promoted to the next class. Your academic performance this term is fair. You can do better if you commit more effort and time to studying thoroughly next term.';
    } elseif($percentage < 40) {
        $result['score'] = $percentage;
        $result['grade'] = 'F';
        $result['remarks'] = 'Needs Help';
        $result['termRemarks'] = 'Your academic performance this term is below the pass grade. You can do better if you commit more effort and time to studying thoroughly next term.';
    }else {
        $result['score'] = $percentage;
        $result['grade'] = '';
        $result['remarks'] = '';
        $result['termRemarks'] = '';
    }

    return $result;
}
