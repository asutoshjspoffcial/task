<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { padding-top: 20px; }
        .task-card { margin-bottom: 20px; border-left: 4px solid #0d6efd; }
        .completed { border-left-color: #198754; opacity: 0.8; }
        .form-container { max-width: 600px; margin: 0 auto; }
        .status-badge { font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Task Manager</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="loadTasks()">Tasks</a>
                        </li>
                    </ul>
                    <div id="auth-buttons">
                        <button class="btn btn-outline-primary me-2" onclick="showRegisterForm()">Register</button>
                        <button class="btn btn-primary" onclick="showLoginForm()">Login</button>
                    </div>
                    <div id="user-info" style="display: none;">
                        <span class="navbar-text me-3" id="username-display"></span>
                        <button class="btn btn-danger" onclick="logout()">Logout</button>
                    </div>
                </div>
            </div>
        </nav>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div id="content-area">
                    <!-- Content loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="registerName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="registerName" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="registerEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="registerPassword" required minlength="8">
                        </div>
                        <div class="mb-3">
                            <label for="registerPasswordConfirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="registerPasswordConfirmation" required>
                        </div>
                        <div class="alert alert-danger" id="registerError" style="display: none;"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="register()">Register</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="loginEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" required>
                        </div>
                        <div class="alert alert-danger" id="loginError" style="display: none;"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="login()">Login</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalTitle">Add Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="taskForm">
                        <input type="hidden" id="taskId">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="taskTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="taskDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="taskDueDate" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="taskDueDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskStatus" class="form-label">Status</label>
                            <select class="form-select" id="taskStatus" required>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="alert alert-danger" id="taskError" style="display: none;"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="taskSubmitBtn" onclick="saveTask()">Save Task</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let authToken = localStorage.getItem('authToken');
        let currentUser = JSON.parse(localStorage.getItem('currentUser'));
        
        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            // Get CSRF cookie first
            fetch('/sanctum/csrf-cookie', {
                credentials: 'include'
            }).then(() => {
                updateAuthUI();
                if (authToken) {
                    loadTasks();
                }
            });
        });
        
        // Update UI based on authentication status
        function updateAuthUI() {
            const authButtons = document.getElementById('auth-buttons');
            const userInfo = document.getElementById('user-info');
            
            if (authToken && currentUser) {
                authButtons.style.display = 'none';
                userInfo.style.display = 'block';
                document.getElementById('username-display').textContent = currentUser.name;
            } else {
                authButtons.style.display = 'block';
                userInfo.style.display = 'none';
            }
        }
        
        // Show register form
        function showRegisterForm() {
            const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
            registerModal.show();
        }
        
        // Show login form
        function showLoginForm() {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        }
        
        // Register a new user
        function register() {
            const name = document.getElementById('registerName').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const passwordConfirmation = document.getElementById('registerPasswordConfirmation').value;
            
            if (password !== passwordConfirmation) {
                showError('registerError', 'Passwords do not match');
                return;
            }
            
            fetch('/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'include',
                body: JSON.stringify({
                    name: name,
                    email: email,
                    password: password,
                    password_confirmation: passwordConfirmation
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                // Auto-login after registration
                loginAfterRegister(email, password);
            })
            .catch(error => {
                let errorMessage = 'Registration failed';
                if (error.errors) {
                    errorMessage = Object.values(error.errors).join('\n');
                }
                showError('registerError', errorMessage);
            });
        }
        
        // Login after successful registration
        function loginAfterRegister(email, password) {
            fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'include',
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                authToken = data.token;
                currentUser = data.user;
                
                localStorage.setItem('authToken', authToken);
                localStorage.setItem('currentUser', JSON.stringify(currentUser));
                
                updateAuthUI();
                document.getElementById('registerModal').querySelector('.btn-close').click();
                loadTasks();
            })
            .catch(error => {
                let errorMessage = 'Auto-login failed';
                if (error.message) {
                    errorMessage = error.message;
                }
                showError('registerError', errorMessage);
            });
        }
        
        // Login user
        function login() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'include',
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                authToken = data.token;
                currentUser = data.user;
                
                localStorage.setItem('authToken', authToken);
                localStorage.setItem('currentUser', JSON.stringify(currentUser));
                
                updateAuthUI();
                document.getElementById('loginModal').querySelector('.btn-close').click();
                loadTasks();
            })
            .catch(error => {
                let errorMessage = 'Login failed';
                if (error.message) {
                    errorMessage = error.message;
                }
                showError('loginError', errorMessage);
            });
        }
        
        // Logout user
        function logout() {
            fetch('/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + authToken,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'include'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Logout failed');
                }
                return response.json();
            })
            .then(() => {
                authToken = null;
                currentUser = null;
                
                localStorage.removeItem('authToken');
                localStorage.removeItem('currentUser');
                
                updateAuthUI();
                document.getElementById('content-area').innerHTML = '';
            })
            .catch(error => {
                alert(error.message);
            });
        }
        
        // Load tasks
        function loadTasks() {
            if (!authToken) return;
            
            fetch('/tasks', {
                headers: {
                    'Authorization': 'Bearer ' + authToken,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'include'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load tasks');
                }
                return response.json();
            })
            .then(tasks => {
                renderTasks(tasks);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load tasks: ' + error.message);
            });
        }
        
        // Render tasks in the UI
        function renderTasks(tasks) {
            const contentArea = document.getElementById('content-area');
            
            if (!tasks || tasks.length === 0) {
                contentArea.innerHTML = `
                    <div class="alert alert-info">
                        No tasks found. Create your first task!
                    </div>
                    <button class="btn btn-primary" onclick="showAddTaskForm()">Add Task</button>
                `;
                return;
            }
            
            let html = `
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Your Tasks</h2>
                    <button class="btn btn-primary" onclick="showAddTaskForm()">Add Task</button>
                </div>
                <div class="row" id="tasks-container">
            `;
            
            tasks.forEach(task => {
                const dueDate = new Date(task.due_date);
                const formattedDate = dueDate.toLocaleDateString();
                
                const statusClass = task.status === 'completed' ? 'bg-success' : 
                                  task.status === 'in_progress' ? 'bg-warning text-dark' : 'bg-secondary';
                
                const cardClass = task.status === 'completed' ? 'task-card completed' : 'task-card';
                
                html += `
                    <div class="col-md-6">
                        <div class="card ${cardClass}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title">${task.title}</h5>
                                    <span class="badge ${statusClass} status-badge">${task.status.replace('_', ' ')}</span>
                                </div>
                                <p class="card-text">${task.description || 'No description'}</p>
                                <p class="card-text"><small class="text-muted">Due: ${formattedDate}</small></p>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="editTask(${task.id})">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteTask(${task.id})">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `</div>`;
            contentArea.innerHTML = html;
        }
        
        // Show add task form
        function showAddTaskForm() {
            document.getElementById('taskModalTitle').textContent = 'Add Task';
            document.getElementById('taskSubmitBtn').textContent = 'Save Task';
            document.getElementById('taskForm').reset();
            document.getElementById('taskId').value = '';
            document.getElementById('taskDueDate').valueAsDate = new Date();
            document.getElementById('taskStatus').value = 'pending';
            
            const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
            taskModal.show();
        }
        
        // Edit task - FIXED
        function editTask(taskId) {
            fetch(`/tasks/${taskId}`, {
                headers: {
                    'Authorization': 'Bearer ' + authToken,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'include'
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(task => {
                document.getElementById('taskModalTitle').textContent = 'Edit Task';
                document.getElementById('taskSubmitBtn').textContent = 'Update Task';
                document.getElementById('taskId').value = task.id;
                document.getElementById('taskTitle').value = task.title;
                document.getElementById('taskDescription').value = task.description || '';
                
                // Fix date formatting for the date input
                const dueDate = new Date(task.due_date);
                const formattedDate = dueDate.toISOString().split('T')[0];
                document.getElementById('taskDueDate').value = formattedDate;
                
                document.getElementById('taskStatus').value = task.status;
                
                const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
                taskModal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load task: ' + error.message);
            });
        }
        
        // Save task (create or update) - FIXED
        function saveTask() {
            const taskId = document.getElementById('taskId').value;
            const url = taskId ? `/tasks/${taskId}` : '/tasks';
            const method = taskId ? 'PUT' : 'POST';
            
            const taskData = {
                title: document.getElementById('taskTitle').value,
                description: document.getElementById('taskDescription').value,
                due_date: document.getElementById('taskDueDate').value,
                status: document.getElementById('taskStatus').value
            };
            
            fetch(url, {
                method: method,
                headers: {
                    'Authorization': 'Bearer ' + authToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'include',
                body: JSON.stringify(taskData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('taskModal').querySelector('.btn-close').click();
                loadTasks();
            })
            .catch(error => {
                let errorMessage = 'Failed to save task';
                if (error.errors) {
                    errorMessage = Object.values(error.errors).join('\n');
                } else if (error.message) {
                    errorMessage = error.message;
                }
                showError('taskError', errorMessage);
            });
        }
        
        // Delete task - FIXED
        function deleteTask(taskId) {
            if (!confirm('Are you sure you want to delete this task?')) return;
            
            fetch(`/tasks/${taskId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + authToken,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'include'
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(() => {
                loadTasks();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete task: ' + (error.message || 'Unknown error'));
            });
        }
        
        // Show error message
        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            
            setTimeout(() => {
                errorElement.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>