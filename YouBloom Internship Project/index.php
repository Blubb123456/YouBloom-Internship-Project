<?php

session_start();


if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}


$statusOptions = ['Pending', 'Complete'];


if (isset($_POST["create"])) {
    $taskName = htmlspecialchars($_POST["taskname"]);
    $taskDescription = htmlspecialchars($_POST["taskdesc"]);
    $taskStatus = isset($_POST["taskstatus"]) ? htmlspecialchars($_POST["taskstatus"]) : 'Pending'; 

    $newTask = [
        'name' => $taskName,
        'description' => $taskDescription,
        'status' => $taskStatus,
        'created_at' => date("m-d-y"),
        'edit_mode' => false
    ];

    $_SESSION['tasks'][] = $newTask;
}

if (isset($_POST["update_task"])) {
    $index = (int)$_POST["task_index"]; 
    if (isset($_SESSION['tasks'][$index])) {
      
        $_SESSION['tasks'][$index]['name'] = htmlspecialchars($_POST["edit_taskname"]);
        $_SESSION['tasks'][$index]['description'] = htmlspecialchars($_POST["edit_taskdesc"]);
        $_SESSION['tasks'][$index]['status'] = htmlspecialchars($_POST["edit_taskstatus"]);
     
        $_SESSION['tasks'][$index]['edit_mode'] = false; 
    }
}


if (isset($_POST['set_edit'])) {
    $index = (int)$_POST["task_index"];
    if (isset($_SESSION['tasks'][$index])) {
        $_SESSION['tasks'][$index]['edit_mode'] = true;
    }
}


if (isset($_POST['set_view'])) {
    $index = (int)$_POST["task_index"];
    if (isset($_SESSION['tasks'][$index])) {
        $_SESSION['tasks'][$index]['edit_mode'] = false;
    }
}


if (isset($_POST['deletetask'])) {
    $index = (int)$_POST["task_index"];
     unset($_SESSION['tasks'][$index]);
    
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Task Management</title>
<link rel ="stylesheet" type="text/css" href="css/bootstrap.min.css">
<style>
  
    .container { padding: 10px; border: 1px solid #ccc; margin-bottom: 20px; }
    .task-item { border: 1px solid #eee; padding: 10px; margin-bottom: 15px; background-color: #f9f9f9; }
    .task-item label { font-weight: bold; margin-right: 10px; }
    input[type="text"] { width: 90%; padding: 5px; margin-bottom: 8px; box-sizing: border-box; }
    .status-group { margin-top: 5px; margin-bottom: 10px; }
    .static-text { margin-bottom: 5px; }
</style>
<script>

function confirmDelete(taskName, form) {
    if (confirm("Are you sure you want to delete the task: " + taskName + "?")) {
       
        return true;
    }
    
    return false;
}
</script>
</head>
<body>


    <div>
        <form method="post">
            <div class="container">
                <div class="row">
                    <div class ="col-sm-3"> 
                        <h1>Create New Task</h1> 
                        <p>Fill out the fields to create a new task</p>
                        
                        <label for="taskname"><b>Task Name</b></label>
                        <input class = "form-control" type="text" name="taskname" id="taskname" required>
                        <br>
                        
                        <label for="taskdesc"><b>Task Description</b></label>
                        <input class = "form-control" type="text" name="taskdesc" id="taskdesc">
                        <br>
                        
                        <label><b>Task Status</b></label><br>
                    
                        <input  type="radio" name="taskstatus" value="Pending" id="new_status_progress" required> <label for="new_status_progress" style="display: inline;">Pending</label>
                        <br>
                        <input type="radio" name="taskstatus" value="Complete" id="new_status_complete" required> <label for="new_status_complete" style="display: inline;">Complete</label>
                        <br><br>
                        
                        <input class = "btn btn-primary" type="submit" name="create" value="Create Task">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <hr>
    <h2>Current Tasks</h2>
    <?php
    if (empty($_SESSION['tasks'])) {
        echo '<p>No tasks created yet. Use the form above to add one!</p>';
    } else {
        foreach ($_SESSION['tasks'] as $index => $task) {
            echo '<div class="task-item">';
            
            
            if (isset($task['created_at'])) {
                echo '<p><strong>Date Created:</strong> ' . htmlspecialchars($task['created_at']) . ' 📅</p>';
            }
            
         
            if ($task['edit_mode'] === true) {
                
                echo '<form method="post">';
                echo '<input type="hidden" name="task_index" value="' . $index . '">';
                
                
                echo '<label for="name_' . $index . '">Task Name</label>';
                echo '<input class = "form-control" type="text" name="edit_taskname" id="name_' . $index . '" value="' . htmlspecialchars($task['name']) . '" required>';
                 echo '<br>';
            
                echo '<label for="desc_' . $index . '">Description</label>';
                echo '<input class = "form-control"  type="text" name="edit_taskdesc" id="desc_' . $index . '" value="' . htmlspecialchars($task['description']) . '">';
                echo '<br>';
               
                echo '<div class="status-group">';
                echo '<strong>Task Status</strong> ';
                foreach ($statusOptions as $status) {
                    $checked = ($task['status'] === $status) ? 'checked' : '';
                    $id = 'status_' . $index . '_' . str_replace(' ', '_', $status);
                    echo '<br>';
                    echo '<input type="radio" name="edit_taskstatus" value="' . $status . '" id="' . $id . '" ' . $checked . ' required>';
                    echo '<label for="' . $id . '" style="display: inline; margin-right: 15px;">' . $status . '</label>';
                  
                }
                echo '</div>';
                
               
                echo '<input type="submit" name="update_task" value="Save Changes">';
                echo '<input class = "btn btn-primary" type="submit" name="set_view" value="Cancel/View" style="margin-left: 10px;">'; // Button to switch back to static view
                
                echo '</form>';
            
            } else {
                
                echo '<div class="static-text">';
                echo '<p><strong>Task:</strong> ' . htmlspecialchars($task['name']) . '</p>';
                echo '<p><strong>Description:</strong> ' . htmlspecialchars($task['description']) . '</p>';
               
if ($task['status'] === 'Complete') {
    $status_color = 'green';
} else if ($task['status'] === 'Pending') {
    $status_color = 'orange'; 
}
                echo '<p><strong>Status:</strong> <span style="font-weight: bold; color: ' . $status_color . ';">' . htmlspecialchars($task['status']) . '</span></p>';
                echo '</div>';
                
           
                echo '<form method="post" style="display: inline;">';
                echo '<input type="hidden" name="task_index" value="' . $index . '">';
                echo '<input class = "btn btn-primary" type="submit" name="set_edit" value="Edit Task">';
                echo '<p> </p>';
               $js_task_name = addslashes($task['name']);
                
                echo '<input class = "btn btn-primary" type="submit" name="deletetask" value="Delete Task" onclick="return confirmDelete(\'' . $js_task_name . '\', this.form)">';
                echo '</form>';
            }
            
            echo '</div>'; 
        }
    }
    ?>
</body>
</html>
