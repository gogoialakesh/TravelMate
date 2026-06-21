<?php
/**
 * TravelMate - Expense Model
 */

class Expense
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByTrip(int $tripId): array
    {
        $stmt = $this->db->prepare(
            'SELECT e.*, u.full_name AS added_by_name, u.profile_photo AS added_by_photo
             FROM expenses e
             JOIN users u ON u.id = e.added_by
             WHERE e.trip_id = :trip_id
             ORDER BY e.expense_date DESC, e.created_at DESC'
        );
        $stmt->execute(['trip_id' => $tripId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM expenses WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO expenses (trip_id, added_by, title, description, amount, expense_date)
             VALUES (:trip_id, :added_by, :title, :description, :amount, :expense_date)'
        );
        $stmt->execute([
            'trip_id'      => $data['trip_id'],
            'added_by'     => $data['added_by'],
            'title'        => $data['title'],
            'description'  => $data['description']  ?? null,
            'amount'       => $data['amount'],
            'expense_date' => $data['expense_date']  ?? date('Y-m-d'),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getTotalByTrip(int $tripId): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE trip_id = ?'
        );
        $stmt->execute([$tripId]);
        return (float) $stmt->fetchColumn();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM expenses WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
