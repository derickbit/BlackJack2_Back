<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registrar Partida</title>
</head>

<body>
    <h1>Registrar Partida</h1>
    <form action="/partida" method="POST">

      <input type="hidden" name="_token" value="{{csrf_token()}}"/>
        <table>
            <tr>
                <td>Partida:</td>
                <td><input type="number" name="codpartida"/></td>
            </tr>
            <tr>
                <td>Jogador1:</td>
                <td><input type="text" name="Jogador1"/></td>
            </tr>
            <tr>
                <td>Jogador2:</td>
                <td><input type="text" name="Jogador2"/></td>
            </tr>
            <tr>
                <td>Vencedor:</td>
                <td><input type="text" name="Vencedor"/></td>
            </tr>
            <tr>
                <td>Pontuação:</td>
                <td><input type="number" name="pontuacao"/></td>
            </tr>
            <tr align="center">
                <td colspan="2"><input type="submit" value="Registrar"/></td>
            </tr>
            <tr align="center">
                <td colspan="2"><a href="/partidas" style="display: inline">&#9664;&nbsp;Voltar</a></td>
            </tr>
        </table>
    </form>
</body>

</html>
