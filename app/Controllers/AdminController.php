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

    private function deleteTask($id)
    {
        $taskModel = new TaskModel();
        $taskModel->deleteTask($id);
        header('Location: /admin');
        exit;
    }

    private function deleteRoom($id)
    {
        $roomModel = new RoomModel();
        $roomModel->deleteRoom($id);
        header('Location: /admin');
        exit;
    }

    private function deleteProfile($id)
    {
        $userModel = new UserModel();
        $userModel->deleteUser($id);
        header('Location: /admin');
        exit;
    }

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

    public function addTask()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskModel = new TaskModel();
            $taskModel->createTask([
                'room_id' => $_POST['room_id'],
                'titre' => $_POST['titre'],
                'frequence' => $_POST['frequence'],
            ]);
        }

        header('Location: /admin');
        exit;
    }

    public function clearList()
    {
        $taskModel = new TaskModel();
        $taskModel->resetAllTasks();

        header('Location: /');
        exit;
    }

    public function resetApp()
    {
        $taskModel = new TaskModel();
        $taskModel->resetAllData();

        header('Location: /admin?reset_success=1');
        exit;
    }
}
