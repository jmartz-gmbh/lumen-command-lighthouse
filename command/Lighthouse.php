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

    public function importReport($json){
        var_dump(DB::table('reports')->get());
        die();
    }

    public function handle(Request $request)
    {
        $filename['websites'] = '../../../../www.lighthouse-report.de/shared/websites.json';
        $foldername['reports'] = '../../../../www.lighthouse-report.de/shared/reports';
        $website = file_get_contents($filename['websites']);
        $reports = scandir($foldername['reports']);

        foreach (scandir($foldername['reports']) as $key => $folder) {
            if ($folder != '.' && $folder != '..') {
                foreach (scandir($foldername['reports'] . '/' . $folder) as $index => $report) {
                    if ($report != '.' && $report != '..') {
                        $file = file_get_contents($foldername['reports'] . '/' . $folder.'/'.$report);
                        $this->importReport($file);
                        // var_dump($file);
                    }
                }
            }
        }
    }
}