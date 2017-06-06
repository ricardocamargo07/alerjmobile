<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Data\Models\Document as DocumentModel;
use App\Data\Models\DocumentPage as DocumentPageModel;

class Documents extends Controller
{
    private function guessShowPage($showPage)
    {
        $showPage = strtolower($showPage);

        return $showPage == 'yes' || $showPage == 'true' || $showPage == 'sim';
    }

    public function pages($name, $showPage = 'yes')
	{
        $showPage = $this->guessShowPage($showPage);

        if ( ! $model = DocumentModel::where('name', $name)->first())
        {
            return $this->respondWithError('documento não encontrado');
        }

		$page = new DocumentPageModel();

        $exceptFields = ! $showPage ? 'page' : 'allfieldswillbeshown';

		$columns = array_except(array_combine($page->getFillable(), $page->getFillable()), $exceptFields);

		return $this->response(
			DocumentPageModel::select(['id'] + $columns)
				->where('document_id', $model->id)
				->orderBy('id')
				->get()
		);
	}

	public function page($id)
	{
		if (! $document = DocumentPageModel::find($id)) {
		    return [];
        }

        $document = $document->toArray();

		$document['page'] = $this->removeUneededLinks($document['page']);

        return response()->json($document);
	}
}
