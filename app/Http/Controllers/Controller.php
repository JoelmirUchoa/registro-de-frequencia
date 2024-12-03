<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brother;  // Modelo para os dados do irmão
use App\Models\Presence; // Modelo para presença

class Controller extends \Illuminate\Routing\Controller
{
        // Método para buscar dados do irmão do quadro
        public function getBrotherData(Request $request)
        {
            $sim = $request->input('sim');

            // Busca o irmão do quadro pelo número SIM
            $brother = Brother::where('sim', $sim)->first();
    
            if ($brother) {
                return response()->json(['success' => true, 'data' => $brother]);
            } else {
                return response()->json(['success' => false, 'message' => 'Irmão não encontrado.']);
            }
        }
    
        // Método para registrar presença
        public function registerPresence(Request $request)
        {
            $sim = $request->input('sim');

            // Verifica se o irmão existe no banco de dados
            $brother = Brother::where('sim', $sim)->first();
    
            if ($brother) {
                // Cria um novo registro de presença
                Presence::create([
                    'brother_id' => $brother->id,
                    'date' => now(),
                ]);
    
                return response()->json(['success' => true, 'message' => 'Presença registrada com sucesso.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Irmão não encontrado.']);
            }
        }
}
