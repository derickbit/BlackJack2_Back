<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Editar Partida</title>
</head>

<body>
    <h1>Editar Partida</h1>
    <form action="{{route('updatepartidas',$partida->codpartida)}}" method="POST">
        @csrf
        <table>
            <tr>
                <td>Jogador1:</td>
                <td><input type="text" name="Jogador1" value="{{$partida->Jogador1}}"/></td>
            </tr>
            <tr>
                <td>Jogador2:</td>
                <td><input type="text" name="Jogador2" value="{{$partida->Jogador2}}"/></td>
            </tr>
            <tr>
                <td>Vencedor:</td>
                <td><input type="text" name="Vencedor" value="{{$partida->Vencedor}}"/></td>
            </tr>
            <tr>
                <td>Pontuação:</td>
                <td><input type="number" name="pontuacao" value="{{$partida->pontuacao}}"/></td>
            </tr>
            <tr align="center">
                <td colspan="2">
                    <input type="submit" value="Salvar"/>
                    <a href="/partidas"><button form=cancel >Cancelar</button></a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
