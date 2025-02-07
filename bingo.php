<?php
// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['name']) && isset($data['time'])) {
    $name = htmlspecialchars($data['name']); // Sanitize the name
    $bingoTime = $data['time']; // The time the bingo was detected

    // Save to log file in JSON format
    $logFile = 'bingo_log.json';
    $logData = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

    $logData[] = ['name' => $name, 'time' => $bingoTime];
    
    file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT));

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>
