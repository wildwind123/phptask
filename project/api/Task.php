<?php
namespace project\api;

use project\db\Db;

class Task
{

    public static function PostTask(TaskModel $task) 
    {
        $db = (new Db())->GetDb();
        $sql = "INSERT INTO task (`description`, done) values(:description, :done)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':description', $task->description, \PDO::PARAM_STR);
        $stmt->bindParam(':done', $task->done, \PDO::PARAM_BOOL);
        $stmt->execute();
    }

    public static function GetTask() : array
    {
        $db = (new Db())->GetDb();
        $sql = "SELECT * FROM task order by done, id  ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $response = [];
        foreach ($result as $row) {
            $task = new TaskModel();
            $task->id = $row['id'];
            $task->description = $row['description'];
            $task->done = !empty($row['done']);
            $response[] = $task;
        }
        return $response;
    }

    public static function GetTaskID(int $taskId) : ?TaskModel 
    {

        $db = (new Db())->GetDb();
        $sql = "SELECT * FROM task where id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $taskId, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $task = new TaskModel();
        foreach ($result as $row) {
            $task->id = $row['id'];
            $task->description = $row['description'];
            $task->done = !empty($row['done']);
        }
        return $task;

    }

    public static function PutTask(TaskModel $task)
    {
        // print_r($task); die()

        $db = (new Db())->GetDb();
        $sql = "update task set description = :description, done = :done where id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':description', $task->description, \PDO::PARAM_STR);
        $done = !empty($task->done);
        $stmt->bindParam(':done', $done, \PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $task->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function DeleteTask(int $id) 
    {
        $db = (new Db())->GetDb();
        $sql = "DELETE FROM task WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }
    
}