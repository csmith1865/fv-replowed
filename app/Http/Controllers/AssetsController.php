<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class AssetsController extends Controller
{

    private $downloadUrl = "https://archive.org/download/original-farmville/";

    // Uncomment for Prod
    private $files = array(
        [
            "filename" => "urls-bluepload.unstable.life-farmvilleassets.txt-shallow-20201225-045045-5762m-00000.warc.gz",
            "hash"     => "d9a36e44e5361e3db6ce1457f74ddf89"
        ],
        [
            "filename" => "urls-bluepload.unstable.life-farmvilleassets.txt-shallow-20201225-045045-5762m-00001.warc.gz",
            "hash"     => "54a8d13a5dfe0b12b5a3e17e39167a1e"
        ],
        [
            "filename" => "urls-bluepload.unstable.life-farmvilleassets.txt-shallow-20201225-045045-5762m-00002.warc.gz",
            "hash"     => "04938bcbc6585858f88962e9af6232b1"
        ],
        [
            "filename" => "urls-bluepload.unstable.life-farmvilleassets.txt-shallow-20201225-045045-5762m-00003.warc.gz",
            "hash"     => "4bb8d8f949f2ecfdab395cd35c9a47e6"
        ],
    );

    public function checkAssets(){
        if (is_dir(public_path('farmville/assets/hashed/assets'))){
            return true;
        } else {
            return false;
        }

        return false;
    }

    public function downloadAssets(Request $request){        
        

        Session::put('file_progress', 0);
        foreach ($this->files as $key => $file){
            Session::put('file_progress', $key + 1);
            $directory = public_path("tmp");

            $filePath = $directory . '/' . $file["filename"];
            
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            if (file_exists($filePath)) continue;
            // Reset progress
            Session::put('download_progress', 0);
            try {
                $bytes = $this->downloadFile($this->downloadUrl.$file["filename"], $filePath);
                
            } catch (\Exception $e) {
                
                return response()->json(['error' => 'Download failed: ' . $e->getMessage()], 500);
            }

            // Mark as completed
            Session::put('download_progress', 100);

        }

        return response()->json(['message' => 'Download completed!']);

    }

    private function downloadFile($srcName, $dstName, $chunkSize = 1, $returnbytes = true) {
        $chunksize = $chunkSize*(1024*1024); // How many bytes per chunk
        $data = '';
        $bytesCount = 0;

        $options = array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $context  = stream_context_create($options);

        $handle = fopen($srcName, 'rb', false, $context);
        $fp = fopen($dstName, 'w');
        if ($handle === false) {
            return false;
        }
        while (!feof($handle)) {
            $data = fread($handle, $chunksize);
            fwrite($fp, $data, strlen($data));
            if ($returnbytes) {
                $bytesCount += strlen($data);
            }
        }
        $status = fclose($handle);
        fclose($fp);
        if ($returnbytes && $status) {
            return $bytesCount; // Return number of bytes delivered like readfile() does.
        }
        return $status;
    }

    public function getProgress()
    {
        return response()->json(['file_num' => Session::get('file_progress', 0), 'progress' => Session::get('download_progress', 0)]);
    }


    public function extractAssets(){
        Session::put('file_progress', 0);
        Session::put('warc_file_finished', 0);

        $files = glob(public_path("tmp") . '/*');

        foreach($files as $key => $file) {
            Session::put('file_progress', $key + 1);
            Session::put('warc_file_progress', 0);

            $warcProcess = 0;
            $warc_reader = new WarcController($file);
            while(($record = $warc_reader->nextRecord()) != FALSE){
                $headers = $record['header'];
                $payload = $record['content'];
                
                if (!empty($payload) && array_key_exists('WARC-Target-URI', $headers)) {

                    if (!str_contains($headers['WARC-Target-URI'], "assets/hashed/assets")) continue;

                    $filename = parse_url($headers['WARC-Target-URI'], PHP_URL_PATH);
                    $filename = ltrim($filename, '/');
                    
                    if (!$filename) {
                        $filename = uniqid('extracted_', true);
                    }

                    $outputDir = public_path();
                    $dirName = dirname($filename);
                    
                    if (!File::exists("$outputDir/$dirName")) {
                        File::makeDirectory("$outputDir/$dirName", 0777, true, true);
                    }
                    if (file_exists("$outputDir/$filename")) continue;
                    file_put_contents("$outputDir/$filename", $payload);
                    $warcProcess++;
                    Session::put('warc_file_progress', $warcProcess);

                }
            }
        }
        Session::put('warc_file_finished', 1);

        //return response()->json(['files' => $files]);
    }

    public function extractProgress()
    {
        return response()->json(['file_num' => Session::get('file_progress', 0), 'progress' => Session::get('warc_file_progress', 0), 'finished' => Session::get('warc_file_finished', 0)]);
    }

}