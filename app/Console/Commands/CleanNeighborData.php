<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanNeighborData extends Command
{
    protected $signature = 'neighbors:clean';
    protected $description = 'Clean duplicate or inconsistent neighbor data';

    public function handle()
    {
        $this->info('Starting neighbor data cleanup...');
        
        // Fetch all neighbor records
        $records = DB::table('playermeta')
            ->whereIn('meta_key', ['current_neighbors', 'pending_neighbors'])
            ->get();
        
        $cleaned = 0;
        
        foreach ($records as $record) {
            $data = unserialize($record->meta_value);
            
            if (is_array($data)) {
                // Remove duplicates
                $originalCount = count($data);
                $data = array_unique($data);
                $data = array_values($data); // Reindex
                
                if (count($data) !== $originalCount) {
                    DB::table('playermeta')
                        ->where('id', $record->id)
                        ->update(['meta_value' => serialize($data)]);
                    
                    $cleaned++;
                    $this->line("Cleaned {$record->uid} - {$record->meta_key}: {$originalCount} -> " . count($data));
                }
            }
        }
        
        $this->info("Cleanup completed! {$cleaned} records updated.");

        return 0;
    }
}