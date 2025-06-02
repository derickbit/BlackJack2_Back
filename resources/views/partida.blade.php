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
        <h1>PARTIDA {{ $partida->codpartida }}</h1>
        <p>VENCEDOR: {{ $partida->Vencedor }}</p>
        <ul>
            <li>{{ $partida->Jogador1 }}</li>
            <li>VS</li>
            <li>{{ $partida->Jogador2 }}</li>
            <li>PONTUAÇÃO: {{ $partida->pontuacao }}</li>
        </ul>
    @else
        <p>Partida não encontrados! </p>
    @endif
    <a href="{{route('indexpartidas')}}">&#9664;Voltar</a>
</body>
</html>
