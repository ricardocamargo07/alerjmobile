<?php

namespace App\Services;

use DB;
use PDO;

class AdmUser
{
    public function getPermissions($username, $system)
    {
        return DB::connection('alerj-adm-user')
                ->select("exec sp_retorna_funcoes ?,?", array($username,$system));
    }
}
