<?php

/**
 * Contrôleur API - Gestion des appels AJAX
 */

namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\UserModel;
use Exception;

class ApiController extends Controller
{
    public function __construct()
    {
        // Les modèles sont chargés automatiquement via Composer
    }

    /**
     * @return void
     */
    public function updateTask()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);

            return;
        }

        $taskId = $_POST['id'] ?? null;
        $profilNom = $_POST['profil'] ?? null;

        if (! $taskId) {
            echo json_encode(['success' => false, 'error' => 'ID manquant']);

            return;
        }

        try {
            $taskModel = new TaskModel();
            $userModel = new UserModel();

            // 1. Récupérer l'ID de l'utilisateur à partir de son nom
            $user = $userModel->getUserByName($profilNom);
            $userId = $user ? (int)$user['id'] : null;

            // 2. Valider la tâche
            $result = $taskModel->validateTask((int)$taskId, $userId);

            if (! $result['success']) {
                echo json_encode($result);

                return;
            }

            echo json_encode([
                'success' => true,
                'points' => $result['points'] ?? 0,
                'message' => 'Tâche validée avec succès !',
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * @return void
     */
    public function pickTask()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);

            return;
        }

        $taskId = $_POST['id'] ?? null;

        if (! $taskId) {
            echo json_encode(['success' => false, 'error' => 'ID manquant']);

            return;
        }

        try {
            $taskModel = new TaskModel();

            $result = $taskModel->pickTask((int)$taskId);

            echo json_encode(['success' => $result]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * @return void
     */
    public function undoTask()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);

            return;
        }

        $taskId = $_POST['id'] ?? null;

        if (! $taskId) {
            echo json_encode(['success' => false, 'error' => 'ID manquant']);

            return;
        }

        try {
            $taskModel = new TaskModel();

            $result = $taskModel->undoTask((int)$taskId);

            echo json_encode($result);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
