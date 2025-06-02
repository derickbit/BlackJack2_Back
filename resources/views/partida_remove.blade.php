<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Partidas</title>
</head>
<body>
    @if ($partida)
    <h1>Editar Partida</h1>
        @csrf
        <table>
            <td>{{$partida->codpartida}}</td>
            <td>{{$partida->Jogador1}}</td>
            <td>{{$partida->Jogador2}}</td>
            <td>{{$partida->Vencedor}}</td>
            <td>{{($partida->pontuacao)}}</td>
            <tr>
                <td>
                    <form action="{{route('removepartidas',$partida->codpartida) }}" method='post'>
                        @csrf
                        <input type="submit" name='confirmar' value="Deletar" />
                    </form>
                </td>
                <td>
                    <a href="/partidas"><button>Cancelar</button></a>
                </td>
            </tr>
        </table>
    @else
        <p>Produtos não encontrados! </p>
    @endif
    <a href="/produtos">&#9664;Voltar</a>
</body>
</html>
