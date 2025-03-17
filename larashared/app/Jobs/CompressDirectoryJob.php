<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ZipArchive;

class CompressDirectoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // The full path of the directory to compress
    protected $directory;

    /**
     * Create a new job instance.
     *
     * @param string $directory The absolute path of the directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Execute the job.
     *
     * This will compress all contents of the provided directory
     * into an archive.zip file located inside the same directory.
     *
     * @return void
     */
    public function handle()
    {
        $zip = new ZipArchive;
        $zipFileName = $this->directory . DIRECTORY_SEPARATOR . 'archive.zip';

        // Open the zip file or create a new one
        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Use RecursiveIterator to add all files and subdirectories
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->directory),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                // Skip directories; only add files
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // Create a relative path so that the structure is preserved in the zip file
                    $relativePath = substr($filePath, strlen($this->directory) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }
    }
}
