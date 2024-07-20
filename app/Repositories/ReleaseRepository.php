<?php

namespace App\Repositories;

use App\Models\Release;
use App\Repositories\Repository;

class ReleaseRepository extends Repository
{
    public function __construct(Release $release)
    {
        parent::__construct($release);
    }
}