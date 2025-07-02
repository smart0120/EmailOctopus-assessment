<?php

namespace App\Models;

use App\Database\Database;
use PDO;

class Contact
{
    private int $id = 0;
    private string $email_address = '';
    private string $name = '';
    private string $created_at = '';
    
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? 0;
            $this->email_address = $data['email_address'] ?? '';
            $this->name = $data['name'] ?? '';
            $this->created_at = $data['created_at'] ?? '';
        }
    }
    
    // Getters
    public function getId(): int { return $this->id; }
    public function getEmailAddress(): string { return $this->email_address; }
    public function getName(): string { return $this->name; }
    public function getCreatedAt(): string { return $this->created_at; }
    
    // Setters
    public function setEmailAddress(string $email): void { $this->email_address = $email; }
    public function setName(string $name): void { $this->name = $name; }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email_address' => $this->email_address,
            'name' => $this->name,
            'created_at' => $this->created_at
        ];
    }
    
    // Database operations
    public static function getAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM contact ORDER BY created_at DESC");
        $contacts = [];
        
        while ($row = $stmt->fetch()) {
            $contacts[] = new self($row);
        }
        
        return $contacts;
    }
    
    public static function findById(int $id): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM contact WHERE id = ?");
        $stmt->execute([$id]);
        
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }
    
    public static function findByEmail(string $email): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM contact WHERE email_address = ?");
        $stmt->execute([$email]);
        
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }
    
    public function save(): bool
    {
        $pdo = Database::getConnection();
        
        if ($this->id > 0) {
            // Update existing contact
            $stmt = $pdo->prepare("UPDATE contact SET email_address = ?, name = ? WHERE id = ?");
            return $stmt->execute([$this->email_address, $this->name, $this->id]);
        } else {
            // Insert new contact
            $stmt = $pdo->prepare("INSERT INTO contact (email_address, name) VALUES (?, ?)");
            if ($stmt->execute([$this->email_address, $this->name])) {
                $this->id = (int) $pdo->lastInsertId();
                return true;
            }
            return false;
        }
    }
    
    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM contact WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function validate(): array
    {
        $errors = [];
        
        if (empty($this->email_address)) {
            $errors[] = "Email address is required";
        } elseif (!filter_var($this->email_address, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address format";
        }
        
        if (empty($this->name)) {
            $errors[] = "Name is required";
        } elseif (strlen($this->name) > 200) {
            $errors[] = "Name must be 200 characters or less";
        }
        
        // Check for duplicate email (only for new contacts)
        if ($this->id === 0) {
            $existing = self::findByEmail($this->email_address);
            if ($existing) {
                $errors[] = "Email address already exists";
            }
        }
        
        return $errors;
    }
} 