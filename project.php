<?php
// Name: Jadelynn Wolden
// Date: 11/05/2025
// Filename: project.php

class Project {
    private $pdo;
    public $id;
    public $user_id;
    public $project_name;
    public $description;
    public $priority;
    public $status;
    public $due_date;
    public $start_time;
    public $budget;
    public $project_type;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $query = "INSERT INTO projects (user_id, project_name, description, priority, status, due_date, start_time, budget, project_type) 
                  VALUES (:user_id, :project_name, :description, :priority, :status, :due_date, :start_time, :budget, :project_type)";
        
        $stmt = $this->pdo->prepare($query);
        
        $result = $stmt->execute([
            'user_id' => $data['user_id'],
            'project_name' => $data['project_name'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'status' => $data['status'],
            'due_date' => $data['due_date'],
            'start_time' => $data['start_time'],
            'budget' => $data['budget'],
            'project_type' => $data['project_type']
        ]);
        
        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }
    
    public function getAllByUser($user_id) {
        $query = "SELECT * FROM projects WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM projects WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT * FROM projects ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE projects SET 
                  project_name = :project_name,
                  description = :description,
                  priority = :priority,
                  status = :status,
                  due_date = :due_date,
                  start_time = :start_time,
                  budget = :budget,
                  project_type = :project_type
                  WHERE id = :id";
        
        $stmt = $this->pdo->prepare($query);
        
        return $stmt->execute([
            'id' => $id,
            'project_name' => $data['project_name'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'status' => $data['status'],
            'due_date' => $data['due_date'],
            'start_time' => $data['start_time'],
            'budget' => $data['budget'],
            'project_type' => $data['project_type']
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM projects WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function countByUser($user_id) {
        $query = "SELECT COUNT(*) as total FROM projects WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['user_id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    public function search($user_id, $searchTerm) {
        $query = "SELECT * FROM projects WHERE user_id = :user_id AND project_name LIKE :search ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'user_id' => $user_id,
            'search' => '%' . $searchTerm . '%'
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>