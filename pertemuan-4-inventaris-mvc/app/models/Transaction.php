<?php
class Transaction extends Model{
    protected string $table = 'transactions';

        public function all(): array {
            return $this->db->query(
                "SELECT t.*, i.name as item_name, i.code as item_code, u.full_name as user_name 
                FROM transactions t 
                JOIN items i ON t.item_id = i.id 
                JOIN users u ON t.user_id = u.id 
                ORDER BY t.created_at DESC"
            )->fetchAll();
        }
    public function create(array $data): bool {
        try {
            $this->db->beginTransaction();
            $this->db->prepare(
                "INSERT INTO transactions (item_id, user_id, type, quantity, notes)
                VALUES (:item_id, :user_id, :type, :quantity, :notes)"
            )->execute([
                ':item_id'  => $data['item_id'],
                ':user_id'  => $data['user_id'],
                ':type'     => $data['type'],       // 'IN' atau 'OUT'
                ':quantity' => $data['quantity'],
                ':notes'    => $data['notes'] ?? null,
            ]);
            //update stok barang
            $op = ($data['type'] === 'IN') ? '+' : '-';
            $this->db->prepare("UPDATE items SET quantity = quantity {$op} :quantity WHERE id = :item_id")
                    ->execute([':quantity' => $data['quantity'], ':item_id' => $data['item_id']]);
            $check = $this->db->prepare("SELECT quantity FROM items WHERE id = :item_id");
            $check->execute([':item_id' => $data['item_id']]);
            if ((int) $check->fetchColumn() < 0) { $this->db->rollBack(); return false; }
            //jika sukses simpan permanen
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            return false;
        }
    }
}
