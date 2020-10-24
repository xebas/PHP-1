<?php

namespace App\Models;

use Core\Model;
use PDO;

class HomeModel extends Model
{
    private $db;

    public function __construct()
    {
        $this->db = Model::getInstanceDB();
    }
}
