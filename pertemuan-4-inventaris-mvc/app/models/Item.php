<?php
public function generateCode(): string {
    $max = $this->db->query(
        "SELECT MAX(CAST(SUBSTRING(code, 4) AS UNSIGNED)) FROM items WHERE code LIKE 'ITM%'"
    )->fetchColumn();
    return 'ITM' . str_pad((string)((($max ? (int)$max : 0) + 1)), 4, '0', STR_PAD_LEFT);
}