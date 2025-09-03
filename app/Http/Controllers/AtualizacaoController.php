<?php

namespace App\Http\Controllers;

use App\Models\Atualizacao;
use App\Http\Requests\StoreAtualizacaoRequest;
use App\Http\Requests\UpdateAtualizacaoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class AtualizacaoController extends Controller
{
    /**
     * Verifica se o usuário é admin
     */
    private function isAdmin()
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * Retorna erro se não for admin
     */
    private function checkAdminPermission()
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'message' => 'Acesso negado. Apenas administradores podem realizar esta ação.'
            ], 403);
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Atualizacao::with('autor')
            ->ativas()
            ->recentes();

        // Filtrar por tipo se especificado
        if ($request->has('tipo') && $request->tipo) {
            $query->doTipo($request->tipo);
        }

        // Paginação
        $atualizacoes = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $atualizacoes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAtualizacaoRequest $request)
    {
        $adminCheck = $this->checkAdminPermission();
        if ($adminCheck) return $adminCheck;

        $imagemPath = null;

        // Upload da imagem se fornecida
        if ($request->hasFile('imagem')) {
            $imagemPath = $request->file('imagem')->store('atualizacoes', 'public');
        }

        $atualizacao = Atualizacao::create([
            'titulo' => $request->titulo,
            'conteudo' => $request->conteudo,
            'imagem' => $imagemPath,
            'versao' => $request->versao,
            'tipo' => $request->tipo,
            'ativo' => $request->ativo ?? true,
            'user_id' => Auth::id()
        ]);

        $atualizacao->load('autor');

        return response()->json([
            'success' => true,
            'message' => 'Atualização criada com sucesso!',
            'data' => $atualizacao
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Atualizacao $atualizacao)
    {
        // Carregar o relacionamento com o autor
        $atualizacao->load('autor');

        return response()->json([
            'success' => true,
            'data' => $atualizacao
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAtualizacaoRequest $request, Atualizacao $atualizacao)
    {
        $adminCheck = $this->checkAdminPermission();
        if ($adminCheck) return $adminCheck;

        $imagemPath = $atualizacao->imagem;

        // Upload da nova imagem se fornecida
        if ($request->hasFile('imagem')) {
            // Deletar imagem antiga se existir
            if ($atualizacao->imagem) {
                Storage::disk('public')->delete($atualizacao->imagem);
            }
            $imagemPath = $request->file('imagem')->store('atualizacoes', 'public');
        }

        $atualizacao->update([
            'titulo' => $request->titulo,
            'conteudo' => $request->conteudo,
            'imagem' => $imagemPath,
            'versao' => $request->versao,
            'tipo' => $request->tipo,
            'ativo' => $request->ativo ?? $atualizacao->ativo,
        ]);

        $atualizacao->load('autor');

        return response()->json([
            'success' => true,
            'message' => 'Atualização editada com sucesso!',
            'data' => $atualizacao
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Atualizacao $atualizacao)
    {
        $adminCheck = $this->checkAdminPermission();
        if ($adminCheck) return $adminCheck;

        // Deletar imagem se existir
        if ($atualizacao->imagem) {
            Storage::disk('public')->delete($atualizacao->imagem);
        }

        $atualizacao->delete();

        return response()->json([
            'success' => true,
            'message' => 'Atualização excluída com sucesso!'
        ]);
    }

    /**
     * Toggle status ativo/inativo da atualização
     */
    public function toggleStatus(Atualizacao $atualizacao)
    {
        $adminCheck = $this->checkAdminPermission();
        if ($adminCheck) return $adminCheck;

        $atualizacao->update(['ativo' => !$atualizacao->ativo]);

        return response()->json([
            'success' => true,
            'message' => 'Status da atualização alterado com sucesso!',
            'data' => $atualizacao
        ]);
    }

    /**
     * Remove apenas a imagem da atualização
     */
    public function removeImagem(Atualizacao $atualizacao)
    {
        $adminCheck = $this->checkAdminPermission();
        if ($adminCheck) return $adminCheck;

        if ($atualizacao->imagem) {
            Storage::disk('public')->delete($atualizacao->imagem);
            $atualizacao->update(['imagem' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Imagem removida com sucesso!',
                'data' => $atualizacao
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Esta atualização não possui imagem.'
        ], 400);
    }
}
