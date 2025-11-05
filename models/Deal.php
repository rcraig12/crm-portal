<?php
/**
 * Deal Model
 *
 * Handles deal/opportunity-related database operations
 */

class Deal {
    private $db;

    public function __construct($database) {
        $this->db = $database->connect();
    }

    /**
     * Get all deals with optional filters
     *
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($filters = [], $limit = null, $offset = 0) {
        $query = "SELECT d.*,
                  CONCAT(c.first_name, ' ', c.last_name) as contact_name,
                  co.name as company_name,
                  CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name
                  FROM deals d
                  LEFT JOIN contacts c ON d.contact_id = c.id
                  LEFT JOIN companies co ON d.company_id = co.id
                  LEFT JOIN users u ON d.assigned_to = u.id
                  WHERE 1=1";

        $params = [];

        if (!empty($filters['stage'])) {
            $query .= " AND d.stage = :stage";
            $params[':stage'] = $filters['stage'];
        }

        if (!empty($filters['assigned_to'])) {
            $query .= " AND d.assigned_to = :assigned_to";
            $params[':assigned_to'] = $filters['assigned_to'];
        }

        $query .= " ORDER BY d.created_at DESC";

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
     * Get deal by ID
     *
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT d.*,
                  CONCAT(c.first_name, ' ', c.last_name) as contact_name,
                  co.name as company_name,
                  CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name
                  FROM deals d
                  LEFT JOIN contacts c ON d.contact_id = c.id
                  LEFT JOIN companies co ON d.company_id = co.id
                  LEFT JOIN users u ON d.assigned_to = u.id
                  WHERE d.id = :id LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Create new deal
     *
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        $query = "INSERT INTO deals (title, description, value, probability, stage,
                  expected_close_date, contact_id, company_id, created_by, assigned_to)
                  VALUES (:title, :description, :value, :probability, :stage,
                  :expected_close_date, :contact_id, :company_id, :created_by, :assigned_to)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':value', $data['value']);
        $stmt->bindParam(':probability', $data['probability']);
        $stmt->bindParam(':stage', $data['stage']);
        $stmt->bindParam(':expected_close_date', $data['expected_close_date']);
        $stmt->bindParam(':contact_id', $data['contact_id']);
        $stmt->bindParam(':company_id', $data['company_id']);
        $stmt->bindParam(':created_by', $data['created_by']);
        $stmt->bindParam(':assigned_to', $data['assigned_to']);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update deal
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE deals SET
                  title = :title,
                  description = :description,
                  value = :value,
                  probability = :probability,
                  stage = :stage,
                  expected_close_date = :expected_close_date,
                  contact_id = :contact_id,
                  company_id = :company_id,
                  assigned_to = :assigned_to
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':value', $data['value']);
        $stmt->bindParam(':probability', $data['probability']);
        $stmt->bindParam(':stage', $data['stage']);
        $stmt->bindParam(':expected_close_date', $data['expected_close_date']);
        $stmt->bindParam(':contact_id', $data['contact_id']);
        $stmt->bindParam(':company_id', $data['company_id']);
        $stmt->bindParam(':assigned_to', $data['assigned_to']);

        return $stmt->execute();
    }

    /**
     * Delete deal
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM deals WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Count total deals
     *
     * @param array $filters
     * @return int
     */
    public function count($filters = []) {
        $query = "SELECT COUNT(*) as total FROM deals WHERE 1=1";
        $params = [];

        if (!empty($filters['stage'])) {
            $query .= " AND stage = :stage";
            $params[':stage'] = $filters['stage'];
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
     * Get total value by stage
     *
     * @return array
     */
    public function getValueByStage() {
        $query = "SELECT stage, SUM(value) as total_value FROM deals GROUP BY stage";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Count deals by stage
     *
     * @return array
     */
    public function countByStage() {
        $query = "SELECT stage, COUNT(*) as count FROM deals GROUP BY stage";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
