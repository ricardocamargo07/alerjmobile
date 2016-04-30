<?php

namespace App\Http\Controllers;

use App\Services\DownloadFromPortal;

class Backup extends Controller
{
    /**
     * @var DownloadFromPortal
     */
    private $downloader;

    public function __construct(DownloadFromPortal $downloader)
    {
        $this->downloader = $downloader;
    }

    public function execute()
    {
        $this->downloader->execute();

        return 'done.';
    }
}
