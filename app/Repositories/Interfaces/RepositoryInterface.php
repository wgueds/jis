<?php

namespace App\Repositories\Interfaces;

interface RepositoryInterface
{
    public function all();
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data): bool;
    public function delete($id): bool;
}