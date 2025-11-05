<?php
/**
 * Company Model
 *
 * Handles company-related database operations
 */

class Company {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    /**
     * Get all companies
     *
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($filters = [], $limit = null, $offset = 0) {
        $query = "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                  FROM companies c
                  LEFT JOIN users u ON c.created_by = u.id
                  WHERE 1=1";

        $params = [];

        if (!empty($filters['search'])) {
            $query .= " AND (c.name LIKE :search OR c.industry LIKE :search OR c.email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $query .= " ORDER BY c.created_at DESC";

        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get company by ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                  FROM companies c
                  LEFT JOIN users u ON c.created_by = u.id
                  WHERE c.id = :id LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create new company
     *
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $query = "INSERT INTO companies (name, industry, website, phone, email, address,
                  city, state, country, postal_code, notes, created_by)
                  VALUES (:name, :industry, :website, :phone, :email, :address,
                  :city, :state, :country, :postal_code, :notes, :created_by)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':industry', $data['industry']);
        $stmt->bindParam(':website', $data['website']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':state', $data['state']);
        $stmt->bindParam(':country', $data['country']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':created_by', $data['created_by']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update company
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE companies SET
                  name = :name,
                  industry = :industry,
                  website = :website,
                  phone = :phone,
                  email = :email,
                  address = :address,
                  city = :city,
                  state = :state,
                  country = :country,
                  postal_code = :postal_code,
                  notes = :notes
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':industry', $data['industry']);
        $stmt->bindParam(':website', $data['website']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':state', $data['state']);
        $stmt->bindParam(':country', $data['country']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':notes', $data['notes']);

        return $stmt->execute();
    }

    /**
     * Delete company
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM companies WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Count total companies
     *
     * @param array $filters
     * @return int
     */
    public function count($filters = []) {
        $query = "SELECT COUNT(*) as total FROM companies WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $query .= " AND (name LIKE :search OR industry LIKE :search OR email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Get companies for select dropdown
     *
     * @return array
     */
    public function getForSelect() {
        $query = "SELECT id, name FROM companies ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
