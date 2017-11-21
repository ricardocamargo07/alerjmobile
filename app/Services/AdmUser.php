<?php

namespace App\Services;

use DB;

class AdmUser
{
    public function getPermissions($username, $systemName)
    {
        return DB::connection('alerj-adm-user')
                ->select("exec sp_retorna_funcoes ?,?",array($username,$systemName));
    }
}
