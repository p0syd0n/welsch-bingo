<?php
// Log file name
$logFile = 'bingo_log.json';

// Check if log file exists
if (file_exists($logFile)) {
    // Read log file and return JSON
    echo file_get_contents($logFile);
} else {
    echo json_encode([]);
}
?>
