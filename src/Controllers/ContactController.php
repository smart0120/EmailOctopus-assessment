<?php

namespace App\Controllers;

use App\Models\Contact;

class ContactController
{
    public function index(): void
    {
        try {
            $contacts = Contact::getAll();
            $data = array_map(fn($contact) => $contact->toArray(), $contacts);
            
            $this->jsonResponse([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Failed to fetch contacts: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(): void
    {
        try {
            $input = $this->getJsonInput();
            
            if (!$input) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Invalid JSON input'
                ], 400);
                return;
            }
            
            $contact = new Contact();
            $contact->setEmailAddress($input['email_address'] ?? '');
            $contact->setName($input['name'] ?? '');
            
            $errors = $contact->validate();
            if (!empty($errors)) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $errors
                ], 400);
                return;
            }
            
            if ($contact->save()) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Contact added successfully',
                    'data' => $contact->toArray()
                ], 201);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Failed to save contact'
                ], 500);
            }
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Failed to create contact: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(int $id): void
    {
        try {
            $contact = Contact::findById($id);
            
            if (!$contact) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Contact not found'
                ], 404);
                return;
            }
            
            if (Contact::delete($id)) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Contact deleted successfully'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Failed to delete contact'
                ], 500);
            }
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => 'Failed to delete contact: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function getJsonInput(): ?array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        return json_last_error() === JSON_ERROR_NONE ? $data : null;
    }
    
    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
        
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
} 