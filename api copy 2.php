<?php
require_once 'config/database.php';

class Router {
    private $routes = [];
    private $params = [];
    
    public function addRoute($method, $path, $handler) {
        // Convert URL parameters to regex pattern
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = "#^" . $pattern . "$#";
        
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
            'path' => $path
        ];
    }
    
    public function getParams() {
        return $this->params;
    }
    
    public function matchRoute($method, $uri) {
        $uri = parse_url($uri, PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                // Extract named parameters
                $this->params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $route['handler'];
            }
        }
        return null;
    }
}
class Api {
    private $router;
    private $db;
    
    public function __construct() {
        $this->router = new Router();
        $this->setupRoutes();
        
        // Initialize database connection
        try {
            $this->db = DatabaseConfig::getInstance()->getConnection();
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    private function setupRoutes() {
        // Sample routes using database operations
        $this->router->addRoute('GET', '/users/{id}', [$this, 'getUser']);
        $this->router->addRoute('GET', '/users', [$this, 'getAllUsers']);
        $this->router->addRoute('POST', '/users', [$this, 'createUser']);
        
        // ... (other routes remain the same)
    }
    
    // Sample GET method with database operation
    public function getUser() {
        try {
            $params = $this->router->getParams();
            $userId = $params['id'];
            
            $stmt = $this->db->prepare("
                SELECT id, name, email, created_at 
                FROM users 
                WHERE id = :id
            ");
            
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return $this->sendResponse(['error' => 'User not found'], 404);
            }
            
            $this->sendResponse(['user' => $user]);
            
        } catch (PDOException $e) {
            $this->sendResponse([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // Sample GET method with pagination and filtering
    public function getAllUsers() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            
            $offset = ($page - 1) * $limit;
            
            // Count total records for pagination
            $countStmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM users 
                WHERE name LIKE :search OR email LIKE :search
            ");
            
            $countStmt->execute(['search' => "%$search%"]);
            $totalCount = $countStmt->fetch()['total'];
            
            // Get paginated results
            $stmt = $this->db->prepare("
                SELECT id, name, email, created_at 
                FROM users 
                WHERE name LIKE :search OR email LIKE :search
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset
            ");
            
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $users = $stmt->fetchAll();
            
            $this->sendResponse([
                'users' => $users,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ]);
            
        } catch (PDOException $e) {
            $this->sendResponse([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // Sample POST method with database operation
    public function createUser() {
        try {
            $data = $this->getRequestData();
            
            // Validate required fields
            if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
                return $this->sendResponse([
                    'error' => 'Missing required fields',
                    'required' => ['name', 'email', 'password']
                ], 400);
            }
            
            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->sendResponse([
                    'error' => 'Invalid email format'
                ], 400);
            }
            
            // Check if email already exists
            $checkStmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
            $checkStmt->execute(['email' => $data['email']]);
            
            if ($checkStmt->fetch()) {
                return $this->sendResponse([
                    'error' => 'Email already exists'
                ], 409);
            }
            
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, created_at) 
                VALUES (:name, :email, :password, NOW())
            ");
            
            $stmt->execute([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $hashedPassword
            ]);
            
            $userId = $this->db->lastInsertId();
            
            // Fetch the created user (excluding password)
            $newUserStmt = $this->db->prepare("
                SELECT id, name, email, created_at 
                FROM users 
                WHERE id = :id
            ");
            
            $newUserStmt->execute(['id' => $userId]);
            $newUser = $newUserStmt->fetch();
            
            $this->sendResponse([
                'message' => 'User created successfully',
                'user' => $newUser
            ], 201);
            
        } catch (PDOException $e) {
            $this->sendResponse([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ... (other methods remain the same)
}

// Initialize and run the API
$api = new Api();
$api->handleRequest();

?>