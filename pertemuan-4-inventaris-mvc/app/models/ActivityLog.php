<?php
/**
 * Activity log (audit trail) model.
 */
class ActivityLog extends Model
{
    protected string $table = 'activity_logs';
    public function record(
        ?int $userId,
        string $action,
        ?string $entity = null,
        ?int $entityId = null,
        ?string $description = null
    ): void {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO activity_logs
                    (user_id, action, entity, entity_id, description, ip_address)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $userId,
                $action,
                $entity,
                $entityId,
                $description !== null ? mb_substr($description, 0, 255) : null,
                Security::clientIp(),
            ]);
        } catch (PDOException $e) {
            Logger::error('Activity log failed', ['error' => $e->getMessage()]);
        }
    }
     public function paginate(int $limit = 20, int $page = 1, string $action = ''): array
    {
        $limit = max(1, $limit);
        $offset = ($page - 1) * $limit;
        $where = [];
        $params = [];
        if ($action !== '') {
            $where[] = "a.action LIKE :action";
            $params['action'] = $action . '%';
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM activity_logs a {$whereSql}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();
        $sql = "SELECT a.*, u.username, u.full_name
                FROM activity_logs a
                LEFT JOIN users u ON a.user_id = u.id
                {$whereSql}
                ORDER BY a.id DESC
                LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        return [
            'data'         => $data,
            'current_page' => $page,
            'total_pages'  => (int) ceil($total / max($limit, 1)),
            'total_data'   => $total,
        ];
    }
}