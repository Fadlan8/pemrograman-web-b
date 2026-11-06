<?php

class Transaction extends Model
{
    protected string $table = 'transactions';

    public function create(array $data): bool
    {
        try {
            $itemCheck = $this->db->prepare(
            "SELECT id FROM items WHERE id = ? AND deleted_at IS NULL LIMIT 1"
            );
            $itemCheck->execute([(int) $data['item_id']]);

            if (!$itemCheck->fetchColumn()) {
            return false; // Batalkan transaksi jika barang tidak ditemukan atau sudah dihapus
            }
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

    public function paginate(int $limit = 10, int $page = 1, array $filters = []): array
    {
        $offset = ($page - 1) * $limit;

        // 1. Siapkan array untuk menampung filter
        $where = [];
        $params = [];
        
        if (!empty($filters['type']) && in_array($filters['type'], ['IN', 'OUT'], true)) {
            $where[] = "t.type = :type";
            $params['type'] = $filters['type'];
        }
        
        if (!empty($filters['from'])) {
            $where[] = "t.created_at >= :from";
            $params['from'] = $filters['from'] . ' 00:00:00';
        }
        
        if (!empty($filters['to'])) {
            $where[] = "t.created_at <= :to";
            $params['to'] = $filters['to'] . ' 23:59:59';
        }
        
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // 2. Ganti penghitungan $total dengan query yang mendeteksi filter
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM transactions t {$whereSql}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // 3. Tambahkan {$whereSql} ke dalam query utama dan jalankan execute dengan $params
        $stmt = $this->db->prepare(
            "SELECT t.*, i.code AS item_code, i.name AS item_name, u.full_name AS user_name
             FROM transactions t
             LEFT JOIN items i ON t.item_id = i.id
             LEFT JOIN users u ON t.user_id = u.id
            {$whereSql}
             ORDER BY t.created_at DESC, t.id DESC LIMIT {$limit} OFFSET {$offset}"
        );
        $stmt->execute($params);

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
  /**
     * Transaction history for one item (detail page).
     */
     public function forItem(int $itemId, int $limit = 20): array
    {
        $sql = "SELECT t.*, u.full_name AS user_name
                FROM transactions t
                LEFT JOIN users u ON t.user_id = u.id
                WHERE t.item_id = :item_id
                ORDER BY t.created_at DESC, t.id DESC
                LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);
        return $stmt->fetchAll();
    }

    public function report(string $from = '', string $to = '', string $type = ''): array
    {
        $where = [];
        $params = [];
        if ($from !== '') {
            $where[] = "t.created_at >= :from";
            $params['from'] = $from . ' 00:00:00';
        }
        if ($to !== '') {
            $where[] = "t.created_at <= :to";
            $params['to'] = $to . ' 23:59:59';
        }
        if (in_array($type, ['IN', 'OUT'], true)) {
            $where[] = "t.type = :type";
            $params['type'] = $type;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT t.created_at, i.code AS item_code, i.name AS item_name,
                    t.type, t.quantity, u.full_name AS user_name, t.notes
                FROM transactions t
                LEFT JOIN items i ON t.item_id = i.id
                LEFT JOIN users u ON t.user_id = u.id
                {$whereSql}
                ORDER BY t.created_at ASC, t.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function summary(string $from = '', string $to = ''): array
    {
        $where = [];
        $params = [];
        if ($from !== '') {
            $where[] = "created_at >= :from";
            $params['from'] = $from . ' 00:00:00';
        }
        if ($to !== '') {
            $where[] = "created_at <= :to";
            $params['to'] = $to . ' 23:59:59';
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN type = 'IN'  THEN quantity ELSE 0 END), 0) AS total_in,
                    COALESCE(SUM(CASE WHEN type = 'OUT' THEN quantity ELSE 0 END), 0) AS total_out,
                    COUNT(*) AS total_rows
                FROM transactions {$whereSql}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return [
            'total_in'   => (int) ($row['total_in'] ?? 0),
            'total_out'  => (int) ($row['total_out'] ?? 0),
            'total_rows' => (int) ($row['total_rows'] ?? 0),
        ];
    }

    public function dailyTotals(int $days = 7): array
    {
        $days = max(1, min(30, $days));
        $sql = "SELECT DATE(created_at) AS day,
                SUM(CASE WHEN type = 'IN' THEN quantity ELSE 0 END) AS total_in,
                SUM(CASE WHEN type = 'OUT' THEN quantity ELSE 0 END) AS total_out
            FROM transactions
            WHERE created_at >= (CURDATE() - INTERVAL {$days} DAY)
            GROUP BY DATE(created_at)
            ORDER BY day ASC";
        $rows = $this->db->query($sql)->fetchAll();

        $byDay = [];
        foreach ($rows as $row) {
            $byDay[(string) $row['day']] = $row;
        }
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $timestamp = strtotime("-{$i} day");
            $date = date('Y-m-d', $timestamp);
            $result[] = [
                'label' => date('d/m', $timestamp),
                'in' => (int) ($byDay[$date]['total_in'] ?? 0),
                'out' => (int) ($byDay[$date]['total_out'] ?? 0),
                ];
        }
        return $result;

    }
}
