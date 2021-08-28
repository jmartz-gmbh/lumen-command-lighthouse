<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LighthouseImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lighthouse:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Lighthouse reports';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(Request $request)
    {
        $storage = storage_path().'/reports';
        $dir = scandir($storage);
        $result = [];

        foreach ($dir as $key => $filename) {
            if($filename != '..' && $filename != '.'){
                $score = 0;
                $json = file_get_contents($storage.'/'.$filename);
                $data = json_decode($json, true);
                foreach($data['audits'] as $key => $audit){
                    $score += $audit['score'];
                }
                $result[str_replace('.json','',$filename)] = $score;
            }
        }
    }
}