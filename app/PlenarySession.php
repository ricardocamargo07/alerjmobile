<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlenarySession extends Model {

	protected $table = 'plenary_sessions';

	protected $fillable = ['date'];

}
