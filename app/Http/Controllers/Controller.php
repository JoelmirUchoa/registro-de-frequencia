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
    // public function registerPresence(Request $request)
    // {
    //     $sim = $request->input('sim');
    //     $type = $request->input('type'); // Pode ser 'brother' ou 'visitor'

    //     // Verifica se o irmão ou visitante existe no banco de dados
    //     if ($type == 'brother') {
    //         // Verifica o irmão no banco
    //         $user = Brother::where('sim', $sim)->first();
    //     } else {
    //         // Para visitantes, você pode ter uma tabela Visitors se for o caso
    //         // Exemplo: $user = Visitor::where('sim', $sim)->first();
    //         $user = null;  // Aqui você precisaria de um modelo para visitantes, se aplicável.
    //     }

    //     if ($user) {
    //         // Cria um novo registro de presença com o tipo de usuário (brother ou visitor)
    //         Presence::create([
    //             'user_type' => $type,  // Tipo de usuário (brother ou visitor)
    //             'user_id' => $user->id, // ID do usuário (irmão ou visitante)
    //             'date' => now(), // Data atual
    //         ]);

    //         return response()->json(['success' => true, 'message' => 'Presença registrada com sucesso.']);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'Irmão ou visitante não encontrado.']);
    //     }
    // }

    public function registerPresence(Request $request)
{
    $sim = $request->input('sim');
    $type = $request->input('type'); // 'brother' ou 'visitor'

    // Verifica se o tipo de usuário foi passado corretamente
    if (empty($sim) || empty($type)) {
        return response()->json(['success' => false, 'message' => 'Dados insuficientes.']);
    }

    // Verifica se é brother ou visitor
    if ($type == 'brother') {
        $user = Brother::where('sim', $sim)->first();
    } else {
        // Para visitante, você precisará verificar o modelo correspondente (por exemplo, Visitor)
        // Adapte esta parte para o seu sistema, aqui está como um exemplo simples:
        // $user = Visitor::where('sim', $sim)->first();
        $user = null; // Caso você não tenha um modelo de visitantes implementado
    }

    // Verifica se o usuário foi encontrado
    if ($user) {
        try {
            // Cria um novo registro de presença com o tipo de usuário (brother ou visitor)
            Presence::create([
                'user_type' => $type,
                'user_id' => $user->id,
                'date' => now(), // Data atual
            ]);
            return response()->json(['success' => true, 'message' => 'Presença registrada com sucesso.']);
        } catch (\Exception $e) {
            // Se houver um erro ao salvar no banco, exibe a mensagem de erro
            return response()->json(['success' => false, 'message' => 'Erro ao registrar presença: ' . $e->getMessage()]);
        }
    } else {
        return response()->json(['success' => false, 'message' => 'Irmão ou visitante não encontrado.']);
    }
}

}
