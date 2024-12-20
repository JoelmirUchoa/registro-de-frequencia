<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Brother;
use App\Models\Visitor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function showReport(Request $request)
{
    $perPage = $request->input('per_page', 10); // Valor padrão de 10 registros por página.

    $query = Presence::query();

    // Aplicar filtros (opcional)
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

    // if ($request->filled('date') && $request->filled('end_date')) {
    //     $query->whereBetween('created_at', [$request->date, $request->end_date]);
    // } elseif ($request->filled('date')) {
    //     $query->whereDate('created_at', $request->date);
    // }

    if ($request->filled('date') && $request->filled('end_date')) {
        $startDate = Carbon::parse($request->date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $query->whereBetween('created_at', [$startDate, $endDate]);
    } elseif ($request->filled('date')) {
        $query->whereDate('created_at', Carbon::parse($request->date));
    }

    if ($request->missing(['name', 'sim', 'position', 'user_type', 'date'])) {
        $query->whereMonth('created_at', now()->month)
              ->whereYear('created_at', now()->year);
    }

    // Paginação
    $perPage = $request->get('per_page', 5);
    $reportData = $query->paginate($perPage);
     // Paginação com o número de registros por página
    $reportData = $query->paginate($perPage)->appends($request->all());

    // Transformar os itens paginados para incluir as informações detalhadas
    $reportData->getCollection()->transform(function ($presence) {
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

    // Adicionar filtro de intervalo de datas
    if ($request->filled('date') && $request->filled('end_date')) {
        $startDate = \Carbon\Carbon::parse($request->date)->startOfDay();
        $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
        $query->whereBetween('created_at', [$startDate, $endDate]);
    } elseif ($request->filled('date')) {
        $query->whereDate('created_at', \Carbon\Carbon::parse($request->date));
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