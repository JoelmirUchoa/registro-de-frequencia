<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\Brother;
use App\Models\Visitor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function showReport(Request $request)
    {
        $sortBy = $request->input('sort_by', 'date');
        $sortDirection = $request->input('sort_direction', 'asc');
        $perPage = $request->input('per_page', 10);

        // Obter os cargos distintos de brothers e visitors
        $brotherPositions = Brother::select('position')->distinct()->pluck('position')->toArray();
        $visitorPositions = Visitor::select('position')->distinct()->pluck('position')->toArray();
        $positions = array_unique(array_merge($brotherPositions, $visitorPositions));

        // Base da consulta
        $query = DB::table('presences')
            ->leftJoin('brothers', function ($join) {
                $join->on('presences.user_id', '=', 'brothers.id')
                    ->where('presences.user_type', '=', 'brother');
            })
            ->leftJoin('visitors', function ($join) {
                $join->on('presences.user_id', '=', 'visitors.id')
                    ->where('presences.user_type', '=', 'visitor');
            })
            ->select(
                'presences.id',
                'presences.user_type',
                'presences.created_at as presence_date',
                'brothers.sim as brother_sim',
                'brothers.name as brother_name',
                'brothers.position as brother_position',
                'brothers.loja as brother_loja',
                'brothers.numero_da_loja as brother_numero_da_loja',
                'visitors.sim as visitor_sim',
                'visitors.name as visitor_name',
                'visitors.position as visitor_position',
                'visitors.loja as visitor_loja',
                'visitors.numero_da_loja as visitor_numero_da_loja'
            );

        // Aplicar filtros
        if ($request->filled('name')) {
            $query->where(function ($query) use ($request) {
                $query->where('brothers.name', 'like', '%' . $request->input('name') . '%')
                    ->orWhere('visitors.name', 'like', '%' . $request->input('name') . '%');
            });
        }

        if ($request->filled('sim')) {
            $query->where(function ($query) use ($request) {
                $query->where('brothers.sim', 'like', '%' . $request->input('sim') . '%')
                    ->orWhere('visitors.sim', 'like', '%' . $request->input('sim') . '%');
            });
        }

        if ($request->filled('position')) {
            $query->where(function ($query) use ($request) {
                $query->where('brothers.position', $request->input('position'))
                    ->orWhere('visitors.position', $request->input('position'));
            });
        }

        if ($request->filled('user_type')) {
            $query->where('presences.user_type', $request->input('user_type'));
        }

        if ($request->filled('date')) {
            $query->whereDate('presences.created_at', '>=', $request->input('date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('presences.created_at', '<=', $request->input('end_date'));
        }

        // Ordenação
        if ($sortBy === 'date') {
            $query->orderBy('presences.created_at', $sortDirection);
        } elseif ($sortBy === 'name') {
            $query->orderBy(DB::raw('COALESCE(brothers.name, visitors.name)'), $sortDirection);
        } elseif ($sortBy === 'position') {
            $query->orderBy(DB::raw('COALESCE(brothers.position, visitors.position)'), $sortDirection);
        } elseif ($sortBy === 'sim') {
            $query->orderBy(DB::raw('COALESCE(brothers.sim, visitors.sim)'), $sortDirection);
        } elseif ($sortBy === 'loja') {
            $query->orderBy(DB::raw('COALESCE(brothers.loja, visitors.loja)'), $sortDirection);
        } elseif ($sortBy === 'numero_da_loja') {
            $query->orderBy(DB::raw('COALESCE(brothers.numero_da_loja, visitors.numero_da_loja)'), $sortDirection);
        } elseif ($sortBy === 'user_type') {
            $query->orderBy(DB::raw("CASE presences.user_type WHEN 'brother' THEN 'Irmão' ELSE 'Visitante' END"), $sortDirection);
        } else {
            $query->orderBy('presences.id', $sortDirection);
        }

        // Paginação
        $reportData = $query->paginate($perPage);

        // Transformação dos dados
        $transformedData = $reportData->getCollection()->map(function ($row) {
            return [
                'sim' => $row->user_type === 'brother' ? $row->brother_sim : $row->visitor_sim,
                'name' => $row->user_type === 'brother' ? $row->brother_name : $row->visitor_name,
                'position' => $row->user_type === 'brother' ? $row->brother_position : $row->visitor_position,
                'loja' => $row->user_type === 'brother' ? $row->brother_loja : $row->visitor_loja,
                'numero_da_loja' => $row->user_type === 'brother' ? $row->brother_numero_da_loja : $row->visitor_numero_da_loja,
                'user_type' => $row->user_type,
                'date' => Carbon::parse($row->presence_date)->format('d/m/Y'),
            ];
        });

        return view('relatorio-presencas', [
            'reportData' => $reportData,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'positions' => $positions,
            'transformedData' => $transformedData,
        ]);
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
            $query->whereBetween('presences.created_at', [$startDate, $endDate]);
        } elseif ($request->filled('date')) {
            $query->whereDate('created_at', Carbon::parse($request->date));
        }

        // Ordenação
        if ($request->filled('sort_by') && in_array($request->sort_by, ['sim', 'name', 'position','loja','numero_da_loja', 'user_type', 'date'])) {
            $sortBy = $request->sort_by;
            $sortDirection = $request->sort_direction === 'asc' ? 'asc' : 'desc';

            if (in_array($sortBy, ['sim', 'name', 'position'])) {
                $query->leftJoin('brothers', function ($join) {
                    $join->on('brothers.id', '=', 'presences.user_id')->where('presences.user_type', 'brother');
                })->leftJoin('visitors', function ($join) {
                    $join->on('visitors.id', '=', 'presences.user_id')->where('presences.user_type', 'visitor');
                });

                if ($sortBy === 'sim') {
                    $query->orderByRaw('COALESCE(brothers.sim, visitors.sim) ' . $sortDirection);
                } elseif ($sortBy === 'name') {
                    $query->orderByRaw('COALESCE(brothers.name, visitors.name) ' . $sortDirection);
                } elseif ($sortBy === 'position') {
                    $query->orderByRaw('COALESCE(brothers.position, visitors.position) ' . $sortDirection);
                }
            } elseif ($sortBy === 'user_type') {
                $query->orderBy('presences.user_type', $sortDirection);
            } elseif ($sortBy === 'date') {
                $query->orderBy('presences.created_at', $sortDirection);
            }
        } else {
            $query->orderBy('presences.created_at', 'desc'); // Ordem padrão
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
                'loja' => $user->loja ?? 'N/A',
                'numero_da_loja' => $user->numero_da_loja ?? 'N/A', 
                'user_type' => $presence->user_type === 'brother' ? 'Irmão' : 'Visitante',
                'date' => $presence->created_at ? Carbon::parse($presence->created_at)->format('d/m/Y H:i') : 'Sem data',
            ];
        });

        // Adicionar o valor de user_type
        $userType = $request->input('user_type', 'geral'); // Valor padrão: 'geral'

        // Gerar o PDF com os dados filtrados
        $pdf = Pdf::loadView('relatorio-presencas-pdf', compact('reportData', 'userType'));

        return $pdf->download('relatorio-presencas.pdf');
    }

    public function print(Request $request)
    {
        $presences = DB::table('presences')
            ->leftJoin('brothers', function ($join) {
                $join->on('presences.user_id', '=', 'brothers.id')
                    ->where('presences.user_type', '=', 'brother');
            })
            ->leftJoin('visitors', function ($join) {
                $join->on('presences.user_id', '=', 'visitors.id')
                    ->where('presences.user_type', '=', 'visitor');
            })
            ->select(
                'presences.id',
                'presences.created_at',
                DB::raw('COALESCE(brothers.sim, visitors.sim) as sim'),
                DB::raw('COALESCE(brothers.name, visitors.name) as name'),
                DB::raw('COALESCE(brothers.position, visitors.position) as position'),
                DB::raw('COALESCE(brothers.loja, visitors.loja) as loja'),
                DB::raw('COALESCE(brothers.numero_da_loja, visitors.numero_da_loja) as numero_da_loja')
            )
            ->whereDate('presences.created_at', now()->toDateString())
            ->get()
            ->map(function ($presence) {
                $presence->created_at = Carbon::parse($presence->created_at);
                return $presence;
            });

        return view('presence-print', compact('presences'));
    }
    // Adicionar a Verificação de Senha no Controlador
    public function loginChancellor(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if ($username === '1' && $password === '123') {
            return redirect()->route('presence.report');
        } else {
            return redirect()->back()->withErrors(['message' => 'Nome ou senha incorretos.']);
        }
    }
}
