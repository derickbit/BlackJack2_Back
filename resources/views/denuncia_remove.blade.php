<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remover Denúncia</title>
</head>

<body>
    <h1>Remover Denúncia</h1>
    @if ($denuncia)
        <p>Tem certeza que deseja remover a denúncia abaixo?</p>
        <ul>
            <li><strong>ID:</strong> {{ $denuncia->coddenuncia }}</li>
            <li><strong>Denunciante:</strong> {{ $denuncia->denunciado }}</li>
            <li><strong>Denunciado:</strong> {{ $denuncia->denunciante }}</li>
            <li><strong>Descrição:</strong> {{ $denuncia->descricao }}</li>
        </ul>
        <form action="{{ route('denuncia.remove', $denuncia->coddenuncia) }}" method="POST">
            @csrf
            <input type="submit" name="confirmar" value="Deletar">
        </form>
    @else
        <p>Denúncia não encontrada.</p>
    @endif
    <a href="{{ route('denuncia.index') }}">&#9664; Voltar</a>
</body>

</html>
