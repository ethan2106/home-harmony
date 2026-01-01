<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\TaskModel;
use PDO;

class TaskModelTest extends TestCase
{
    private $pdo;
    private $taskModel;

    protected function setUp(): void
    {
        // Création d'une base de données SQLite en mémoire pour les tests
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Création de la table tasks pour le test
        $this->pdo->exec("
            CREATE TABLE tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                room_id INTEGER,
                titre TEXT,
                frequence TEXT,
                dernier_fait DATE,
                fait_par TEXT,
                date_prevue DATE,
                date_creation DATE
            );
            
            CREATE TABLE rooms (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom TEXT,
                emoji TEXT,
                couleur TEXT,
                zone TEXT
            );
        ");

        $this->taskModel = new TaskModel($this->pdo);
    }

    public function testGetAllTasksWithRoomsReturnsEmptyArrayWhenNoTasks(): void
    {
        $tasks = $this->taskModel->getAllTasksWithRooms();
        $this->assertIsArray($tasks);
        $this->assertEmpty($tasks);
    }

    public function testCreateTaskInsertsData(): void
    {
        $data = [
            'room_id' => 1,
            'titre' => 'Nettoyer les vitres',
            'frequence' => 'Mensuel'
        ];

        $result = $this->taskModel->createTask($data);
        $this->assertTrue($result);

        $tasks = $this->taskModel->getAllTasksWithRooms();
        $this->assertCount(1, $tasks);
        $this->assertEquals('Nettoyer les vitres', $tasks[0]['titre']);
    }

    public function testGetTodoTasksReturnsDueTasks(): void
    {
        // Insérer une pièce
        $this->pdo->exec("INSERT INTO rooms (id, nom, zone) VALUES (1, 'Salon', 'maison')");
        
        // Insérer une tâche quotidienne jamais faite
        $this->pdo->exec("INSERT INTO tasks (room_id, titre, frequence, dernier_fait) VALUES (1, 'Aspirateur', 'Quotidien', NULL)");
        
        // Insérer une tâche quotidienne faite aujourd'hui
        $today = date('Y-m-d');
        $this->pdo->exec("INSERT INTO tasks (room_id, titre, frequence, dernier_fait) VALUES (1, 'Vaisselle', 'Quotidien', '$today')");

        $todoTasks = $this->taskModel->getTodoTasks($today);
        
        $this->assertCount(1, $todoTasks);
        $this->assertEquals('Aspirateur', array_values($todoTasks)[0]['titre']);
    }
}
