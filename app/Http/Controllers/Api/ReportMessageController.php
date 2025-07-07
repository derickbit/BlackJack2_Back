<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportMessage;
use Illuminate\Http\Request;

class ReportMessageController extends Controller
{
    // Lista todas as mensagens de um report
    public function index(Report $report)
    {
        $messages = $report->messages()->with('user')->orderBy('created_at')->get();
        return response()->json($messages);
    }

    // Adiciona uma mensagem ao report (ADM ou usuário)
    public function store(Request $request, Report $report)
    {
    if ($report->status === 'concluído') {
        return response()->json(['message' => 'Não é possível adicionar mensagens a um report concluído.'], 403);
    }

    $request->validate([
        'mensagem' => 'required|string|max:1000',
        'imagem' => 'nullable|image',
    ]);

        $messageData = [
            'report_id' => $report->id,
            'user_id' => $request->user()->id,
            'mensagem' => $request->input('mensagem'),
        ];

        if ($request->hasFile('imagem')) {
            $fileName = $request->file('imagem')->store(
    'reports/messages',
    's3',
    ['visibility' => 'public']
);
            $messageData['imagem'] = $fileName;
        }

        $message = $report->messages()->create($messageData);

        return response()->json(['message' => 'Mensagem enviada!', 'data' => $message->load('user')], 201);
    }


    // Exibe uma mensagem específica de um report
    public function show(Report $report, ReportMessage $message)
    {
        return response()->json($message->load('user'));
    }

    // Atualiza uma mensagem de um report (ADM ou usuário)
    public function update(Request $request, Report $report, ReportMessage $message)
    {
        $request->validate([
            'mensagem' => 'required|string|max:1000',
            'imagem' => 'nullable|image',
        ]);

        $message->update([
            'mensagem' => $request->input('mensagem'),
        ]);

        if ($request->hasFile('imagem')) {
            $fileName = $request->file('imagem')->store('reports', 'public');
            $message->imagem = $fileName;
            $message->save();
        }

        return response()->json(['message' => 'Mensagem atualizada!', 'data' => $message->load('user')]);
    }

    // Exclui uma mensagem de um report (ADM ou usuário)
    public function destroy(Report $report, ReportMessage $message)
    {
        $message->delete();
        return response()->json(['message' => 'Mensagem excluída com sucesso!']);
    }
}




