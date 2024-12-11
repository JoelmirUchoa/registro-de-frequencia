<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use App\Models\Presence;

class VisitorController extends Controller
{
    // Método para registrar um novo visitante
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sim' => 'required|string|unique:visitors,sim',
        ]);

        // Cria o visitante no banco de dados
        $visitor = Visitor::create([
            'name' => $validated['name'],
            'sim' => $validated['sim'],
        ]);

        return response()->json(['success' => true, 'message' => 'Visitante cadastrado com sucesso!', 'data' => $visitor]);
    }

    // Método para verificar se um visitante existe
    public function verify(Request $request)
    {
        $sim = $request->input('sim');

        $visitor = Visitor::where('sim', $sim)->first();

        if ($visitor) {
            return response()->json(['success' => true, 'data' => $visitor]);
        } else {
            return response()->json(['success' => false, 'message' => 'Visitante não encontrado.']);
        }
    }

    // Método para registrar presença de um visitante
    //public function registerPresence(Request $request)
    public function registerVisitorPresence(Request $request)
    {
        $sim = $request->input('sim');

        // Verifica se o SIM foi fornecido
        if (empty($sim)) {
            return response()->json(['success' => false, 'message' => 'Número SIM é obrigatório.']);
        }

        // Busca o visitante pelo número SIM
        $visitor = Visitor::where('sim', $sim)->first();

        // Verifica se o visitante foi encontrado
        if ($visitor) {
            try {
                // Registra a presença do visitante
                Presence::create([
                    'user_type' => 'visitor',   // Tipo será "visitor" neste contexto
                    'user_id' => $visitor->id, // ID do visitante
                    'date' => now(),           // Data atual
                ]);

                return response()->json(['success' => true, 'message' => 'Presença do Visitante registrada com sucesso.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Erro ao registrar presença: ' . $e->getMessage()]);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Visitante não encontrado.']);
        }
    }
}
