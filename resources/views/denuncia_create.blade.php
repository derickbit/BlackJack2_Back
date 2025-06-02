<h1>Nova Denúncia</h1>
<form action="{{ route('denuncia.store') }}" method="POST">
    @csrf
    <table>
        <tr>
            <td>Codigo denuncia:</td>
            <td><input type="text" name="coddenuncia" required /></td>
        </tr>
          <tr>
            <td>Denunciante:</td>
            <td><input type="text" name="denunciante" required /></td>
        </tr>
        <tr>
            <td>Denunciado:</td>
            <td><input type="text" name="denunciado" required /></td>
        </tr>
        <tr>
            <td>Descrição:</td>
            <td><textarea name="descricao" required></textarea></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" value="Criar" /></td>
        </tr>
    </table>
</form>
<a href="/denuncias">&#9664; Voltar</a>
