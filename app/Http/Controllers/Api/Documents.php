<?php

namespace App\Http\Controllers\Api;

use App\Document as DocumentModel;
use App\DocumentPage;
use App\Http\Controllers\Controller;

class Documents extends Controller {

	public function pages($name)
	{
		$model = DocumentModel::where('name', $name)->first();

		$page = new DocumentPage();

		$columns = array_except(array_combine($page->getFillable(), $page->getFillable()), 'page');

		return
			DocumentPage::select(['id'] + $columns)
				->where('document_id', $model->id)
				->orderBy('id')
				->get();
	}

	public function page($id)
	{
		return DocumentPage::find($id);
	}

}
