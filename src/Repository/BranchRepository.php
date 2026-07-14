<?php

namespace ProGym\Repository;

use mysqli;

final class BranchRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    /** @return array<int,array{name:string,image_path:?string}> */
    public function allActive(): array
    {
        $res = $this->conn->query("SELECT name, image_path FROM branches WHERE is_active = 1 ORDER BY id");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countActive(): int
    {
        $res = $this->conn->query("SELECT COUNT(*) c FROM branches WHERE is_active = 1");
        return $res ? (int) $res->fetch_assoc()['c'] : 0;
    }
}
