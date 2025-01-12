<?php

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
    private $db; // You would initialize your database connection here
    
    public function __construct() {
        $this->router = new Router();
        $this->setupRoutes();
        // Initialize database connection here
        // $this->db = new PDO(...);
    }
    
    private function setupRoutes() {
        // Auth routes
        $this->router->addRoute('POST', '/auth/login', [$this, 'login']);
        $this->router->addRoute('POST', '/auth/register', [$this, 'register']);
        $this->router->addRoute('POST', '/auth/logout', [$this, 'logout']);
        
        // Resource routes
        $this->router->addRoute('GET', '/images/{id}', [$this, 'getImage']);
        $this->router->addRoute('GET', '/images', [$this, 'getAllImages']);
        $this->router->addRoute('POST', '/images', [$this, 'createImage']);
        $this->router->addRoute('PUT', '/images/{id}', [$this, 'updateImage']);
        $this->router->addRoute('DELETE', '/images/{id}', [$this, 'deleteImage']);
    }
    
    private function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    private function getRequestData() {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    // Auth handlers
    public function login() {
        $data = $this->getRequestData();
        // Validate input
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->sendResponse(['error' => 'Email and password required'], 400);
        }
        
        // Add your login logic here
        // Example response:
        $this->sendResponse([
            'token' => 'example_token',
            'user' => ['id' => 1, 'email' => $data['email']]
        ]);
    }
    
    public function register() {
        $data = $this->getRequestData();
        // Add registration logic
        $this->sendResponse(['message' => 'User registered successfully'], 201);
    }
    
    public function logout() {
        // Add logout logic
        $this->sendResponse(['message' => 'Logged out successfully']);
    }
    
    // Image handlers
    public function getImage() {
        $params = $this->router->getParams();
        $imageId = $params['id'];
        // Add logic to fetch image by ID
        $this->sendResponse(['image' => ['id' => $imageId, 'url' => 'example.jpg']]);
    }
    
    public function getAllImages() {
        // Add pagination parameters
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        
        // Add logic to fetch images with pagination
        $this->sendResponse([
            'images' => [],
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => 0
            ]
        ]);
    }
    
    public function createImage() {
        $data = $this->getRequestData();
        // Add image creation logic
        $this->sendResponse(['message' => 'Image created successfully'], 201);
    }
    
    public function updateImage() {
        $params = $this->router->getParams();
        $data = $this->getRequestData();
        // Add image update logic
        $this->sendResponse(['message' => 'Image updated successfully']);
    }
    
    public function deleteImage() {
        $params = $this->router->getParams();
        // Add image deletion logic
        $this->sendResponse(['message' => 'Image deleted successfully']);
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        $handler = $this->router->matchRoute($method, $uri);
        
        if ($handler === null) {
            $this->sendResponse(['error' => 'Route not found'], 404);
            return;
        }
        
        try {
            call_user_func($handler);
        } catch (Exception $e) {
            $this->sendResponse(['error' => $e->getMessage()], 500);
        }
    }
}

// Initialize and run the API
$api = new Api();
$api->handleRequest();

?>