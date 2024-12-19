<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Brother;
use App\Models\Visitor;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function showReport(Request $request)
    {
        // Criar a query base para Presence
        $query = Presence::query();
    
        // Filtros opcionais
        if ($request->filled('name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }
    
        if ($request->filled('sim')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('sim', 'like', '%' . $request->sim . '%');
            });
        }
    
        if ($request->filled('position')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('position', 'like', '%' . $request->position . '%');
            });
        }
    
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }
    
        // if ($request->filled('date')) {
        //     $query->whereDate('created_at', $request->date);
        // }
        if ($request->filled('date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->date, $request->end_date]);
        } elseif ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        
    
        // Filtrar pelo mês atual se não houver filtros específicos
        if ($request->missing(['name', 'sim', 'position', 'user_type', 'date'])) {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }

         // Paginação com o número de registros por página
        $perPage = $request->get('per_page', 10); // Padrão: 10
        $reportData = $query->paginate($perPage);
    
        // Buscar os dados
        $presences = $query->get();
    
        // Formatar os dados para exibição
        $reportData = $presences->map(function ($presence) {
            $user = $presence->user_type === 'brother'
                ? \App\Models\Brother::find($presence->user_id)
                : \App\Models\Visitor::find($presence->user_id);
        
            return [
                'sim' => $user->sim ?? 'N/A',
                'name' => $user->name ?? 'Desconhecido',
                'position' => $user->position ?? 'N/A', // Pegar o cargo de ambos
                'user_type' => $presence->user_type === 'brother' ? 'Irmão' : 'Visitante',
                'date' => $presence->created_at->format('d/m/Y H:i'),
            ];
        });
    
        // Retornar a view com os dados
        return view('relatorio-presencas', compact('reportData'));
    }    
    
    public function exportPdf(Request $request)
    {
        // Filtrar os dados da tabela presences
        $query = Presence::query();
    
        if ($request->filled('name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }
    
        if ($request->filled('sim')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('sim', 'like', '%' . $request->sim . '%');
            });
        }
    
        if ($request->filled('position')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('position', 'like', '%' . $request->position . '%');
            });
        }
    
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }
    
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
    
        // Buscar os dados filtrados
        $presences = $query->get();
    
        // Preparar os dados formatados
        $reportData = $presences->map(function ($presence) {
            $user = $presence->user_type === 'brother'
                ? \App\Models\Brother::find($presence->user_id)
                : \App\Models\Visitor::find($presence->user_id);
    
            return [
                'sim' => $user->sim ?? 'N/A',
                'name' => $user->name ?? 'Desconhecido',
                'position' => $user->position ?? 'N/A',
                'user_type' => $presence->user_type === 'brother' ? 'Irmão' : 'Visitante',
                'date' => $presence->created_at->format('d/m/Y H:i'),
            ];
        });
    
        // Gerar o PDF com os dados filtrados
        $pdf = Pdf::loadView('relatorio-presencas-pdf', compact('reportData'));
    
        return $pdf->download('relatorio-presencas.pdf');
    }    
}