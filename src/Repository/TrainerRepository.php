<?php

namespace ProGym\Repository;

use mysqli;

final class TrainerRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    /**
     * Active trainers shaped for the showcase card (name/role/tag/bg).
     * The photo path is turned into a page-relative CSS url().
     */
    public function allActiveForShowcase(): array
    {
        $res = $this->conn->query(
            "SELECT name, role, specialty_tag, photo_path FROM trainers WHERE is_active = 1 ORDER BY id"
        );
        if (!$res) {
            return [];
        }
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $photo = $row['photo_path'] ?? '';
            $out[] = [
                'name' => $row['name'],
                'role' => $row['role'],
                'tag'  => $row['specialty_tag'] ?? '',
                'bg'   => $photo !== '' ? "url('../" . $photo . "')" : 'none',
            ];
        }
        return $out;
    }

    public function countActive(): int
    {
        $res = $this->conn->query("SELECT COUNT(*) c FROM trainers WHERE is_active = 1");
        return $res ? (int) $res->fetch_assoc()['c'] : 0;
    }
}
