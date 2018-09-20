<?php

namespace App\Services;

use DB;

class AdmUser
{
    public function getPermissions($username, $system)
    {
        return DB::connection('alerj-adm-user')
                ->select("exec sp_retorna_funcoes ?,?", array($username, $system));
    }

    public function getProfiles($username, $system)
    {
        return DB::connection('alerj-adm-user')
                 ->select("exec sp_consulta_perfil_sistema ?,?", array($username, $system));
    }
}
