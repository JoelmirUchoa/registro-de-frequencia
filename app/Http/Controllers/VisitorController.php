<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\Brother;
use App\Models\Loja;
use App\Models\Cargo;
use Illuminate\Http\Request;
use App\Models\Presence;
use Carbon\Carbon;

class VisitorController extends Controller
{

    public function getExistingData()
    {
        // Buscar cargos de visitantes, irmãos e tabela de cargos
        $positions = Visitor::pluck('position')
            ->merge(Brother::pluck('position'))
            ->merge(Cargo::pluck('nome'))
            ->unique()->values();
    
        // Buscar lojas de visitantes, irmãos e tabela de lojas
        $lojas = Visitor::pluck('loja')
            ->merge(Brother::pluck('loja'))
            ->merge(Loja::pluck('nome'))
            ->unique()->values();
    
        return response()->json([
            'success' => true,
            'positions' => $positions,
            'lojas' => $lojas,
        ]);
    }

    // Método para exibir o formulário de registro de visitantes
    public function showVisitorForm()
    {
        // Obter os cargos distintos de brothers e visitors
        $brotherPositions = Brother::select('position')->distinct()->pluck('position')->toArray();
        $visitorPositions = Visitor::select('position')->distinct()->pluck('position')->toArray();
        $positions = array_unique(array_merge($brotherPositions, $visitorPositions));

        // Obter as lojas distintas de brothers e visitors
        $brotherLojas = Brother::select('loja')->distinct()->pluck('loja')->toArray();
        $visitorLojas = Visitor::select('loja')->distinct()->pluck('loja')->toArray();
        $lojas = array_unique(array_merge($brotherLojas, $visitorLojas));

        // Obter os cargos
        $cargos = Cargo::all();

        // Obter as lojas
        $lojas = Loja::all();

        return view('visitor-form', compact('positions', 'lojas', 'cargos'));
    }
    
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

            // Verifica se o CIM já está registrado como "Visitante"
            $existingVisitor = Visitor::where('sim', $validated['sim'])->first();
            if ($existingVisitor) {
                return response()->json([
                    'success' => false,
                    'message' => 'O número CIM informado já está cadastrado como visitante.',
                ], 422);
            }

            // Verifica se o CIM já está registrado como "Irmão"
            $existingBrother = Brother::where('sim', $validated['sim'])->first();
            if ($existingBrother) {
                return response()->json([
                    'success' => false,
                    'message' => 'O número CIM informado já está cadastrado como irmão.',
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

        // Verifica se o CIM pertence a um irmão
        $brother = Brother::where('sim', $sim)->first();
        if ($brother) {
            return response()->json([
                'success' => false,
                'message' => 'O número CIM informado já está cadastrado como irmão.',
            ]);
        }

        // Verifica se o CIM pertence a um visitante
        $visitor = Visitor::where('sim', $sim)->first();
        if ($visitor) {
            return response()->json(['success' => true, 'data' => $visitor]);
        }

        // Caso o CIM não seja encontrado em nenhum lugar
        return response()->json([
            'success' => false,
            'message' => 'Visitante não encontrado.',
        ]);
    }

    // Método para registrar presença de um visitante
    public function registerVisitorPresence(Request $request)
    {
        $sim = $request->input('sim');
    
        // Verifica se o CIM foi fornecido
        if (empty($sim)) {
            return response()->json([
                'success' => false, 
                'message' => 'O número CIM é obrigatório.',
            ]);
        }
    
        // Busca o visitante pelo número CIM
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
                'name' => $visitor->name, // Adicionando o nome do visitante
                'loja' => $visitor->loja, // Adicionando a loja (se necessário)
                'date' => Carbon::now()->format('Y-m-d H:i:s'),   // Data atual formatada
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
