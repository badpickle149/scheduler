<?php

include("utilities.php");

$db = dbInit($ds, $user, $password);

$response = array();

if (isset($_GET['task'])) {
  $task = $_GET['task'];
  global $db;
  try {
    $query = $db->query("delete from schedule where task_name='$task'");
    $query->execute();
    $response['msg'] = "success! task $task deleted";
  } catch(PDOexception $ex) {
    print($ex);
  }
} else {
  $response['msg'] = "task was not set";
}

header('Content-Type: application/json');
print(json_encode($response));





 ?>
