<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // Lista todos os reports do usuário autenticado
    public function index(Request $request)
    {
    $user = $request->user();
    if ($user->isAdmin()) {
        // Admin vê todos os reports
        $reports = Report::with('messages.user')->get();
    } else {
        // Usuário comum vê apenas os seus
        $reports = Report::with('messages.user')->where('user_id', $user->id)->get();
    }
    return response()->json($reports);

    }

    // Cria um novo report com a primeira mensagem
    public function store(Request $request)
    {
        $request->validate([
        'titulo' => 'required|string|max:255', // adicione esta linha
        'mensagem' => 'required|string|max:1000',
        'imagem' => 'nullable|image',
        ]);

    $report = Report::create([
        'user_id' => $request->user()->id,
        'status' => 'aberto',
        'titulo' => $request->input('titulo'),
        ]);

        $messageData = [
            'report_id' => $report->id,
            'user_id' => $request->user()->id,
            'mensagem' => $request->input('mensagem'),
        ];

        if ($request->hasFile('imagem')) {
            $fileName = $request->file('imagem')->store('reports', 's3');
            $messageData['imagem'] = $fileName;
        }

        $report->messages()->create($messageData);

        return response()->json(['message' => 'Report criado com sucesso!', 'report' => $report->load('messages.user')], 201);
    }


    // Exibe um report específico
    public function show($id)
    {
        $report = Report::with('messages.user')->findOrFail($id);
        return response()->json($report);
    }

    // Atualiza o status de um report
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aberto,em_análise,concluído',
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->input('status');
        $report->save();

        return response()->json(['message' => 'Status do report atualizado com sucesso!', 'report' => $report]);
    }

    public function updateStatus(Request $request, $id)
{
    $user = $request->user();
    if (!$user->isAdmin()) {
        return response()->json(['message' => 'Acesso negado. Apenas administradores podem alterar o status.'], 403);
    }

    $request->validate([
        'status' => 'required|in:aberto,em_análise,concluído',
    ]);

    $report = Report::findOrFail($id);
    $report->status = $request->input('status');
    $report->save();

    return response()->json(['message' => 'Status do report atualizado com sucesso!', 'report' => $report]);
}

// Exclui um report
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return response()->json(['message' => 'Report excluído com sucesso!']);
    }



public function addMessage(Request $request, $id)
    {
        $request->validate([
            'mensagem' => 'required|string|max:1000',
            'imagem' => 'nullable|image',
        ]);

        $report = Report::findOrFail($id);

        $messageData = [
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'mensagem' => $request->input('mensagem'),
        ];

        if ($request->hasFile('imagem')) {
            $fileName = $request->file('imagem')->store('reports', 's3');
            $messageData['imagem'] = $fileName;
        }

        $report->messages()->create($messageData);

        return response()->json(['message' => 'Mensagem adicionada com sucesso!', 'report' => $report->load('messages.user')], 201);
    }
}

