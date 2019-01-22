<?php

include 'utilities.php';

global $db;
global $host;
global $dbname;

$db = dbInit("mysql:host={$host};dbname={$dbname};charset=utf8", "badpickle", "poopwerty1");

function parmVal($p) { return (isset($_REQUEST[$p]) ? $_REQUEST[$p] : ''); } // get/default param val

function tableHTML($task, $time, $desc) {
  return <<<ROW
    <tr>
      <td class="task">$task</td>
      <td class="time">$time</td>
      <td class="time">$desc</td>
    </tr>
ROW;
}

function getId() {
  global $db;
  $rows = "";
  try {
    $rows = $db->query("select id from schedule");
    $rows = $rows->fetchAll();
  } catch(PDOexception $ex) {
    header("Content-Type: text/plain");
    print ("Error details: $ex \n");
    die();
  }

  $nums = array();
  foreach ($rows as $row) {
    array_push($nums, $row["id"]);
  }

  return returnNewId($nums);
}

function getRand() {
  return mt_rand(10, 100);
}

function returnNewId($nums) {
  $new_id = getRand();
  if (in_array($new_id, $nums)) {
    returnNewId($nums);
  } else {
    return $new_id;
  }
}

$rows = "";
try {
  $query_rows = $db -> query("select * from schedule");
  $query_rows= $query_rows -> fetchAll();
  for ($i = 0; $i < count($query_rows); $i++) {
    $row = $query_rows[$i];
    $rows .= tableHTML($row['task_name'], $row['task_description'], $row['task_time']);
  }
} catch(PDOexception $ex) {
  header('Content-Type: text/plain');
  print($ex);
  die();
}

if ($rows != "") {
  $table = <<<TABLE
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Task Name</th>
          <th>Task Description</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody>
        $rows
      </tbody>
    </table>
TABLE;
}

$db = null; //end $db connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $db = dbInit("mysql:host={$host};dbname={$dbname};charset=utf8", "badpickle", "poopwerty1");
  $task = parmVal("task");
  $time = parmVal("time");
  $desc = parmVal("desc");
  $id = getId();
  $success_message = "";
  $error_message = "";

  if ($task != "" && $time != "") {
    $success_message = <<<SUCCESS
      <h2 style="color: green;" class="text-center">Task Saved!</h2>
SUCCESS;

    try {
      $sql = "insert into schedule (id, task_name, task_description, task_time) values (:id, :task, :desc, :time)";
      $stmt = $db->prepare($sql);
      $params = array("id"=>$id, "task"=>$task, "desc"=>$desc, "time"=>$time);
      $stmt->execute($params);
      $task = $time = $desc = "";
    } catch(PDOexception $ex) {
      header('Content-Type: text/plain');
      print($ex);
    }

    $db = null;
  } else if ($task == "" && $time == "") {
    $error_message = <<<ERROR
      <p style="color: red" class="text-center">Either the time or task was not set</p>
ERROR;
  }

  header('Location: scheduler.php');
}

 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="scheduler.css" type="text/css" rel="stylesheet">
    <script src="scheduler.js" type="text/javascript"></script>
    <title>Scheduler</title>
  </head>
  <body>
    <header>
      <h1 class="text-center">Schedule</h1>
      <?php echo $success_message; ?>
      <?php echo $error_message; ?>
    </header>

      <div class="container">
        <main class="text-center">
          <?php echo $table; ?>
          <form href="scheduler.php" method="post">
            <input name="task" type="text" placeholder="type task here"></input>
            <input name="time" type="datetime-local"></input>
            <input name="desc" type="text" placeholder="description"></input>
            <input type="submit"></input>
          </form>
        </main>
      </div>

  </body>
</html>
