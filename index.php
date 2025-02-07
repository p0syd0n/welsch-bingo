<?php
session_start();
$bop = array();
$file = fopen("expressions.txt", "r");
$file_content = fread($file, filesize("expressions.txt"));
fclose($file);  // Close the file after reading

// Use explode to split file content into an array of expressions (lines)
$expressions = explode("\n", trim($file_content)); // Trim to remove trailing spaces and newlines


function create_board($expressions) {
  $bop_local = array(); // Initialize local board array
  $expressions_copy = $expressions; // Copy the expressions array to avoid modifying the original
  for ($i = 0; $i < 9; $i++) {
      $choice_index = array_rand($expressions_copy); // Random index from expressions copy
      array_push($bop_local, $expressions_copy[$choice_index]); // Add the element to the board
      array_splice($expressions_copy, $choice_index, 1); // Remove the chosen element from the copy to avoid duplicates
  }
  return $bop_local;
}

// Check session timeout and board data
if (!isset($_SESSION["time"]) || $_SESSION["time"] + 43200 < time()) {
    $_SESSION["time"] = time();
    $bop = create_board($expressions); // Create a new board
    $_SESSION["my_board"] = $bop; // Store the board in session
} else {
    $bop = $_SESSION["my_board"]; // Get the existing board from session
}

?>
<html>
  <head>
      <title>Welsch Bingo</title>
      <style>
body {
    font-family: Arial, sans-serif;
    background-color: #2c3e50;
    color: #ecf0f1;
    text-align: center;
    margin: 0;
    padding: 20px;
}

h1 {
    font-size: 2em;
    margin-bottom: 10px;
}

hr {
    border: 1px solid #34495e;
    width: 60%;
    margin: 20px auto;
}

table {
    margin: 20px auto;
    border-collapse: collapse;
    width: 50%;
    background-color: #34495e;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    overflow: hidden;
}

td {
    border: 1px solid #2c3e50;
    padding: 20px;
    text-align: center;
    font-size: 1.2em;
    cursor: pointer;
    transition: background-color 0.3s;
}

td:hover {
    background-color: #16a085;
}

#bingoLogBox {
    margin: 20px auto;
    padding: 15px;
    border: 1px solid #34495e;
    width: 50%;
    max-height: 150px;
    overflow-y: auto;
    background-color: #1c2833;
    color: #ecf0f1;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

#bingoLogBox h3 {
    margin: 0 0 10px;
    font-size: 1.4em;
    color: #1abc9c;
}
      </style>
  </head>
  <body>
      <h1>Welsch Bingo</h1>
      <hr>
      <table>
          <?php 
          $index = 0; // Track element index in the board
          for ($row = 0; $row < 3; $row++): ?>
              <tr>
                  <?php for ($col = 0; $col < 3; $col++): ?>
                      <td><?php echo $bop[$index++]; ?></td> <!-- Use $bop to get expressions -->
                  <?php endfor; ?>
              </tr>
          <?php endfor; ?>
      </table>

      <!-- Box to display bingo log -->
      <div id="bingoLogBox">
          <h3>Bingo Log</h3>
          <div id="logContent">Loading...</div>
      </div>

      <script src="animations.js"></script>
  </body>
</html>
