<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\User;
use App\Models\Partida;
use Illuminate\Http\Request;
use App\Http\Resources\PartidaResource;
use App\Http\Resources\PartidaCollection;
use App\Http\Resources\PartidaStoredResource;
use App\Http\Resources\PartidaUpdatedResource;
use App\Http\Requests\PartidaStoreRequest;
use App\Http\Requests\PartidaUpdateRequest;
use Exception;
use Illuminate\Support\Facades\DB;

class PartidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new PartidaCollection(Partida::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PartidaStoreRequest $request)
    {
        try {
            // Valida os dados e cria a partida
             $partida = Partida::create($request->validated());
            return (new PartidaStoredResource($partida))
            ->additional(['message'=> 'Partida registrada com Sucesso'])
            ->response()
            ->setStatusCode(201, 'Partida criada');
        } catch (Exception $error) {
            // Trate erros e retorne um status apropriado
            $this->errorHandler("Erro ao cadastrar partida",$error);
    }}

    public function showByUser(Request $request)
{
    try {
        $userId = $request->user()->id; // Obtém o ID do usuário autenticado

        // Busca as partidas do usuário autenticado
        $partidas = Partida::with(['jogador1', 'jogador2', 'vencedor'])
        ->where('jogador1_id', $userId)
        ->orWhere('jogador2_id', $userId)
        ->get();


        return response()->json([
            'success' => true,
            'data' => $partidas,
        ], 200);
    } catch (Exception $error) {
        return response()->json([
            'success' => false,
            'message' => "Erro ao buscar partidas do usuário",
            'error' => $error->getMessage(),
        ], 500);
    }
}



    public function ranking()
    {
        try {
            // Executa a query para calcular o ranking
            $ranking = DB::table('users')
                ->leftJoin('partidas', 'users.id', '=', 'partidas.vencedor_id')
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw('COALESCE(SUM(partidas.pontuacao), 0) AS total_pontuacao')
                )
                ->groupBy('users.id', 'users.name')
                ->orderBy('total_pontuacao', 'desc')
                ->orderBy('users.name', 'asc')
                ->get();

            // Retorna o ranking como JSON
            return response()->json($ranking, 200);
        } catch (\Exception $e) {
            // Loga o erro e retorna uma resposta de erro
            \Log::error('Erro ao calcular o ranking: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao calcular o ranking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function simularPartida(Request $request)
    {
        try {
            // Obtém o usuário logado (Jogador 1)
            $jogador1 = $request->user();

            // 1. Verifica se o Jogador 1 (usuário logado) é um administrador
            if ($jogador1->isAdmin()) { // Usando o método isAdmin() do seu modelo User
                return response()->json(['message' => 'Administradores não podem iniciar partidas simuladas.'], 403); // 403 Forbidden
            }
            $jogador1Id = $jogador1->id;

            // 2. Seleciona um jogador aleatório (Jogador 2) do banco que:
            //    - Não seja o usuário logado (Jogador 1)
            //    - Não seja um administrador
            $jogador2 = User::where('id', '!=', $jogador1Id)
                              ->where('role', '!=', 'admin') // Assume que a coluna 'role' existe e admins têm o valor 'admin'
                                                            // Se seu método isAdmin() verifica outra coisa, ajuste esta query
                              ->inRandomOrder()
                              ->first();

            if (!$jogador2) {
                return response()->json(['message' => 'Não há jogadores não-administradores disponíveis para simulação.'], 400);
            }

            // Sorteia um vencedor aleatório entre Jogador 1 e Jogador 2
            // (Ambos já são garantidos como não-admins neste ponto)
            $vencedorId = rand(0, 1) ? $jogador1Id : $jogador2->id;

            // Sorteia uma pontuação de 1 a 5
            $pontuacao = rand(1, 5);

            // Registra a partida no banco de dados
            $partida = Partida::create([
                'jogador1_id' => $jogador1Id,
                'jogador2_id' => $jogador2->id,
                'vencedor_id' => $vencedorId,
                'pontuacao' => $pontuacao,
            ]);

            return response()->json([
                'message' => 'Partida simulada com sucesso!',
                'partida' => $partida->load(['jogador1:id,name', 'jogador2:id,name', 'vencedor:id,name']), // Opcional: carregar nomes para o frontend
            ], 201);

        } catch (Exception $e) {
            Log::error('Erro ao simular partida: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return response()->json(['message' => 'Erro interno ao simular partida.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Partida $partida)
    {
        return new PartidaResource($partida);
    }

   /**
 * Update the specified resource in storage.
 */
public function update(PartidaUpdateRequest $request, Partida $partida)
{
    try {
        $partida->update([
            'jogador1_id' => $request->input('jogador1_id'),
            'jogador2_id' => $request->input('jogador2_id'),
            'vencedor_id' => $request->input('vencedor_id'),
            'pontuacao' => $request->input('pontuacao'),
        ]);

        return new PartidaUpdatedResource($partida);
    } catch (\Exception $error) {
        return $this->errorHandler("Erro ao atualizar a partida!", $error);
    }
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partida $partida)
    {
        try{
            $partida->delete();
            return (new PartidaResource($partida))->additional(["message"=>"Partida Removida!"]);
        }catch (Exception $error){
            return $this->errorHandler("Erro ao remover Partida", $error);
        }
    }
}
