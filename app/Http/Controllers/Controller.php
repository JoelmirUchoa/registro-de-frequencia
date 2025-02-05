<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brother;  // Modelo para os dados do irmão
use App\Models\Visitor;
use App\Models\Presence; // Modelo para presença

class Controller extends \Illuminate\Routing\Controller
{
    // Método para buscar dados do irmão do quadro
    public function getBrotherData(Request $request)
    {
        $sim = $request->input('sim');

        // Busca o irmão do quadro pelo número CIM
        $brother = Brother::where('sim', $sim)->first();

        // Busca o visitante pelo número CIM
        $visitor = Visitor::where('sim', $sim)->first();

        // Verifica se o CIM já está registrado como visitante
        if ($visitor) {
            return response()->json([
                'success' => false,
                'message' => 'CIM já cadastrado como visitante.',
                'type' => 'visitor',
                'data' => $visitor
            ]);
        }

        if ($brother) {
            return response()->json([
                'success' => true,
                'type' => 'brother',
                'data' => [
                    'name' => $brother->name,
                    'position' => $brother->position,
                    'loja' => $brother->loja,
                    'numero_da_loja' => $brother->numero_da_loja,
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'CIM não encontrado como irmão ou visitante.'
            ]);
        }
    }

    // Método para registrar presença de um irmão do quadro
    //public function registerPresence(Request $request)
    public function registerBrotherPresence(Request $request)
    {
        $sim = $request->input('sim');

        // Verifica se o CIM foi fornecido
        if (empty($sim)) {
            return response()->json(['success' => false, 'message' => 'Número CIM é obrigatório.']);
        }

        // Busca o irmão pelo número CIM
        $brother = Brother::where('sim', $sim)->first();

        // Verifica se o irmão foi encontrado
        if ($brother) {
            try {
                // Cria um novo registro de presença
                Presence::create([
                    'user_type' => 'brother',   // Tipo sempre será "brother" neste contexto
                    'user_id' => $brother->id, // ID do irmão
                    'date' => now(),           // Data atual
                ]);

                return response()->json(['success' => true, 'message' => 'Presença registrada com sucesso.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Erro ao registrar presença: ' . $e->getMessage()]);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Irmão não encontrado.']);
        }
    }
}
