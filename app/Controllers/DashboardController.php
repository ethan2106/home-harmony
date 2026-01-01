<?php
namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\TaskModel;

class DashboardController extends Controller {
    private $taskModel;

    public function __construct() {
        $this->taskModel = new TaskModel();
    }

    public function index() {
        // Inclure bootstrap pour charger les variables globales
        require_once __DIR__ . '/../../includes/bootstrap.php';

        $today = date('Y-m-d');
        $todoTasks = $this->taskModel->getTodoTasks($today);
        $doneTasks = $this->taskModel->getDoneTasks($today);

        $this->view('dashboard', [
            'todoTasks' => $todoTasks,
            'doneTasks' => $doneTasks,
            'profiles' => $GLOBALS['profiles'],
            'rooms' => $GLOBALS['rooms'],
            'stats' => $GLOBALS['stats'],
            'date_fr' => $GLOBALS['date_fr']
        ]);
    }
}
