<?php
require_once 'task.php';
const FILE_NAME = "task.json";

class TaskManager
{
    private $tasks = [];

    public function __construct()
    {
        $this->load();
    }

    private function ensureFileExists()
    {
        if (!file_exists(FILE_NAME)) {
            file_put_contents(FILE_NAME, json_encode([]));
        }
    }

    private function load()
    {
        $this->ensureFileExists();
        $data = file_get_contents(FILE_NAME);

        $decode = json_decode($data, true);
        if (!is_array($decode)) {
            $decode = [];
        }

        foreach ($decode as $task) {
            $this->tasks[] = new Task(
                $task["id"],
                $task["description"],
                $task["status"],
                $task["createdAt"],
                $task["updatedAt"]
            );
        }
    }

    private function save()
    {
        $data = array_map(function ($task) {
            return [
                'id' => $task->id,
                'description' => $task->description,
                'status' => $task->status,
                'createdAt' => $task->createdAt,
                'updatedAt' => $task->updatedAt,

            ];
        }, $this->tasks);

        file_put_contents(FILE_NAME, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function addTask($description)
    {
        $id = uniqid();
        $task = new Task($id, $description);
        $this->tasks[] = $task;
        $this->save();
        echo "Task added successfully (ID : $id)";
    }

    public function updateTask($id, $newDescription)
    {
        foreach ($this->tasks as $task) {
            if ($task->id === $id) {
                $task->description = $newDescription;
                $task->updatedAt = date("Y-m-d H:i:s");
                $this->save();
                echo "Task updated. \n";
                return;
            }
        }

        echo "Task not found. \n";
    }

    public function deleteTask($id)
    {
        foreach ($this->tasks as $index => $task) {
            if ($task->id === $id) {
                unset($this->tasks[$index]);
                $this->tasks = array_values($this->tasks);
                $this->save();
                echo "Task deleted. \n";
                return;
            }
        }
        echo "Task not found. \n";
    }

    public function changeStatus($id, $status)
    {
        $validStatuses = ["todo", "done", "in-progress"];
        if (!in_array($status, $validStatuses)) {
            echo "Invalid status. Use: todo, in-progress, done \n";
            return;
        }

        foreach ($this->tasks as $task) {
            if ($task->id === $id) {
                $task->status = $status;
                $task->updatedAt = date("Y-m-d H:i:s");
                $this->save();
                echo "Task status updated to {$status}";

                return;
            }
        }
        echo "Task not found. \n";
    }

    public function listTasks($status = null)
    {
        foreach ($this->tasks as $task) {
            if ($status === null || $status === $status) {
                echo "[{$task->status}] {$task->id} - {$task->description} (Created: {$task->createdAt}, Updated: {$task->updatedAt})\n";
            }
        }
    }
}

$manager = new TaskManager();
$args = $argv;
array_shift($args);

$command = $args[0] ?? null;

switch ($command) {
    case "add":
        $description = $args[1] ?? null;
        if ($description) {
            $manager->addTask($description);
        } else {
            echo "Usage: php taskManager.php add \"task description\".\n";
        }
        break;
    case "update":
        $id = $args[1] ?? null;
        $description = $args[2] ?? null;
        if ($id && $desc) {
            $manager->updateTask($id, $description);
        } else {
            echo "Usage: php taskManager.php update <id> \"new description\".\n";
        }
        break;
    case "delete":
        $id = $args[1] ?? null;
        if ($id) {
            $manager->deleteTask($id);
        } else {
            echo "Usage: php taskManager.php delete <id>.\n";
        }
        break;
    case "status":
        $id = $args[1] ?? null;
        $status = $args[2] ?? null;
        if ($id && $status) {
            $manager->changeStatus($id, $status);
        } else {
            echo "Usage: php taskManager.php status <id> <todo|in-progress|done>.";
        }
        break;
    case "list":
        $status = $args[1] ?? null;
        $manager->listTasks($status);
        break;
    default:
        echo "Commands:\n";
        echo "  add \"task description\"\n";
        echo "  update <id> \"new description\"\n";
        echo "  delete <id>\n";
        echo "  status <id> <todo|in_progress|done>\n";
        echo "  list [status]\n";
}
