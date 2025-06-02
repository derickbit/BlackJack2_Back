<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Denúncia</title>
</head>

<body>
    <h1>Editar Denúncia</h1>
    <form action="{{ route('denuncia.update', $denuncia->coddenuncia) }}" method="POST">
        @csrf
        <table>
            <tr>
                <td>Denunciante:</td>
                <td><input type="text" name="denunciante" value="{{ $denuncia->denunciante }}"></td>
            </tr>
            <tr>
                <td>Denunciado:</td>
                <td><input type="text" name="denunciado" value="{{ $denuncia->denunciado }}"></td>
            </tr>
            <tr>
                <td>Descrição:</td>
                <td><textarea name="descricao" cols="30" rows="10">{{ $denuncia->descricao }}</textarea></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Salvar">
                    <a href="{{ route('denuncia.index') }}"><button type="button">Cancelar</button></a>
                </td>
            </tr>
        </table>
    </form>
</body>

</html>
