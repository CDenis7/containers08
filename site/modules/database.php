<?php
class Database {
    private $db;

    public function __construct($path) {
        $this->db = new PDO("sqlite:$path");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function Execute($sql) {
        return $this->db->exec($sql);
    }

    public function Fetch($sql) {
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Create($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->db->lastInsertId();
    }

    public function Read($table, $id) {
        $sql = "SELECT * FROM $table WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function Update($table, $id, $data) {
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';
        
        $sql = "UPDATE $table SET $setClause WHERE id = ?";
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function Delete($table, $id) {
        $sql = "DELETE FROM $table WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function Count($table) {
        $sql = "SELECT COUNT(*) as count FROM $table";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }
}