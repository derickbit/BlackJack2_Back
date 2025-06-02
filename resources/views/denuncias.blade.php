<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Partidas</title>
</head>
<body>
    <h1>Denúncias</h1>
<h2><a href={{route('indexpartidas')}}>Partidas</a></h2>
@if ($denuncias->count() > 0)
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Denunciante</th>
            <th>Denunciado</th>
            <th>Descrição</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($denuncias as $denuncia)
        <tr>
            <td><a href="{{ route('denuncia.show', $denuncia->coddenuncia) }}">{{ $denuncia->coddenuncia }}</a></td>
            <td>{{ $denuncia->denunciante }}</td>
            <td>{{ $denuncia->denunciado }}</td>
            <td>{{ $denuncia->descricao }}</td>
            <td>{{ $denuncia->reg_date }}</td>
            <td>
                <a href="{{ route('denuncia.edit', $denuncia->coddenuncia) }}">Editar</a>
                <a href="{{ route('denuncia.delete', $denuncia->coddenuncia) }}">Excluir</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<a href="{{ route('denuncia.create') }}">Nova denúncia</a>
@else
<p>Nenhuma denúncia encontrada!</p>
@endif
</body>
</html>
