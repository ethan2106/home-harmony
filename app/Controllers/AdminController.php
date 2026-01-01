<?php

/**
 * Contrôleur Admin - Gestion de l'administration
 */

namespace App\Controllers;

use App\Models\RoomModel;
use App\Models\TaskModel;
use App\Models\UserModel;

class AdminController extends Controller
{
    public function __construct()
    {
        // Les modèles sont maintenant chargés automatiquement via Composer
    }

    /**
     * @return void
     */
    public function index()
    {
        // Gestion des suppressions
        if (isset($_GET['delete'])) {
            $this->deleteTask($_GET['delete']);
        }

        if (isset($_GET['delete_room'])) {
            $this->deleteRoom($_GET['delete_room']);
        }

        if (isset($_GET['delete_profile'])) {
            $this->deleteProfile($_GET['delete_profile']);
        }

        // Récupération des données
        $taskModel = new TaskModel();
        $userModel = new UserModel();
        $roomModel = new RoomModel();

        $data = [
            'tasks' => $taskModel->getAllTasksWithRooms(),
            'rooms' => $roomModel->getAllRooms(),
            'profiles' => $userModel->getAllUsers(),
            'reset_success' => isset($_GET['reset_success']),
        ];

        $this->view('admin', $data);
    }

    /**
     * @param mixed $id
     * @return void
     */
    private function deleteTask($id)
    {
        $taskModel = new TaskModel();
        $taskModel->deleteTask((int)$id);
        header('Location: /admin');
        exit;
    }

    /**
     * @param mixed $id
     * @return void
     */
    private function deleteRoom($id)
    {
        $roomModel = new RoomModel();
        $roomModel->deleteRoom((int)$id);
        header('Location: /admin');
        exit;
    }

    /**
     * @param mixed $id
     * @return void
     */
    private function deleteProfile($id)
    {
        $userModel = new UserModel();
        $userModel->deleteUser((int)$id);
        header('Location: /admin');
        exit;
    }

    /**
     * @return void
     */
    public function addProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $emoji = $_POST['emoji'] ?? '';
            $couleur = $_POST['couleur'] ?? 'indigo-500';

            if (! empty($nom)) {
                $userModel = new UserModel();
                $userModel->createUser([
                    'nom' => $nom,
                    'emoji' => $emoji,
                    'couleur' => $couleur,
                ]);
            }
        }

        header('Location: /admin');
        exit;
    }

    /**
     * @return void
     */
    public function addRoom()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roomModel = new RoomModel();
            $roomModel->createRoom([
                'nom' => $_POST['nom'],
                'emoji' => $_POST['emoji'],
                'couleur' => $_POST['couleur'],
                'zone' => $_POST['zone'] ?? 'maison',
            ]);
        }

        header('Location: /admin');
        exit;
    }

    /**
     * @return void
     */
    public function addTask()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskModel = new TaskModel();
            $taskModel->createTask([
                'room_id' => (int)$_POST['room_id'],
                'titre' => $_POST['titre'],
                'frequence' => $_POST['frequence'],
            ]);
        }

        header('Location: /admin');
        exit;
    }

    /**
     * @return void
     */
    public function clearList()
    {
        $taskModel = new TaskModel();
        $taskModel->resetAllTasks();

        header('Location: /');
        exit;
    }

    /**
     * @return void
     */
    public function resetApp()
    {
        $taskModel = new TaskModel();
        $taskModel->resetAllData();

        header('Location: /admin?reset_success=1');
        exit;
    }
}
