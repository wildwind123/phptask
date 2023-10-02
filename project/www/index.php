<?php
require_once __DIR__ . '/../vendor/autoload.php';;
use project\api\Task;
use project\api\TaskModel;
use project\db\Db;

// phpinfo();die();

$request_uri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$rawBody = file_get_contents('php://input');

$pathArray = explode('/', trim($request_uri , '/'));


if (($pathArray[0] ?? '')  === 'api') {
    // api router 
    header("Content-Type: application/json");
    $res = new stdClass();
    switch ($pathArray[1] ?? '') {
        case 'task':
            if ($requestMethod === 'GET' && (count($pathArray) == 3 || count($pathArray) == 2)) {
                if (count($pathArray) == 3) {
                    // /api/task/{task_id}
                    $task = Task::GetTaskID($pathArray[2] ?? 0);
                    $res->data = $task;
                    printResponse($res);
                    return;
                } else {
                    // /api/task
                    $taskList = Task::GetTask();
                    $res->data = $taskList;
                    printResponse($res);
                    return;
                }    
            } elseif ($requestMethod === 'POST' && count($pathArray) == 2) {
                // /api/task
                $object = json_decode($rawBody);
                if (empty($object) || !is_object($object)) {
                    printError("body is empty or not object");
                    return;
                }
                if (empty($object->description) ) {
                    printError('description is required');
                    return;
                }
                $task = new TaskModel();
                $task->description = $object->description;
                $task->done = !empty($object->done);
                Task::PostTask($task);
                return;
            } elseif ($requestMethod === 'PUT' && count($pathArray) == 3) {
                // /api/task/{task_id}
                $object = json_decode($rawBody);
                if (empty($object) || !is_object($object)) {
                    printError("body is empty or not object");
                    return;
                }
                if (empty($object->description)) {
                    printError('description is required');
                    return;
                }
                if ($pathArray[2] != $object->id) {
                    printError('path task id and object id not equal');
                    return;
                }

                $task = new TaskModel();
                $task->id = $pathArray[2];
                $task->description = $object->description;
                $task->done = !empty($object->done);
                Task::PutTask($task);

            } elseif ($requestMethod === 'DELETE' && count($pathArray) == 3) {
                // /api/task/{task_id}
                Task::DeleteTask($pathArray[2]);
            } else {
                printError("this method not implement");
            }
            break;
    
        case 'ping':
            $res = new stdClass();
            $res->message = 'pong';
            printResponse($res);
            break;
        default:
            printError('unknown method');
            break;
    }
} else {
    $file_path = __DIR__."/../web/index.html";
    if (file_exists($file_path)) {
        // Read the file content
        $file_content = file_get_contents($file_path);
        echo $file_content;
    } else {
        $file_content = 'File not found';
        echo $file_content;
    }
}

function printResponse($response) {
    echo json_encode($response);
}

function printError(string $error) {
    http_response_code(400);
    $res = new stdClass();
    $res->message = $error;
    echo json_encode($res);
}
