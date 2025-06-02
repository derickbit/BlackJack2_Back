<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Partidas</title>
</head>
<body>
    <h1>Partidas</h1>
    <h2><a href={{route('denuncia.index')}}>Denuncias</a></h2>
    @if ($partidas->count()>0)
    <table>
        <thead>
            <tr>
                <th>Partida</th>
                <th>Jogador1</th>
                <th>Jogador2</th>
                <th>Vencedor</th>
                <th>Pontuação</th>
            </tr>
        </thead>
        <tbody>
            <a href="{{route('criarpartidas')}}">Registrar Partida</a>
            @foreach($partidas as $partida)
            <tr>
                <td> <a href="{{route('showpartidas',$partida->codpartida)}}">{{$partida->codpartida}}</td></a>
                <td>{{$partida->Jogador1}}</td>
                <td>{{$partida->Jogador2}}</td>
                <td>{{$partida->Vencedor}}</td>
                <td>{{($partida->pontuacao)}}</td>
                <td><a href="{{route('editpartidas',$partida->codpartida)}}">Editar</a>
                    <a href="{{route('deletepartidas',$partida->codpartida)}}">Excluir</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>Partida não encontrados! </p>
    @endif
</body>
</html>
