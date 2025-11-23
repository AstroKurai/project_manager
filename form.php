<?php
// Name: Jadelynn Wolden
// Date: 11//2025
// Filename: form.php

require_once 'config.php';
require_once 'Project.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('Location: signin.html?redirect=form.php');
    exit;
}

$formSubmitted = false;
$formData = [];
$errors = [];

// Initialize form variables with default values
$projectName = '';
$description = '';
$priority = null;
$status = '';
$dueDate = '';
$startTime = '';
$budget = '';
$teamMembers = [];
$projectType = '';
$notifications = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formSubmitted = true;
    $projectName = trim($_POST['project_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? null;
    $status = $_POST['status'] ?? '';
    $dueDate = $_POST['due_date'] ?? '';
    $startTime = $_POST['start_time'] ?? '';
    $budget = $_POST['budget'] ?? '';
    $teamMembers = $_POST['team_members'] ?? [];
    $projectType = $_POST['project_type'] ?? '';

    if ($projectName == '') {
        $errors[] = "Project name is required";
    }

    if ($description == '') {
        $errors[] = "Description not set";
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
    if (empty($teamMembers)) {
        $errors[] = "At least one team member must be selected";
    }

    if ($projectType == '') {
        $errors[] = "Project type not set";
    }

    if (empty($errors)) {
        // Create database connection
        try {
            // NOTE: Assumes $attr, $user, $pass, $opts are defined in config.php
            $pdo = new PDO($attr, $user, $pass, $opts);

            // Create Project object
            $project = new Project($pdo);

            // Get user_id from session (you'll need to store this at login)
            // For now, we'll look it up by email
            $userQuery = "SELECT id FROM users WHERE email = :email";
            $userStmt = $pdo->prepare($userQuery);
            $userStmt->execute(['email' => $_SESSION['email']]);
            $userData = $userStmt->fetch(PDO::FETCH_ASSOC);

            if (!$userData) {
                $errors[] = "User not found in database.";
                $pdo = null;
            } else {
                $formData = [
                    'user_id' => $userData['id'],
                    'project_name' => $projectName,
                    'description' => $description,
                    'priority' => $priority,
                    'status' => $status,
                    'due_date' => $dueDate,
                    'start_time' => $startTime,
                    'budget' => $budget,
                    'project_type' => $projectType,
                    'team_members' => $teamMembers,
                    'notifications' => $notifications
                ];
                $projectDataForDB = $formData;
                unset($projectDataForDB['team_members'], $projectDataForDB['notifications']);

                $newProjectId = $project->create($projectDataForDB);

                if ($newProjectId) {
                    $successMessage = "Project created successfully! ID: " . $newProjectId;
                } else {
                    $errors[] = "Failed to save project to database";
                }
            }

            $pdo = null;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Project - TaskFlow</title>
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
        <?php if ($formSubmitted && !empty($errors)): ?>
            <div class="error-box">
                <h2>Error:</h2>
                <?php foreach ($errors as $error): ?>
                    <div class="error-item">â€¢ <?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($formSubmitted && empty($errors)): ?>
            <div class="submitted-data">
                <h2>Project Created!</h2>
                <div class="data-item">
                    <div class="data-label">Project Name:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['project_name']); ?></div>
                </div>

                <div class="data-item">
                    <div class="data-label">Description:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['description']); ?></div>
                </div>

                <div class="data-item">
                    <div class="data-label">Priority Level:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['priority']); ?></div>
                </div>

                <div class="data-item">
                    <div class="data-label">Status:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['status']); ?></div>
                </div>

                <div class="data-item">
                    <div class="data-label">Due Date:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['due_date']); ?></div>
                </div>

                <div class="data-item">
                    <div class="data-label">Start Time:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['start_time']); ?></div>
                </div>

                <div class="data-item">
                    <div class="data-label">Budget:</div>
                    <div class="data-value">$<?php echo htmlspecialchars($formData['budget']); ?></div>
                </div>

                <div class="data-item">
                    <div class="data-label">Team Members:</div>
                    <div class="data-value">
                        <?php
                        // FIX: Use $formData['team_members'] which was populated after success
                        if (!empty($formData['team_members'])) {
                            echo implode(', ', array_map('htmlspecialchars', $formData['team_members']));
                        } else {
                            echo 'None selected';
                        }
                        ?>
                    </div>
                </div>

                <div class="data-item">
                    <div class="data-label">Notification Preferences:</div>
                    <div class="data-value">
                        <?php
                        // FIX: Use $formData['notifications'] which was populated after success
                        if (!empty($formData['notifications'])) {
                            echo implode(', ', array_map('htmlspecialchars', $formData['notifications']));
                        } else {
                            echo 'None selected';
                        }
                        ?>
                    </div>
                </div>

                <div class="data-item">
                    <div class="data-label">Project Type:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['project_type']); ?></div>
                </div>

                <div class="data-item">
                    <div class="data-label">User ID (hidden):</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['user_id']); ?></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="project-form-box">
            <h2>Create New Project</h2>

            <form method="POST" action="form.php" autocomplete="off">

                <input type="hidden" name="user_id" value="1">
                <div class="form-group">
                    <label for="project_name">Project Name: *</label>
                    <input type="text"
                        id="project_name"
                        name="project_name"
                        placeholder="Enter project name"
                        required
                        autofocus
                        value="<?php echo htmlspecialchars($projectName ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="description">Project Description:</label>
                    <textarea id="description"
                        name="description"
                        placeholder="Describe your project in detail..."><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Priority Level:</label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="priority_low" name="priority" value="Low"
                                <?php echo ($priority == 'Low' || $priority === null) ? 'checked' : ''; ?>>
                            <label for="priority_low">Low Priority</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="priority_medium" name="priority" value="Medium"
                                <?php echo ($priority == 'Medium') ? 'checked' : ''; ?>>
                            <label for="priority_medium">Medium Priority</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="priority_high" name="priority" value="High"
                                <?php echo ($priority == 'High') ? 'checked' : ''; ?>>
                            <label for="priority_high">High Priority</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="status">Project Status:</label>
                    <select id="status" name="status">
                        <option value="planning" <?php echo ($status == 'planning' || $status == '') ? 'selected' : ''; ?>>Planning</option>
                        <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="on-hold" <?php echo ($status == 'on-hold') ? 'selected' : ''; ?>>On Hold</option>
                        <option value="completed" <?php echo ($status == 'completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="archived" <?php echo ($status == 'archived') ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="due_date">Due Date:</label>
                    <input type="date"
                        id="due_date"
                        name="due_date"
                        value="<?php echo htmlspecialchars($dueDate ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="start_time">Start Time:</label>
                    <input type="time"
                        id="start_time"
                        name="start_time"
                        value="<?php echo htmlspecialchars($startTime ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="budget">Project Budget ($):</label>
                    <input type="number"
                        id="budget"
                        name="budget"
                        placeholder="Enter budget amount"
                        min="0"
                        step="100"
                        value="<?php echo htmlspecialchars($budget ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Assign Team Members:</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="member1" name="team_members[]" value="Kady Alister"
                                <?php echo (in_array('Kady Alister', $teamMembers)) ? 'checked' : ''; ?>>
                            <label for="member1">Kady Alister</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="member2" name="team_members[]" value="Quin Sai"
                                <?php echo (in_array('Quin Sai', $teamMembers)) ? 'checked' : ''; ?>>
                            <label for="member2">Quin Sai</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="member3" name="team_members[]" value="Shawna Peach"
                                <?php echo (in_array('Shawna Peach', $teamMembers)) ? 'checked' : ''; ?>>
                            <label for="member3">Shawna Peach</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="member4" name="team_members[]" value="Wilson Park"
                                <?php echo (in_array('Wilson Park', $teamMembers)) ? 'checked' : ''; ?>>
                            <label for="member4">Wilson Park</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="member5" name="team_members[]" value="Melissa Karp"
                                <?php echo (in_array('Melissa Karp', $teamMembers)) ? 'checked' : ''; ?>>
                            <label for="member5">Melissa Karp</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Notification Preferences:</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="notify_email" name="notifications[]" value="Email"
                                <?php echo (in_array('Email', $notifications)) ? 'checked' : ''; ?>>
                            <label for="notify_email">Email Notifications</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="notify_sms" name="notifications[]" value="SMS"
                                <?php echo (in_array('SMS', $notifications)) ? 'checked' : ''; ?>>
                            <label for="notify_sms">SMS Notifications</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="notify_app" name="notifications[]" value="In-App"
                                <?php echo (in_array('In-App', $notifications)) ? 'checked' : ''; ?>>
                            <label for="notify_app">In-App Notifications</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="project_type">Project Type:</label>
                    <select id="project_type" name="project_type">
                        <option value="">Select a category...</option>
                        <option value="web-development" <?php echo ($projectType == 'web-development') ? 'selected' : ''; ?>>Web Development</option>
                        <option value="mobile-app" <?php echo ($projectType == 'mobile-app') ? 'selected' : ''; ?>>Mobile App</option>
                        <option value="marketing" <?php echo ($projectType == 'marketing') ? 'selected' : ''; ?>>Marketing Campaign</option>
                        <option value="infrastructure" <?php echo ($projectType == 'infrastructure') ? 'selected' : ''; ?>>Infrastructure</option>
                        <option value="research" <?php echo ($projectType == 'research') ? 'selected' : ''; ?>>Research & Development</option>
                        <option value="other" <?php echo ($projectType == 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Create Project</button>
            </form>
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