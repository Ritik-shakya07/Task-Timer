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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" />
  <style>
    body {
      background: url('https://source.unsplash.com/1600x900/?abstract,technology') center/cover no-repeat;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 90px 0 120px;
      color: #333;
    }

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
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
      z-index: 1000;
    }

    header h1 {
      margin: 0;
      font-size: 28px;
      flex: 1
    }

    header a.logout {
      color: #fff;
      text-decoration: none;
      background: #968d7e;
      padding: 8px 14px;
      border-radius: 4px;
      transition: 0.3s
    }

    header a.logout:hover {
      background: #484542
    }

    .container {
      max-width: 900px;
      margin: 40px auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
      backdrop-filter: blur(4px);
    }

    h2 {
      text-align: center;
      color: #00695c;
      margin-bottom: 20px;
      font-size: 26px
    }

    .decorative-divider {
      height: 5px;
      background: linear-gradient(to right, #26a69a, #00bcd4, #1e88e5);
      border-radius: 3px;
      margin: 30px auto;
      width: 80%;
    }

    .add-task-container {
      text-align: center;
      margin-bottom: 20px
    }

    .add-task-btn {
      background: #26a69a;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
      font-size: 16px
    }

    .add-task-btn:hover {
      background: #1e88e5
    }

    .task-form {
      display: none;
      text-align: center;
      margin-bottom: 20px
    }

    .task-form input {
      padding: 10px;
      margin: 5px;
      border: 1px solid #ccc;
      border-radius: 8px;
      width: 160px
    }

    .task-form button {
      padding: 10px 20px;
      background: #00acc1;
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin: 5px;
      transition: 0.3s
    }

    .task-form button:hover {
      background: #00838f
    }

    .task-list {
      margin-top: 20px
    }

    .task-list h3 {
      border-bottom: 2px solid #00acc1;
      padding-bottom: 5px;
      color: #00796b;
      font-size: 20px
    }

    .task-list ul {
      list-style: none;
      padding: 0
    }

    .task-list li {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 20px;
      background: #b3e5fc;
      margin: 8px 0;
      border-radius: 12px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      transition: background 0.3s
    }

    .task-list li:hover {
      background: #81d4fa
    }

    .task-left {
      display: flex;
      align-items: center
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
      margin-right: 10px
    }

    .task-name {
      font-size: 16px;
      font-weight: 500
    }

    .task-duration {
      font-size: 16px;
      font-weight: 500;
      color: #00796b;
      margin-right: 5px;
      min-width: 60px;
      text-align: right
    }

    .delete-btn {
      background: #e53935;
      color: #fff;
      border: none;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
      transition: 0.3s
    }

    .delete-btn:hover {
      background: #d32f2f
    }

    .play-btn {
      background: #ff5722;
      color: #fff;
      border: none;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
      transition: 0.3s;
      margin-left: 5px
    }

    .play-btn.active {
      transform: scale(1.1);
      box-shadow: 0 0 8px rgba(255, 87, 34, 0.7)
    }

    .play-btn:hover {
      background: #e64a19
    }

    .controls {
      display: none;
      justify-content: center;
      gap: 10px;
      margin-top: 20px
    }

    .controls button {
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      color: #fff;
      transition: background 0.3s
    }

    .controls .play-all {
      background: #ff5722
    }

    .controls .pause-all {
      background: #fbbf24
    }

    .controls .reset-all {
      background: #f87171
    }

    .controls button:hover {
      opacity: 0.8
    }

    footer {
      background: #f1f1f1;
      color: #000;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      font-size: 14px;
      font-family: 'Georgia', serif;
      box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.2)
    }

    @media(max-width:600px) {
      .task-form input {
        width: 90%;
        margin: 5px auto
      }

      footer {
        flex-direction: column;
        text-align: center
      }
    }
  </style>
</head>

