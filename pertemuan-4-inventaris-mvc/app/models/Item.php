<?php
class Item extends Model{
     public const LOW_STOCK_THRESHOLD = 5;
    protected string $table = 'items';

    public function all(): array{
        return $this->db->query(
            "SELECT i.*, c.name AS category_name FROM items i
            LEFT JOIN categories c ON i.category_id = c.id
            ORDER BY i.id DESC"
        )->fetchAll();
    }
    public function find(int $id){
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->excute([$id]);
        return $stmt->fetch();
    }
    public function generateCode(): string {
        $max = $this->db->query(
            "SELECT MAX(CAST(SUBSTRING(code, 4) AS UNSIGNED)) FROM items WHERE code LIKE 'ITM%'"
        )->fetchColumn();
        return 'ITM' . str_pad((string)((($max ? (int)$max : 0) + 1)), 4, '0', STR_PAD_LEFT);
    }
    public function create(array $data): bool{
        return $this->db->prepare(
            "INSERT INTO items (code, name, category_id, quantity, unit, price, entry_date, description)
            VALUES (:code, :name, :category_id, :quantity, :unit, :price, :entry_date, :description)"
        )->excute($data);
    }
    public function update(int $id, array $data): bool{
        $data['id'] = $id;
        return $this->db->prepare(
            "UPDATE items SET code = :code, name = :name, category_id = :category_id, quantity = :quantity,
            unit = :unit, price = :price, entry_date = :entry_date, description = :description WHERE id = :id"
        )->excute($data);
    }
    public function paginate(int $limit = 10, int $page = 1, string $search = '', string $categoryId = ''): array
    {
        $offset = ($page - 1) * $limit;
        $where = [];
        $params = [];
        if ($search !== '') {
            $where[] = "(i.code LIKE :search OR i.name LIKE :search OR c.name LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        if ($categoryId !== '') {
            $where[] = "i.category_id = :category_id";
            $params['category_id'] = (int) $categoryId;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $count = $this->db->prepare("SELECT COUNT(*) FROM items i
            LEFT JOIN categories c ON i.category_id = c.id {$whereSql}");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $stmt = $this->db->prepare("SELECT i.*, c.name AS category_name
            FROM items i LEFT JOIN categories c ON i.category_id = c.id
            {$whereSql} ORDER BY i.id DESC LIMIT {$limit} OFFSET {$offset}");
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
        return (int) $this->db->query("SELECT COUNT(*) FROM items")->fetchColumn();
    }

    public function totalStock(): int
    {
        return (int) $this->db->query("SELECT COALESCE(SUM(quantity), 0) FROM items")->fetchColumn();
    }

    public function totalAssetValue(): float
    {
        return (float) $this->db->query("SELECT COALESCE(SUM(quantity * price), 0) FROM items")->fetchColumn();
    }

    public function lowStockCount(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM items WHERE quantity <= :threshold");
        $stmt->execute(['threshold' => self::LOW_STOCK_THRESHOLD]);

        return (int) $stmt->fetchColumn();
    }

    public function lowStockItems(): array
    {
        $stmt = $this->db->prepare("SELECT i.*, c.name AS category_name
            FROM items i LEFT JOIN categories c ON i.category_id = c.id
            WHERE i.quantity <= :threshold ORDER BY i.quantity ASC, i.code ASC");
        $stmt->execute(['threshold' => self::LOW_STOCK_THRESHOLD]);

        return $stmt->fetchAll();
    }
}