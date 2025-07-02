<?php

// Simple API test script
// Run this after starting the server to test the endpoints

$baseUrl = 'http://localhost:8000/api';

echo "Testing Mailing List API\n";
echo "========================\n\n";

// Test 1: Get all contacts
echo "1. Testing GET /api/contacts\n";
$response = file_get_contents($baseUrl . '/contacts');
$data = json_decode($response, true);
echo "Status: " . (isset($data['success']) && $data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Contacts found: " . count($data['data'] ?? []) . "\n\n";

// Test 2: Add a new contact
echo "2. Testing POST /api/contacts\n";
$postData = json_encode([
    'name' => 'Test User',
    'email_address' => 'test@example.com'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $postData
    ]
]);

$response = file_get_contents($baseUrl . '/contacts', false, $context);
$data = json_decode($response, true);
echo "Status: " . (isset($data['success']) && $data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
if (isset($data['message'])) {
    echo "Message: " . $data['message'] . "\n";
}
echo "\n";

// Test 3: Get contacts again to see the new one
echo "3. Testing GET /api/contacts (after adding)\n";
$response = file_get_contents($baseUrl . '/contacts');
$data = json_decode($response, true);
echo "Status: " . (isset($data['success']) && $data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
echo "Contacts found: " . count($data['data'] ?? []) . "\n\n";

echo "API tests completed!\n";
echo "You can now visit http://localhost:8000 to use the web interface.\n"; 