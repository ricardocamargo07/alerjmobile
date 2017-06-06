<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model {

	protected $table = 'documents';

	protected $fillable = ['name', 'base_url'];

	public function pages()
	{
		return $this->hasMany('App\DocumentPage');
	}

}
