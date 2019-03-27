<?php

namespace App\Http\Controllers\Api;

use App\Data\Models\BillProject;
use App\Http\Controllers\Controller;

class BillProjects extends Controller
{
    public function all()
    {
        $output = request()->get('output', 'csv');

        $projects = BillProject::orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('number', 'desc')
            ->get(BillProject::getColumns());

        if ($output === 'csv') {
            return $this->downloadCsv($projects);
        }

        return $this->projects->toJson();
    }

    private function downloadCsv($projects)
    {
        $projects->prepend(
            collect($projects[0])
                ->keys()
                ->implode(';')
        );

        return $this->streamDownload(
            $projects
                ->map(function ($row) {
                    return $this->sanitize(collect($row)->implode(';'));
                })

                ->implode("\n"),
            'projetos-de-lei.csv'
        );
    }

    private function sanitize($string)
    {
        return str_replace("\n", '', str_replace("\r", '', $string));
    }

    protected function streamDownload($contents, $file)
    {
        return response($contents, 200, [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $file . '"',
        ]);
    }
}
