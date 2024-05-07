<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\AdminTasksResearchesPresented;
use App\Logs;
use Carbon\Carbon;
use Auth;

class ResearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $authors;
    protected $host;
    protected $level;
    protected $filePaths;

    /**
     * Create a new job instance.
     */
    public function __construct($title, $authors, $host, $level, $filePaths)
    {
        $this->title = $title;
        $this->authors = $authors;
        $this->host = $host;
        $this->level = $level;
        $this->filePaths = $filePaths;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->filePaths) {
            // Create the 'Researches' folder if it doesn't exist
            if (!Storage::disk('google')->exists('Researches')) {
                Storage::disk('google')->makeDirectory('Researches');
                Storage::disk('google')->setVisibility('Researches', 'public');
            }
    
            // Create the 'Presented' folder if it doesn't exist
            if (!Storage::disk('google')->exists('Researches/Presented')) {
                Storage::disk('google')->makeDirectory('Researches/Presented');
                Storage::disk('google')->setVisibility('Researches/Presented', 'public');
            }
    
            // Create the 'Presented' folder if it doesn't exist
            if (!Storage::disk('google')->exists('Researches/Presented/' . $this->title)) {
                Storage::disk('google')->makeDirectory('Researches/Presented/' . $this->title);
                Storage::disk('google')->setVisibility('Researches/Presented/' . $this->title, 'public');
            }
    
            // Store files in the 'Presented' folder
            foreach ($this->filePaths as $filePath) {
                $file = Storage::get($filePath);
                Storage::disk('google')->put(
                    'Researches/Presented/' . $this->title . '/' . basename($filePath),
                    $file
                );
                Storage::disk('google')->setVisibility('Researches/Presented/' . $this->title . '/' . basename($filePath), 'public');
            }
        }
    }
}
