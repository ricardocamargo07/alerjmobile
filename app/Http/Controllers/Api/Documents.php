<?php

namespace App\Http\Controllers\Api;

use App\Document as DocumentModel;
use App\DocumentPage;
use App\Http\Controllers\Controller;

class Documents extends Controller
{
	public function pages($name)
	{
		$model = DocumentModel::where('name', $name)->first();

		$page = new DocumentPage();

		$columns = array_except(array_combine($page->getFillable(), $page->getFillable()), 'page');

		return $this->response(
			DocumentPage::select(['id'] + $columns)
				->where('document_id', $model->id)
				->orderBy('id')
				->get()
		);
	}

	public function page($id)
	{
		$document = DocumentPage::find($id)->toArray();

		$document['page'] = $this->removeUneededLinks($document['page']);

		return response()->json($document);
	}
}
