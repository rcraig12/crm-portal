<?php
/**
 * Activity Model
 *
 * Handles activity-related database operations
 */

class Activity {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    /**
     * Get all activities with optional filters
     *
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($filters = [], $limit = null, $offset = 0) {
        $query = "SELECT a.*,
                  CONCAT(c.first_name, ' ', c.last_name) as contact_name,
                  co.name as company_name,
                  d.title as deal_title,
                  CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name
                  FROM activities a
                  LEFT JOIN contacts c ON a.contact_id = c.id
                  LEFT JOIN companies co ON a.company_id = co.id
                  LEFT JOIN deals d ON a.deal_id = d.id
                  LEFT JOIN users u ON a.assigned_to = u.id
                  WHERE 1=1";

        $params = [];

        if (!empty($filters['type'])) {
            $query .= " AND a.type = :type";
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['status'])) {
            $query .= " AND a.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['contact_id'])) {
            $query .= " AND a.contact_id = :contact_id";
            $params[':contact_id'] = $filters['contact_id'];
        }

        if (!empty($filters['assigned_to'])) {
            $query .= " AND a.assigned_to = :assigned_to";
            $params[':assigned_to'] = $filters['assigned_to'];
        }

        $query .= " ORDER BY a.scheduled_at DESC, a.created_at DESC";

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
     * Get activity by ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT a.*,
                  CONCAT(c.first_name, ' ', c.last_name) as contact_name,
                  co.name as company_name,
                  d.title as deal_title,
                  CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name
                  FROM activities a
                  LEFT JOIN contacts c ON a.contact_id = c.id
                  LEFT JOIN companies co ON a.company_id = co.id
                  LEFT JOIN deals d ON a.deal_id = d.id
                  LEFT JOIN users u ON a.assigned_to = u.id
                  WHERE a.id = :id LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create new activity
     *
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $query = "INSERT INTO activities (type, subject, description, contact_id, company_id,
                  deal_id, scheduled_at, status, created_by, assigned_to)
                  VALUES (:type, :subject, :description, :contact_id, :company_id,
                  :deal_id, :scheduled_at, :status, :created_by, :assigned_to)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':subject', $data['subject']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':contact_id', $data['contact_id']);
        $stmt->bindParam(':company_id', $data['company_id']);
        $stmt->bindParam(':deal_id', $data['deal_id']);
        $stmt->bindParam(':scheduled_at', $data['scheduled_at']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':created_by', $data['created_by']);
        $stmt->bindParam(':assigned_to', $data['assigned_to']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update activity
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE activities SET
                  type = :type,
                  subject = :subject,
                  description = :description,
                  contact_id = :contact_id,
                  company_id = :company_id,
                  deal_id = :deal_id,
                  scheduled_at = :scheduled_at,
                  status = :status,
                  assigned_to = :assigned_to";

        if (!empty($data['completed_at']) && $data['status'] === 'completed') {
            $query .= ", completed_at = :completed_at";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':subject', $data['subject']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':contact_id', $data['contact_id']);
        $stmt->bindParam(':company_id', $data['company_id']);
        $stmt->bindParam(':deal_id', $data['deal_id']);
        $stmt->bindParam(':scheduled_at', $data['scheduled_at']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':assigned_to', $data['assigned_to']);

        if (!empty($data['completed_at']) && $data['status'] === 'completed') {
            $stmt->bindParam(':completed_at', $data['completed_at']);
        }

        return $stmt->execute();
    }

    /**
     * Delete activity
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM activities WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Count activities
     *
     * @param array $filters
     * @return int
     */
    public function count($filters = []) {
        $query = "SELECT COUNT(*) as total FROM activities WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $query .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['contact_id'])) {
            $query .= " AND contact_id = :contact_id";
            $params[':contact_id'] = $filters['contact_id'];
        }

        $stmt = $this->db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
