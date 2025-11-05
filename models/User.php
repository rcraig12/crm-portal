<?php
/**
 * User Model
 *
 * Handles user-related database operations
 */

class User {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    /**
     * Authenticate user
     *
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function authenticate($username, $password) {
        $query = "SELECT * FROM users WHERE (username = :username OR email = :username) AND status = 'active' LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Get user by ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT id, username, email, first_name, last_name, role, status, created_at
                  FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get all users
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($limit = null, $offset = 0) {
        $query = "SELECT id, username, email, first_name, last_name, role, status, created_at
                  FROM users ORDER BY created_at DESC";

        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($query);

        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create new user
     *
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $query = "INSERT INTO users (username, email, password, first_name, last_name, role, status)
                  VALUES (:username, :email, :password, :first_name, :last_name, :role, :status)";

        $stmt = $this->db->prepare($query);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':status', $data['status']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE users SET
                  username = :username,
                  email = :email,
                  first_name = :first_name,
                  last_name = :last_name,
                  role = :role,
                  status = :status";

        if (!empty($data['password'])) {
            $query .= ", password = :password";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':status', $data['status']);

        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword);
        }

        return $stmt->execute();
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Count total users
     *
     * @return int
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
