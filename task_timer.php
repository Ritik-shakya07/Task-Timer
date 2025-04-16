<?php
session_start();
// Protect the page – require a logged-in user
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Timer Dashboard</title>
  <!-- Font Awesome CDN for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" 
        integrity="sha512-dNrXzljJ4ZxHk8bUSKnR0u9tC9Y5aVQv2Fhrw0OVX5C8MXWw1w6VRsJPlm5hS2YG/2o8l3K5Sxi1l1pO9z0fkw==" 
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    /* Global Styles & Background */
    body {
      /* Using an Unsplash URL for a fantastic abstract technology background */
      background: url('https://source.unsplash.com/1600x900/?abstract,technology') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      color: #333;
      min-height: 100vh;
      /* Reserve space for fixed header and footer */
      padding-top: 90px;
      padding-bottom: 120px;
    }
    /* Fixed Header */
    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background: linear-gradient(90deg, #0288d1, #00acc1);
      color: #fff;
      padding: 20px 30px;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      z-index: 1000;
    }
    header img.logo {
      height: 50px;
      width: auto;
      margin-right: 15px;
    }
    header h1 {
      margin: 0;
      font-size: 28px;
      flex-grow: 1;
    }
    /* Welcome greeting appended to header title */
    header .welcome {
      font-size: 18px;
      margin-left: 15px;
      color: #e0f7fa;
    }
    header a.logout {
      color: #fff;
      text-decoration: none;
      background:rgb(150, 141, 126); /* New logout color */
      padding: 8px 14px;
      border-radius: 4px;
      transition: background 0.3s ease;
    }
    header a.logout:hover {
      background:rgb(72, 69, 66);
    }
    /* Main Container */
    .container {
      max-width: 900px;
      margin: 40px auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
      backdrop-filter: blur(4px);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #00695c;
      font-size: 26px;
    }
    /* Decorative Divider */
    .decorative-divider {
      height: 5px;
      background: linear-gradient(to right, #26a69a, #00bcd4, #1e88e5);
      border-radius: 3px;
      margin: 30px auto;
      width: 80%;
    }
    /* Add Task Button & Form */
    .add-task-container {
      text-align: center;
      margin-bottom: 20px;
    }
    .add-task-btn {
      background: #26a69a;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
      font-size: 16px;
    }
    .add-task-btn:hover {
      background: #1e88e5;
    }
    .task-form {
      display: none;
      margin-top: 20px;
      text-align: center;
    }
    .task-form input[type="text"],
    .task-form input[type="number"] {
      padding: 10px;
      margin: 5px;
      border: 1px solid #ccc;
      border-radius: 8px;
      width: 180px;
      font-size: 14px;
    }
    .task-form button {
      padding: 10px 20px;
      background: #00acc1;
      color: #fff;
      border: none;
      border-radius: 8px;
      margin: 5px;
      cursor: pointer;
      transition: background 0.3s ease;
      font-size: 14px;
    }
    .task-form button:hover {
      background: #00838f;
    }
    /* Task List */
    .task-list {
      margin-top: 20px;
    }
    .task-list h3 {
      border-bottom: 2px solid #00acc1;
      padding-bottom: 5px;
      margin-bottom: 10px;
      color: #00796b;
      font-size: 20px;
    }
    .task-list ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .task-list li {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 20px;
      background: #b3e5fc;
      margin: 8px 0;
      border-radius: 12px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: background 0.3s ease;
    }
    .task-list li:hover {
      background: #81d4fa;
    }
    .task-left {
      display: flex;
      align-items: center;
    }
    .task-number {
      background: #0277bd;
      color: #fff;
      font-weight: bold;
      width: 30px;
      height: 30px;
      line-height: 30px;
      border-radius: 50%;
      text-align: center;
      margin-right: 10px;
    }
    .task-name {
      font-size: 16px;
      font-weight: 500;
    }
    /* The task duration now has reduced margin-right so it sits right next to the delete button */
    .task-duration {
      font-size: 16px;
      font-weight: 500;
      color: #00796b;
      margin-right: 5px;
      min-width: 60px;
      text-align: right;
    }
    .delete-btn {
      background: #e53935;
      border: none;
      color: #fff;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.3s ease;
      font-size: 14px;
    }
    .delete-btn:hover {
      background: #d32f2f;
    }
    /* Footer Styles */
    footer {
      background: #f1f1f1;
      color: #000;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      font-size: 14px;
      font-family: 'Georgia', serif;
      box-shadow: 0 -2px 6px rgba(0,0,0,0.2);
    }
    footer .left,
    footer .center,
    footer .right {
      flex: 1;
    }
    footer .left {
      text-align: left;
    }
    footer .center {
      text-align: center;
    }
    footer .right {
      text-align: right;
    }
    footer .center a {
      margin: 0 10px;
      transition: opacity 0.3s ease;
    }
    footer .center a img {
      width: 35px;
      height: 35px;
      vertical-align: middle;
      border-radius: 50%;
    }
    footer .center a:hover {
      opacity: 0.7;
    }
    @media (max-width: 600px) {
      .task-form input[type="text"],
      .task-form input[type="number"] {
        width: 90%;
        margin: 5px auto;
      }
      footer {
        flex-direction: column;
      }
      footer .left,
      footer .center,
      footer .right {
        margin: 5px 0;
      }
    }
  </style>
</head>
<body>
  <header>
    <!-- <img src="logo.png" alt="App Logo" class="logo"> -->
    <h1>Task Timer Dashboard</h1>
    <!-- Display a welcome greeting using the session username -->
    <!-- <span class="welcome">Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?></span> -->
    <a href="logout.php" class="logout">Logout</a>
  </header>
  
  <div class="container">
    <h2>Manage Your Tasks</h2>
    
    <div class="add-task-container">
      <button class="add-task-btn" id="toggleTaskForm">Add Task</button>
    </div>
    
    <div class="task-form" id="taskForm">
      <input type="text" id="taskName" placeholder="Task Name" required>
      <input type="number" id="taskMinutes" placeholder="Minutes" required min="0">
      <input type="number" id="taskSeconds" placeholder="Seconds" required min="0" max="59">
      <button id="saveTask">Save Task</button>
      <button id="cancelTask">Cancel</button>
    </div>
    
    <!-- Decorative Divider -->
    <div class="decorative-divider"></div>
    
    <!-- List of Added Tasks -->
    <div class="task-list">
      <h3>Added Tasks</h3>
      <ul id="addedTasks">
        <!-- Dynamically added tasks will be displayed here -->
      </ul>
    </div>
  </div>
  
  <footer>
    <div class="left">
      Created by: 1. Ritik Shakya, 2. Dharam Khajuriya
    </div>
    <!-- <div class="center">
    <a href="#" target="_blank"><i class="fas fa-envelope fa-2x"></i></a>
            <a href="#" target="_blank"><i class="fab fa-github fa-2x"></i></a>
            <a href="#" target="_blank"><i class="fas fa-code fa-2x"></i></a>
            <a href="#" target="_blank"><i class="fab fa-twitter fa-2x"></i></a>    </div> -->
    <div class="right">
      Guided by: Mr. Gaurav Bohare
    </div>
  </footer>
  
  <!-- Audio element for alarm sound -->
  <audio id="alarmSound" src="audio/beep.mp3" preload="auto"></audio>
  
  <script>
    // Variables and state
    let timerInterval;
    let totalSeconds = 0;
    let remainingSeconds = 0;
    let isPaused = false;
    let tasks = [];
    let activeTaskIndex = null;
    let activeDurationSpan = null; // Reference to the duration element for the active task

    // DOM Elements
    const toggleTaskForm = document.getElementById("toggleTaskForm");
    const taskForm = document.getElementById("taskForm");
    const saveTaskBtn = document.getElementById("saveTask");
    const cancelTaskBtn = document.getElementById("cancelTask");
    const addedTasksList = document.getElementById("addedTasks");
    const alarmSound = document.getElementById("alarmSound");

    // Utility: Format seconds into MM:SS string
    function formatTime(seconds) {
      let mins = Math.floor(seconds / 60);
      let secs = seconds % 60;
      return (mins < 10 ? "0" + mins : mins) + ":" + (secs < 10 ? "0" + secs : secs);
    }

    // Start Timer Countdown for active task
    function startTimer() {
      timerInterval = setInterval(() => {
        if (!isPaused) {
          if (remainingSeconds > 0) {
            remainingSeconds--;
            if (activeDurationSpan) {
              activeDurationSpan.textContent = formatTime(remainingSeconds);
            }
          } else {
            clearInterval(timerInterval);
            // When time is up, play beep sound(s) based on task order (index + 1)
            let beepCount = activeTaskIndex + 1;
            playBeeps(beepCount);
            // After beeps, automatically start the next task (if exists)
            setTimeout(() => {
              if (activeTaskIndex < tasks.length - 1) {
                startTaskFromList(activeTaskIndex + 1);
              } else {
                activeTaskIndex = null;
                // Optionally, display a completion message
              }
            }, beepCount * 500 + 500);
          }
        }
      }, 1000);
    }

    // Function to play beep sound count times
    function playBeeps(count) {
      for (let i = 0; i < count; i++) {
        setTimeout(() => {
          let beep = alarmSound.cloneNode();
          beep.play();
        }, i * 1000); // 500ms interval between beeps
      }
    }

    // Render tasks in the added tasks list with numbering, delete button, and running timer for active task
    function renderTaskList() {
      addedTasksList.innerHTML = "";
      tasks.forEach((task, index) => {
        const li = document.createElement("li");
        
        // Create left side: task number and name
        const leftDiv = document.createElement("div");
        leftDiv.className = "task-left";
        const numSpan = document.createElement("span");
        numSpan.className = "task-number";
        numSpan.textContent = index + 1;
        const nameSpan = document.createElement("span");
        nameSpan.className = "task-name";
        nameSpan.textContent = task.name;
        leftDiv.appendChild(numSpan);
        leftDiv.appendChild(nameSpan);
        
        // Create task duration span: placed just to the left of the delete button
        const durationSpan = document.createElement("span");
        durationSpan.className = "task-duration";
        if (activeTaskIndex === index) {
          durationSpan.textContent = formatTime(remainingSeconds);
          activeDurationSpan = durationSpan;
        } else {
          durationSpan.textContent = formatTime(task.minutes * 60 + task.seconds);
        }
        
        // Create delete button, placed at the far right
        const delBtn = document.createElement("button");
        delBtn.className = "delete-btn";
        delBtn.textContent = "×";
        delBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          deleteTask(index);
        });
        
        // Combine elements into li: leftDiv, then durationSpan, then delete button
        li.appendChild(leftDiv);
        li.appendChild(durationSpan);
        li.appendChild(delBtn);
        
        li.dataset.index = index;
        li.addEventListener("click", () => {
          startTaskFromList(index);
        });
        addedTasksList.appendChild(li);
      });
    }

    // Delete task by index and update activeTaskIndex if needed
    function deleteTask(index) {
      if (activeTaskIndex === index) {
        clearInterval(timerInterval);
        activeTaskIndex = null;
      }
      tasks.splice(index, 1);
      if (activeTaskIndex !== null && activeTaskIndex >= tasks.length) {
        activeTaskIndex = null;
      }
      renderTaskList();
    }

    // Start a task when clicked from the list
    function startTaskFromList(index) {
      clearInterval(timerInterval);
      activeTaskIndex = index;
      const task = tasks[index];
      totalSeconds = task.minutes * 60 + task.seconds;
      remainingSeconds = totalSeconds;
      isPaused = false;
      renderTaskList();
      startTimer();
    }

    // Toggle task form visibility
    toggleTaskForm.addEventListener("click", () => {
      taskForm.style.display = taskForm.style.display === "block" ? "none" : "block";
    });
    
    // Save new task
    saveTaskBtn.addEventListener("click", () => {
      const name = document.getElementById("taskName").value.trim();
      const minutes = parseInt(document.getElementById("taskMinutes").value, 10);
      const seconds = parseInt(document.getElementById("taskSeconds").value, 10);
      if (name === "" || isNaN(minutes) || isNaN(seconds)) {
        alert("Please provide valid task details.");
        return;
      }
      tasks.push({ name, minutes, seconds });
      document.getElementById("taskName").value = "";
      document.getElementById("taskMinutes").value = "";
      document.getElementById("taskSeconds").value = "";
      taskForm.style.display = "none";
      renderTaskList();
    });
    
    // Cancel adding task
    cancelTaskBtn.addEventListener("click", () => {
      taskForm.style.display = "none";
    });
    
    // Initial render of the task list
    renderTaskList();
  </script>
</body>
</html>
