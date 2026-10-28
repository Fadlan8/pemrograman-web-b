<?php

class Transaction extends Model
{
    protected string $table = 'transactions';

    public function create(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $this->db->prepare(
                "INSERT INTO transactions (item_id, user_id, type, quantity, notes)
                 VALUES (:item_id, :user_id, :type, :quantity, :notes)"
            )->execute([
                ':item_id' => $data['item_id'],
                ':user_id' => $data['user_id'],
                ':type' => $data['type'],
                ':quantity' => $data['quantity'],
                ':notes' => $data['notes'] ?? null,
            ]);

            $op = ($data['type'] === 'IN') ? '+' : '-';
            $this->db->prepare("UPDATE items SET quantity = quantity {$op} :quantity WHERE id = :item_id")
                ->execute([':quantity' => $data['quantity'], ':item_id' => $data['item_id']]);

            $check = $this->db->prepare("SELECT quantity FROM items WHERE id = :item_id");
            $check->execute([':item_id' => $data['item_id']]);
            if ((int) $check->fetchColumn() < 0) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            Logger::error('Transaksi gagal', ['message' => $e->getMessage()]);
            return false;
        }
    }

    public function paginate(int $limit = 10, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;
        $total = (int) $this->db->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
        $stmt = $this->db->prepare(
            "SELECT t.*, i.code AS item_code, i.name AS item_name, u.full_name AS user_name
             FROM transactions t
             LEFT JOIN items i ON t.item_id = i.id
             LEFT JOIN users u ON t.user_id = u.id
             ORDER BY t.created_at DESC, t.id DESC LIMIT {$limit} OFFSET {$offset}"
        );
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'current_page' => $page,
            'total_pages' => (int) ceil($total / max($limit, 1)),
            'total_data' => $total,
        ];
    }

    public function recent(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT t.*, i.name AS item_name FROM transactions t
             LEFT JOIN items i ON t.item_id = i.id
             ORDER BY t.created_at DESC, t.id DESC LIMIT {$limit}"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function todayCount(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM transactions WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    }
}
