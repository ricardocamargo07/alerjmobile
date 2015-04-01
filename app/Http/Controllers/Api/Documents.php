<?php

namespace App\Http\Controllers\Api;

use App\Document as DocumentModel;
use App\DocumentPage;
use App\Http\Controllers\Controller;

class Documents extends Controller {

	public function pages($name)
	{
		$model = DocumentModel::where('name', $name)->first();

		return $model->pages;
	}

	public function page($id)
	{
		return DocumentPage::find($id);
	}

}
