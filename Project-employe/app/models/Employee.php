<?php

class Employee extends Model
{
    public function all(): array
    {
        $stmt = $this->db->prepare("SELECT id, nip, name, department, position, salary FROM employees ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT id, nip, name, department, position, salary FROM employees WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function nipExists(string $nip, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM employees WHERE nip = :nip AND id != :id");
            $stmt->execute([':nip' => $nip, ':id' => $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM employees WHERE nip = :nip");
            $stmt->execute([':nip' => $nip]);
        }

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO employees (nip, name, department, position, salary)
                    VALUES (:nip, :name, :department, :position, :salary)";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':nip'        => $data['nip'],
                ':name'       => $data['name'],
                ':department' => $data['department'],
                ':position'   => $data['position'],
                ':salary'     => $data['salary'],
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE employees
                    SET nip = :nip,
                        name = :name,
                        department = :department,
                        position = :position,
                        salary = :salary
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':nip'        => $data['nip'],
                ':name'       => $data['name'],
                ':department' => $data['department'],
                ':position'   => $data['position'],
                ':salary'     => $data['salary'],
                ':id'         => $id,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM employees WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}