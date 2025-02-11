document.addEventListener('DOMContentLoaded', function() {
  const cells = document.querySelectorAll('table td');
  let selectedCells = [];
  let bingoDetected = false;
  let color_coding = {
    "key_phrase": ["rgb(143 217 228)", "rgb(62 118 125)"],
    "miscellaneous": ["rgb(160 228 143)", "rgb(87 152 71)"],
    "assorted_happening": ["rgb(226 166 232)", "rgb(149 76 158)"]
  };
  function rgbToHex(rgb) {
    let match = rgb.match(/\d+/g);
    return match
        ? "#" + match.map(x => parseInt(x).toString(16).padStart(2, '0')).join('')
        : rgb;
  }
  // Add click event to each cell
  cells.forEach((cell, index) => {
    var label = cell.innerText.split(":")[0].trim().toLowerCase(); // assuming format "label:text"
    cell.style.backgroundColor = color_coding[label][0]
    cell.innerText = cell.innerText.split(":")[1];

      cell.addEventListener('click', function() {
        if (rgbToHex(cell.style.backgroundColor) == rgbToHex(color_coding[label][0])) {
            cell.style.backgroundColor = color_coding[label][1]
            selectedCells.push(index);

        } else {
            cell.style.backgroundColor = color_coding[label][0]
            selectedCells = selectedCells.filter(i => i !== index);

        }
          if (bingoDetected) return;


          // Check for Bingo
          if (checkBingo()) {
              bingoDetected = true;
              const playerName = prompt("Bingo! Enter your name:");

              if (playerName) {
                  const bingoTime = new Date().toISOString();
                  sendBingoData(playerName, bingoTime);
              }
          }
      });
  });

  // Bingo detection logic
  function checkBingo() {
      const winningCombinations = [
          [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
          [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
          [0, 4, 8], [2, 4, 6]             // Diagonals
      ];

      return winningCombinations.some(combo => 
          combo.every(index => selectedCells.includes(index))
      );
  }

  // Send bingo data to server
  function sendBingoData(name, time) {
      fetch('bingo.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name, time })
      })
      .then(response => response.json())
      .then(data => console.log('Bingo recorded:', data))
      .catch(error => console.error('Error:', error));
  }

  // Format time with "X minutes ago" logic
  function formatTime(timestamp) {
      const date = new Date(timestamp);
      const now = new Date();
      const minutesAgo = Math.floor((now - date) / (1000 * 60));

      if (minutesAgo < 1) return "Just now";
      if (minutesAgo <= 10) return `${minutesAgo} minute${minutesAgo > 1 ? 's' : ''} ago`;

      // If more than 10 minutes, show actual timestamp
      const options = { hour: '2-digit', minute: '2-digit' };
      return `${date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} ${date.toLocaleTimeString([], options)}`;
  }

  // Update Bingo Log with new time format
  function updateBingoLog() {
      fetch('getBingo.php')
          .then(response => response.json())
          .then(data => {
              let logContent = "";

              data.forEach((entry) => {
                  logContent += `
                      <div style="padding: 5px; color: #0057ff; margin: 2px 0; font-weight: bold;">
                          ${entry.name} - ${formatTime(entry.time)}
                      </div>
                  `;
              });

              document.getElementById('logContent').innerHTML = logContent;
          })
          .catch(error => {
              console.error('Error fetching bingo log:', error);
              document.getElementById('logContent').innerHTML = 'Failed to load log data.';
          });
  }

  // Auto-update Bingo log every second
  setInterval(updateBingoLog, 1000);
});
