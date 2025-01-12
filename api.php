<?php

function handleRequest() {
    $method = $_SERVER['REQUEST_METHOD'];
    $response = [];

    switch ($method) {
        case 'GET':
            $response = handleGet();
            break;
        case 'POST':
            $response = handlePost();
            break;
        case 'PUT':
            $response = handlePut();
            break;
        case 'DELETE':
            $response = handleDelete();
            break;
        default:
            http_response_code(405);
            $response = ['message' => 'Method Not Allowed'];
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

function handleGet() {
    // Logic for GET request
    return ['message' => 'GET request handled'];
}

function handlePost() {
    // Logic for POST request
    return ['message' => 'POST request handled'];
}

function handlePut() {
    // Logic for PUT request
    return ['message' => 'PUT request handled'];
}

function handleDelete() {
    // Logic for DELETE request
    return ['message' => 'DELETE request handled'];
}

handleRequest();

?>