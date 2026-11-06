<?php

class Item extends Model
{
    use SoftDeletes;
    protected string $table = 'items';

    public function all(): array
    {
        return $this->db->query(
            "SELECT i.*, c.name AS category_name FROM items i LEFT JOIN categories c ON i.category_id = c.id WHERE i.deleted_at IS NULL ORDER BY i.id DESC"
        )->fetchAll();
    }
    public function search(string $keyword): array
    {
        $stmt = $this->db->prepare("SELECT i.*, c.name AS category_name FROM items i LEFT JOIN categories c ON i.category_id = c.id WHERE i.deleted_at IS NULL AND (i.code LIKE ? OR i.name LIKE ?) ORDER BY i.id DESC");
        $keyword = '%' . $keyword . '%';
        $stmt->execute([$keyword, $keyword]);
        return $stmt->fetchAll();
    }

    public function find(int $id, bool $withTrashed = false)
    {
        $sql = "SELECT i.*, c.name AS category_name
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                WHERE i.id = ?";
        $params = [$id];
        if (!$withTrashed) {
            $sql .= " AND i.deleted_at IS NULL";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function create(array $data): bool
    {
        return $this->db->prepare(
            "INSERT INTO items (code, name, category_id, quantity, unit, price, entry_date, description, photo)
             VALUES (:code, :name, :category_id, :quantity, :unit, :price, :entry_date, :description, :photo)"
        )->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $data['id'] = $id;
        return $this->db->prepare(
            "UPDATE items SET code = :code, name = :name, category_id = :category_id, quantity = :quantity,
             unit = :unit, price = :price, entry_date = :entry_date, description = :description, photo = :photo WHERE id = :id"
        )->execute($data);
    }

    public function generateCode(): string
    {
        $max = $this->db->query(
            "SELECT MAX(CAST(SUBSTRING(code, 4) AS UNSIGNED)) FROM items WHERE code LIKE 'ITM%'"
        )->fetchColumn();
        return 'ITM' . str_pad((string) (((int) $max) + 1), 4, '0', STR_PAD_LEFT);
    }

    public function paginate(int $limit = 10, int $page = 1, string $search = '', string $categoryId = '', bool $trashed = false): array
    {
        $offset = ($page - 1) * $limit;
        $where = [$trashed ? "i.deleted_at IS NOT NULL" : "i.deleted_at IS NULL"];
        $params = [];

        if ($search !== '') {
            $where[] = "(i.code LIKE :search OR i.name LIKE :search OR c.name LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        if ($categoryId !== '') {
            $where[] = "i.category_id = :category_id";
            $params['category_id'] = (int) $categoryId;
        }
         $whereSql = 'WHERE ' . implode(' AND ', $where);

        $count = $this->db->prepare("SELECT COUNT(*) FROM items i LEFT JOIN categories c ON i.category_id = c.id {$whereSql}");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT i.*, c.name AS category_name FROM items i LEFT JOIN categories c ON i.category_id = c.id
             {$whereSql} ORDER BY i.id DESC LIMIT {$limit} OFFSET {$offset}"
        );
        $stmt->execute($params);

        return [
            'data' => $stmt->fetchAll(),
            'current_page' => $page,
            'total_pages' => (int) ceil($total / max($limit, 1)),
            'total_data' => $total,
        ];
    }

    public function totalItems(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM items WHERE deleted_at IS NULL")->fetchColumn();
    }

    public function totalStock(): int
    {
        return (int) $this->db->query("SELECT COALESCE(SUM(quantity), 0) FROM items WHERE deleted_at IS NULL")->fetchColumn();
    }

    public function totalAssetValue(): float
    {
        return (float) $this->db->query("SELECT COALESCE(SUM(quantity * price), 0) FROM items WHERE deleted_at IS NULL")->fetchColumn();
    }

    public function lowStockCount(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM items WHERE quantity <= 5 AND deleted_at IS NULL")->fetchColumn();
    }

    public function lowStockItems(): array
    {
        return $this->db->query(
            "SELECT i.*, c.name AS category_name FROM items i LEFT JOIN categories c ON i.category_id = c.id
             WHERE i.quantity <= 5 AND i.deleted_at IS NULL ORDER BY i.quantity ASC LIMIT 10"
        )->fetchAll();
    }
    public function report(array $filters = []): array
    {
        $where = ["i.deleted_at IS NULL"];
        $params = [];
        if (!empty($filters['search'])) {
            $where[] = "(i.code LIKE :search OR i.name LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['category'])) {
            $where[] = "i.category_id = :category_id";
            $params['category_id'] = (int) $filters['category'];
        }
        if (!empty($filters['low_stock'])) {
            $where[] = "i.quantity <= :threshold";
            $params['threshold'] = (int) ($filters['threshold'] ?? 5);
        }
        $sql = "SELECT i.code, i.name, c.name AS category_name, i.quantity, i.unit,
                    i.price, i.entry_date
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY i.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function topStock(int $limit = 5): array
    {
        $sql = "SELECT name, quantity
            FROM items
            WHERE deleted_at IS NULL
            ORDER BY quantity DESC
            LIMIT {$limit}";
        return $this->db->query($sql)->fetchAll();
    }
}
