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
        $perPage = $request->input('per_page', 5);
        $query = Presence::query();

        // Obter cargos únicos dos irmãos e visitantes
        $brotherPositions = Brother::distinct()->pluck('position')->toArray();
        $visitorPositions = Visitor::distinct()->pluck('position')->toArray();
        $positions = array_unique(array_merge($brotherPositions, $visitorPositions));

        if ($request->filled('name')) {
            $query->whereHas('brother', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            })->orWhereHas('visitor', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%');
            });
        }

        if ($request->filled('sim')) {
            $query->whereHas('brother', function ($q) use ($request) {
                $q->where('sim', 'like', '%' . $request->sim . '%');
            })->orWhereHas('visitor', function ($q) use ($request) {
                $q->where('sim', 'like', '%' . $request->sim . '%');
            });
        }

        // if ($request->filled('position')) {
        //     $query->whereHas('brother', function ($q) use ($request) {
        //         $q->where('position', 'like', '%' . $request->position . '%');
        //     });
        // }
        if ($request->filled('position')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('brother', function ($subQuery) use ($request) {
                    $subQuery->where('position', $request->position); // Cargo exato
                })->orWhereHas('visitor', function ($subQuery) use ($request) {
                    $subQuery->where('position', $request->position); // Cargo exato
                });
            });
        }
        

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

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

        $reportData = $query->paginate($perPage)->appends($request->all());

        $reportData->getCollection()->transform(function ($presence) {
            $user = $presence->user_type === 'brother'
                ? Brother::find($presence->user_id)
                : Visitor::find($presence->user_id);

            return [
                'sim' => $user->sim ?? 'N/A',
                'name' => $user->name ?? 'Desconhecido',
                'position' => $user->position ?? 'N/A',
                'user_type' => $presence->user_type === 'brother' ? 'Irmão' : 'Visitante',
                'date' => $presence->created_at->format('d/m/Y H:i'),
            ];
        });

        return view('relatorio-presencas', compact('reportData', 'positions'));
    }

    public function exportPdf(Request $request)
    {
        // Filtrar os dados da tabela presences
        $query = Presence::query();

        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('brother', function ($subQuery) use ($request) {
                    $subQuery->where('name', 'like', '%' . $request->name . '%');
                })
                ->orWhereHas('visitor', function ($subQuery) use ($request) {
                    $subQuery->where('name', 'like', '%' . $request->name . '%');
                });
            });
        }

        if ($request->filled('sim')) {
            $query->whereHas('brother', function ($q) use ($request) {
                $q->where('sim', 'like', '%' . $request->sim . '%');
            });
        }

        if ($request->filled('position')) {
            $query->whereHas('brother', function ($q) use ($request) {
                $q->where('position', 'like', '%' . $request->position . '%');
            });
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Adicionar filtro de intervalo de datas
        if ($request->filled('date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($request->filled('date')) {
            $query->whereDate('created_at', Carbon::parse($request->date));
        }

        // Buscar os dados filtrados
        $presences = $query->get();

        // Preparar os dados formatados
        $reportData = $presences->map(function ($presence) {
            $user = $presence->user_type === 'brother'
                ? Brother::find($presence->user_id)
                : Visitor::find($presence->user_id);

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
