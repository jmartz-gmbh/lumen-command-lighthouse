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

    public function importReport($json, $hash)
    {
        $reports = DB::table('reports');
        $data = json_decode($json, true);
        $timestamp = date('Y-m-d H:i:s');
        $report = DB::table('reports')->where('hash', '=', $hash)->get();
        if ($report->count() === 0) {
            $reports->insert([
                "created_at" => $timestamp,
                "url" => $data['requestedUrl'],
                "hash" => $hash
            ]);
            $report = (array) DB::table('reports')->where('created_at', '=', $timestamp)->where('url', '=', $data['finalUrl'])->first();
            $this->importReportItems($json, $report['id'], $hash);
        }
    }

    public function importReportItems($json, $id, $hash)
    {


        $data = json_decode($json, true);
        foreach ($data['audits'] as $key => $audit) {
            $items = DB::table('report_items');
            $timestamp = date('Y-m-d H:i:s');
            $items->insert([
                "created_at" => $timestamp,
                "hash" => $hash,
                "type" => $audit['id'],
                "score" => $audit['score'],
                "json" => json_encode($audit)
            ]);
        }
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
                        $filename['report'] = $foldername['reports'] . '/' . $folder . '/' . $report;
                        $file = file_get_contents($filename['report']);
                        $this->importReport($file, sha1($folder . '/' . $report));
                        exec('rm '.$filename['report']);
                    }
                }
            }
        }
    }
}