<body>
  <header>
    <h1>Task Timer Dashboard</h1>
    <a href="logout.php" class="logout">Logout</a>
  </header>

  <div class="container">
    <h2>Manage Your Tasks</h2>
    <div class="add-task-container">
      <button class="add-task-btn" id="toggleTaskForm">Add Task</button>
    </div>

    <div class="task-form" id="taskForm">
      <input type="text" id="taskName" placeholder="Task Name" required>
      <input type="number" id="taskMinutes" placeholder="Minutes" min="0">
      <input type="number" id="taskSeconds" placeholder="Seconds" min="0" max="59">
      <button id="saveTask">Save Task</button>
      <button id="cancelTask">Cancel</button>
    </div>

    <div class="decorative-divider"></div>
    <div class="task-list">
      <h3>Added Tasks</h3>
      <ul id="addedTasks"></ul>
    </div>

    <div class="controls" id="controls">
      <button class="play-all" id="playAll"><i class="fas fa-play"></i> Play All</button>
      <button class="pause-all" id="pauseAll"><i class="fas fa-pause"></i> Pause All</button>
      <button class="reset-all" id="resetAll"><i class="fas fa-redo"></i> Reset All</button>
    </div>

  </div>
  <footer>
    <div>Created by: 1. Ritik Shakya, 2. Dharam Khajuriya</div>
    <div>Guided by: Mr. Gaurav Bohare</div>
  </footer>
  <audio id="alarmSound" src="audio/beep.mp3" preload="auto"></audio>
  <script>

    let timerInterval, totalSeconds = 0,
      remainingSeconds = 0,
      isPaused = false,
      tasks = [],
      activeTaskIndex = null,
      activeDurationSpan = null;


    const toggleTaskForm = document.getElementById('toggleTaskForm'),
      taskForm = document.getElementById('taskForm'),
      saveTaskBtn = document.getElementById('saveTask'),
      cancelTaskBtn = document.getElementById('cancelTask'),
      addedTasksList = document.getElementById('addedTasks'),
      alarmSound = document.getElementById('alarmSound'),
      controls = document.getElementById('controls'),
      playAll = document.getElementById('playAll'),
      pauseAll = document.getElementById('pauseAll'),
      resetAll = document.getElementById('resetAll');

    function formatTime(s) {
      let m = Math.floor(s / 60),
        sec = s % 60;
      return (m < 10 ? '0' + m : m) + ':' + (sec < 10 ? '0' + sec : sec)
    }

    function startTimer() {
      clearInterval(timerInterval);
      timerInterval = setInterval(() => {
        if (!isPaused) {
          if (remainingSeconds > 0) {
            remainingSeconds--;
            activeDurationSpan.textContent = formatTime(remainingSeconds);
          } else {
            clearInterval(timerInterval);
            alarmSound.play();
            setTimeout(() => {
              if (activeTaskIndex < tasks.length - 1) startTaskFromList(activeTaskIndex + 1);
            }, 1500);
            preparePlayEffect(false);
          }
        }
      }, 1000)
    }

    function preparePlayEffect(active) {
      if (active) {
        activeDurationSpan.nextSibling.classList.add('active');
      } else {
        document.querySelectorAll('.play-btn.active').forEach(b => b.classList.remove('active'));
      }
    }

    function renderTaskList() {
      addedTasksList.innerHTML = '';
      tasks.forEach((t, i) => {
        const li = document.createElement('li'),
          left = document.createElement('div'),
          num = document.createElement('span'),
          name = document.createElement('span'),
          dur = document.createElement('span'),
          del = document.createElement('button');
        left.className = 'task-left';
        num.className = 'task-number';
        name.className = 'task-name';
        dur.className = 'task-duration';
        del.className = 'delete-btn';
        num.textContent = i + 1;
        name.textContent = t.name;
        dur.textContent = activeTaskIndex === i ? formatTime(remainingSeconds) : formatTime(t.minutes * 60 + t.seconds);
        if (activeTaskIndex === i) activeDurationSpan = dur;
        del.textContent = '×';
        del.addEventListener('click', e => {
          e.stopPropagation();
          deleteTask(i)
        });
          
        left.append(num, name);

        li.append(left);
        if (activeTaskIndex === i) {
          li.className = 'active-task';
          const play = document.createElement('button');
          play.className = 'play-btn active';
          play.innerHTML = '<i class="fas fa-pause"></i>';
          play.addEventListener('click', e => {
            e.stopPropagation();
            isPaused = !isPaused;
            preparePlayEffect(isPaused);
          });
          li.append(dur, del, play);
        } 
        
        else if (activeTaskIndex === null) {
          li.className = 'inactive-task';
        } 
        
        else
          li.className = 'inactive-task';

        if (tasks.length === 1) {
          const play = document.createElement('button');
          play.className = 'play-btn';
          play.innerHTML = '<i class="fas fa-play"></i>';
          play.addEventListener('click', e => {
            e.stopPropagation();
            activeTaskIndex = i;
            totalSeconds = t.minutes * 60 + t.seconds;
            remainingSeconds = totalSeconds;
            isPaused = false;
            renderTaskList();
            startTimer();
            preparePlayEffect(true)
          });

          li.append(dur, del, play);
        }
         
        else {
          li.append(dur, del);
        }

        li.addEventListener('click', () => startTaskFromList(i));
        addedTasksList.append(li)
      });
      
      controls.style.display = tasks.length > 1 ? 'flex' : 'none';
    }

    function deleteTask(i) {
      if (activeTaskIndex === i) clearInterval(timerInterval);
      tasks.splice(i, 1);
      if (activeTaskIndex !== null && activeTaskIndex >= tasks.length) activeTaskIndex = null;
      renderTaskList()
    }

    function startTaskFromList(i) {
      clearInterval(timerInterval);
      activeTaskIndex = i;
      totalSeconds = tasks[i].minutes * 60 + tasks[i].seconds;
      remainingSeconds = totalSeconds;
      isPaused = false;
      renderTaskList();
      startTimer();
      preparePlayEffect(true)
    }
    toggleTaskForm.addEventListener('click', () => taskForm.style.display = taskForm.style.display === 'block' ? 'none' : 'block');
    saveTaskBtn.addEventListener('click', () => {
      const n = document.getElementById('taskName').value.trim(),
        m = parseInt(document.getElementById('taskMinutes').value),
        s = parseInt(document.getElementById('taskSeconds').value);
      if (!n || isNaN(m) || isNaN(s)) {
        alert('Please provide valid details.');
        return;
      }
      tasks.push({
        name: n,
        minutes: m,
        seconds: s
      });
      document.getElementById('taskName').value = '';
      document.getElementById('taskMinutes').value = '';
      document.getElementById('taskSeconds').value = '';
      taskForm.style.display = 'none';
      renderTaskList()
    });
    cancelTaskBtn.addEventListener('click', () => taskForm.style.display = 'none');
    playAll.addEventListener('click', () => activeTaskIndex === null ? startTaskFromList(0) : startTimer());
    pauseAll.addEventListener('click', () => {
      isPaused = true;
      preparePlayEffect(false)
    });
    resetAll.addEventListener('click', () => {
      clearInterval(timerInterval);
      if (activeTaskIndex !== null) {
        remainingSeconds = tasks[activeTaskIndex].minutes * 60 + tasks[activeTaskIndex].seconds;
        activeDurationSpan.textContent = formatTime(remainingSeconds);
      }
      isPaused = false;
      preparePlayEffect(false)
    });
    renderTaskList();
  </script>
</body>

</html>