<?php
/**
 * Contact Model
 *
 * Handles contact-related database operations
 */

class Contact {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    /**
     * Get all contacts with optional filters
     *
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($filters = [], $limit = null, $offset = 0) {
        $query = "SELECT c.*, co.name as company_name,
                  CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name
                  FROM contacts c
                  LEFT JOIN companies co ON c.company_id = co.id
                  LEFT JOIN users u ON c.assigned_to = u.id
                  WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $query .= " AND c.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $query .= " AND (c.first_name LIKE :search OR c.last_name LIKE :search OR c.email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['assigned_to'])) {
            $query .= " AND c.assigned_to = :assigned_to";
            $params[':assigned_to'] = $filters['assigned_to'];
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
     * Get contact by ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT c.*, co.name as company_name,
                  CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name,
                  CONCAT(creator.first_name, ' ', creator.last_name) as created_by_name
                  FROM contacts c
                  LEFT JOIN companies co ON c.company_id = co.id
                  LEFT JOIN users u ON c.assigned_to = u.id
                  LEFT JOIN users creator ON c.created_by = creator.id
                  WHERE c.id = :id LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create new contact
     *
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $query = "INSERT INTO contacts (first_name, last_name, email, phone, mobile, position,
                  company_id, address, city, state, country, postal_code, status, source,
                  notes, created_by, assigned_to)
                  VALUES (:first_name, :last_name, :email, :phone, :mobile, :position,
                  :company_id, :address, :city, :state, :country, :postal_code, :status,
                  :source, :notes, :created_by, :assigned_to)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':mobile', $data['mobile']);
        $stmt->bindParam(':position', $data['position']);
        $stmt->bindParam(':company_id', $data['company_id']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':state', $data['state']);
        $stmt->bindParam(':country', $data['country']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':source', $data['source']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':created_by', $data['created_by']);
        $stmt->bindParam(':assigned_to', $data['assigned_to']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update contact
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE contacts SET
                  first_name = :first_name,
                  last_name = :last_name,
                  email = :email,
                  phone = :phone,
                  mobile = :mobile,
                  position = :position,
                  company_id = :company_id,
                  address = :address,
                  city = :city,
                  state = :state,
                  country = :country,
                  postal_code = :postal_code,
                  status = :status,
                  source = :source,
                  notes = :notes,
                  assigned_to = :assigned_to
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':mobile', $data['mobile']);
        $stmt->bindParam(':position', $data['position']);
        $stmt->bindParam(':company_id', $data['company_id']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':state', $data['state']);
        $stmt->bindParam(':country', $data['country']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':source', $data['source']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':assigned_to', $data['assigned_to']);

        return $stmt->execute();
    }

    /**
     * Delete contact
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM contacts WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Count total contacts
     *
     * @param array $filters
     * @return int
     */
    public function count($filters = []) {
        $query = "SELECT COUNT(*) as total FROM contacts WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $query .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['assigned_to'])) {
            $query .= " AND assigned_to = :assigned_to";
            $params[':assigned_to'] = $filters['assigned_to'];
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
     * Count contacts by status
     *
     * @return array
     */
    public function countByStatus() {
        $query = "SELECT status, COUNT(*) as count FROM contacts GROUP BY status";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
