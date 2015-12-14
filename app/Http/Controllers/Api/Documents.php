<?php

namespace App\Http\Controllers\Api;

use App\DocumentPage;
use App\Document as DocumentModel;
use App\Http\Controllers\Controller;

class Documents extends Controller
{
	public function pages($name)
	{
        if ( ! $model = DocumentModel::where('name', $name)->first())
        {
            return $this->respondWithError('documento não encontrado');
        }

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
