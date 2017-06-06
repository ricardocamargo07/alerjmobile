<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentPage extends Model
{
	protected $table = 'documents_pages';

	protected $fillable = ['document_id', 'position', 'level', 'alerj_id', 'title', 'page', 'has_content'];

	public function document()
	{
		return $this->belongsTo(Document::class);
	}
}
