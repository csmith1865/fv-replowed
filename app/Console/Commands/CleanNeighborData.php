<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanNeighborData extends Command
{
    protected $signature = 'neighbors:clean';
    protected $description = 'Limpa dados duplicados ou inconsistentes de vizinhos';

    public function handle()
    {
        $this->info('Iniciando limpeza de dados de vizinhos...');
        
        // Buscar todos os registros de vizinhos
        $records = DB::table('playermeta')
            ->whereIn('meta_key', ['current_neighbors', 'pending_neighbors'])
            ->get();
        
        $cleaned = 0;
        
        foreach ($records as $record) {
            $data = unserialize($record->meta_value);
            
            if (is_array($data)) {
                // Remover duplicatas
                $originalCount = count($data);
                $data = array_unique($data);
                $data = array_values($data); // Reindexar
                
                if (count($data) !== $originalCount) {
                    DB::table('playermeta')
                        ->where('id', $record->id)
                        ->update(['meta_value' => serialize($data)]);
                    
                    $cleaned++;
                    $this->line("Limpou {$record->uid} - {$record->meta_key}: {$originalCount} -> " . count($data));
                }
            }
        }
        
        $this->info("Limpeza concluída! {$cleaned} registros atualizados.");
        return 0;
    }
}