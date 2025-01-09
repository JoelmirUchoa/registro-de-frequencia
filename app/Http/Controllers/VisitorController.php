<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Brother;
use Illuminate\Http\Request;
use App\Models\Presence;

class VisitorController extends Controller
{
    // Método para registrar um novo visitante
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'sim' => 'required|string|max:255',
                'loja' => 'required|string|max:255',
                'numero_da_loja' => 'required|string|max:255',
            ]);

            // Verifica se o SIM já está registrado como "Visitante"
            $existingVisitor = Visitor::where('sim', $validated['sim'])->first();
            if ($existingVisitor) {
                return response()->json([
                    'success' => false,
                    'message' => 'O número SIM informado já está cadastrado como visitante.',
                ], 422);
            }

            // Verifica se o SIM já está registrado como "Irmão"
            $existingBrother = Brother::where('sim', $validated['sim'])->first();
            if ($existingBrother) {
                return response()->json([
                    'success' => false,
                    'message' => 'O número SIM informado já está cadastrado como irmão.',
                ], 422);
            }

            // Cria o visitante no banco de dados
            $visitor = Visitor::create([
                'name' => $validated['name'],
                'sim' => $validated['sim'],
                'position' => $validated['position'],
                'loja' => $validated['loja'],
                'numero_da_loja' => $validated['numero_da_loja'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visitante cadastrado com sucesso!',
                'data' => $visitor,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar visitante: ' . $e->getMessage(),
            ]);
        }
    }

    // Método para verificar se um visitante existe
    public function verify(Request $request)
{
    $sim = $request->input('sim');

    // Verifica se o SIM pertence a um irmão
    $brother = Brother::where('sim', $sim)->first();
        if ($brother) {
            return response()->json([
                'success' => false,
                'message' => 'O número SIM informado já está cadastrado como irmão.',
            ]);
        }

        // Verifica se o SIM pertence a um visitante
        $visitor = Visitor::where('sim', $sim)->first();
        if ($visitor) {
            return response()->json(['success' => true, 'data' => $visitor]);
        }

        // Caso o SIM não seja encontrado em nenhum lugar
        return response()->json([
            'success' => false,
            'message' => 'Visitante não encontrado.',
        ]);
    }

    // Método para registrar presença de um visitante
    //public function registerPresence(Request $request)
    public function registerVisitorPresence(Request $request)
    {
        $sim = $request->input('sim');
    
        // Verifica se o SIM foi fornecido
        if (empty($sim)) {
            return response()->json([
                'success' => false, 
                'message' => 'O número SIM é obrigatório.',
            ]);
        }
    
        // Busca o visitante pelo número SIM
        $visitor = Visitor::where('sim', $sim)->first();
    
        // Verifica se o visitante foi encontrado
        if (!$visitor) {
            return response()->json([
                'success' => false, 
                'message' => 'Visitante não encontrado.'
            ]);
        }
    
        try {
            // Registra a presença do visitante
            Presence::create([
                'user_type' => 'visitor',   // Tipo será "visitor" neste contexto
                'user_id' => $visitor->id, // ID do visitante
                'date' => now(),           // Data atual
            ]);
    
            return response()->json([
                'success' => true, 
                'message' => 'Presença do visitante registrada com sucesso.'
            ]);
        } catch (\Exception $e) {
            // Captura erros e retorna uma mensagem adequada
            return response()->json([
                'success' => false, 
                'message' => 'Erro ao registrar presença: ' . $e->getMessage()
            ]);
        }
    }    
}
