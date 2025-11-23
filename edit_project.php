<?php
// Name: Jadelynn Wolden
// Date: 11/17/2025
// Filename: edit_project.php

require_once 'config.php';
require_once 'Project.php';

if (!isset($_SESSION['email'])) {
    header('Location: signin.html?redirect=projects.html');
    exit;
}

// Check if project ID is provided
if (!isset($_GET['id']) && !isset($_POST['id'])) {
    header('Location: projects.html');
    exit;
}

$projectId = $_GET['id'] ?? $_POST['id'];
$errors = [];
$successMessage = '';

try {
    $pdo = new PDO($attr, $user, $pass, $opts);
    $project = new Project($pdo);
    
    // Get current project data
    $projectData = $project->getById($projectId);
    
    if (!$projectData) {
        header('Location: projects.html');
        exit;
    }
    
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $projectName = trim($_POST['project_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? null;
        $status = $_POST['status'] ?? '';
        $dueDate = $_POST['due_date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $budget = $_POST['budget'] ?? '';
        $projectType = $_POST['project_type'] ?? '';
        
        // Validation
        if ($projectName == '') {
            $errors[] = "Project name is required";
        }
        if ($description == '') {
            $errors[] = "Description is required";
        }
        if ($priority === null) {
            $errors[] = "Priority not set";
        }
        if ($status == '') {
            $errors[] = "Status not set";
        }
        if ($dueDate == '') {
            $errors[] = "Due date not set";
        }
        if ($startTime == '') {
            $errors[] = "Start time not set";
        }
        if ($budget == '') {
            $errors[] = "Budget not set";
        }
        if ($projectType == '') {
            $errors[] = "Project type not set";
        }
        
        if (empty($errors)) {
            $updateData = [
                'project_name' => $projectName,
                'description' => $description,
                'priority' => $priority,
                'status' => $status,
                'due_date' => $dueDate,
                'start_time' => $startTime,
                'budget' => $budget,
                'project_type' => $projectType
            ];
            
            $result = $project->update($projectId, $updateData);
            
            if ($result) {
                $successMessage = "Project updated successfully!";
                // Refresh project data
                $projectData = $project->getById($projectId);
            } else {
                $errors[] = "Failed to update project";
            }
        }
    }
    
    $pdo = null;
    
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - TaskFlow</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav>
        <div class="nav-left">
            <h1>Project Manager</h1>
            <a href="index.html">home</a>
            <a href="projects.html">projects</a>
            <a href="calendar.html">calendar</a>
            <a href="documents.html">docs</a>
        </div>
        <div class="nav-right">
            <span class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></span>
            <button class="btn-auth" id="signOutBtn">Sign Out</button>
        </div>
    </nav>

    <div class="project-form-container">
        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <h2>Error:</h2>
                <?php foreach ($errors as $error): ?>
                    <div class="error-item">• <?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div class="submitted-data">
                <h2><?php echo htmlspecialchars($successMessage); ?></h2>
                <p><a href="projects.html">Back to Projects</a></p>
            </div>
        <?php endif; ?>

        <div class="project-form-box">
            <h2>Edit Project</h2>

            <form method="POST" action="edit_project.php" autocomplete="off">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($projectId); ?>">
                
                <div class="form-group">
                    <label for="project_name">Project Name: *</label>
                    <input type="text"
                        id="project_name"
                        name="project_name"
                        placeholder="Enter project name"
                        required
                        autofocus
                        value="<?php echo htmlspecialchars($projectData['project_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Project Description:</label>
                    <textarea id="description"
                        name="description"
                        placeholder="Describe your project in detail..."><?php echo htmlspecialchars($projectData['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Priority Level:</label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="priority_low" name="priority" value="Low"
                                <?php echo ($projectData['priority'] == 'Low') ? 'checked' : ''; ?>>
                            <label for="priority_low">Low Priority</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="priority_medium" name="priority" value="Medium"
                                <?php echo ($projectData['priority'] == 'Medium') ? 'checked' : ''; ?>>
                            <label for="priority_medium">Medium Priority</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="priority_high" name="priority" value="High"
                                <?php echo ($projectData['priority'] == 'High') ? 'checked' : ''; ?>>
                            <label for="priority_high">High Priority</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="status">Project Status:</label>
                    <select id="status" name="status">
                        <option value="planning" <?php echo ($projectData['status'] == 'planning') ? 'selected' : ''; ?>>Planning</option>
                        <option value="active" <?php echo ($projectData['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="on-hold" <?php echo ($projectData['status'] == 'on-hold') ? 'selected' : ''; ?>>On Hold</option>
                        <option value="completed" <?php echo ($projectData['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="archived" <?php echo ($projectData['status'] == 'archived') ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="due_date">Due Date:</label>
                    <input type="date"
                        id="due_date"
                        name="due_date"
                        value="<?php echo htmlspecialchars($projectData['due_date'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="start_time">Start Time:</label>
                    <input type="time"
                        id="start_time"
                        name="start_time"
                        value="<?php echo htmlspecialchars($projectData['start_time'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="budget">Project Budget ($):</label>
                    <input type="number"
                        id="budget"
                        name="budget"
                        placeholder="Enter budget amount"
                        min="0"
                        step="100"
                        value="<?php echo htmlspecialchars($projectData['budget'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="project_type">Project Type:</label>
                    <select id="project_type" name="project_type">
                        <option value="">Select a category...</option>
                        <option value="web-development" <?php echo ($projectData['project_type'] == 'web-development') ? 'selected' : ''; ?>>Web Development</option>
                        <option value="mobile-app" <?php echo ($projectData['project_type'] == 'mobile-app') ? 'selected' : ''; ?>>Mobile App</option>
                        <option value="marketing" <?php echo ($projectData['project_type'] == 'marketing') ? 'selected' : ''; ?>>Marketing Campaign</option>
                        <option value="infrastructure" <?php echo ($projectData['project_type'] == 'infrastructure') ? 'selected' : ''; ?>>Infrastructure</option>
                        <option value="research" <?php echo ($projectData['project_type'] == 'research') ? 'selected' : ''; ?>>Research & Development</option>
                        <option value="other" <?php echo ($projectData['project_type'] == 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Update Project</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                <a href="projects.html">Cancel and return to Projects</a>
            </p>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Jadelynn. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('signOutBtn').addEventListener('click', function() {
            fetch('signout.php')
                .then(response => {
                    window.location.href = 'index.html';
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
</body>

</html>