# API de Atualizações (Patch Notes)

## Descrição

Sistema de patch notes que permite aos administradores criar, editar e excluir atualizações sobre o desenvolvimento do site e jogos. Os usuários comuns podem apenas visualizar as atualizações.

## Recursos Implementados

### Características

-   ✅ Criação, edição e exclusão de atualizações (apenas admins)
-   ✅ Visualização pública das atualizações
-   ✅ Upload de imagens opcional nas atualizações
-   ✅ Sistema de versionamento
-   ✅ Categorização por tipo (feature, bugfix, improvement, breaking)
-   ✅ Status ativo/inativo
-   ✅ Relacionamento com usuário autor
-   ✅ Paginação e filtros

### Estrutura da Tabela

```sql
- id (bigint, primary key)
- titulo (string, 255 chars)
- conteudo (text)
- imagem (string, nullable) - caminho da imagem
- versao (string, 50 chars, nullable) - ex: v1.2.3
- tipo (enum: feature, bugfix, improvement, breaking)
- ativo (boolean, default: true)
- user_id (foreign key para users)
- created_at (timestamp)
- updated_at (timestamp)
```

## Rotas da API

### Rotas Públicas (sem autenticação)

#### Listar Atualizações

```http
GET /api/atualizacoes
```

**Parâmetros de query:**

-   `tipo` (opcional): filtrar por tipo (feature, bugfix, improvement, breaking)
-   `page` (opcional): número da página para paginação

**Resposta de sucesso (200):**

```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "titulo": "Sistema de Atualizações Implementado",
                "conteudo": "Agora você pode acompanhar...",
                "imagem": "atualizacoes/exemplo.jpg",
                "imagem_url": "http://localhost:8000/storage/atualizacoes/exemplo.jpg",
                "versao": "v1.0.0",
                "tipo": "feature",
                "ativo": true,
                "created_at": "2025-08-30T23:30:00.000000Z",
                "updated_at": "2025-08-30T23:30:00.000000Z",
                "autor": {
                    "id": 1,
                    "name": "Administrador",
                    "email": "admin@blackjack.com"
                }
            }
        ],
        "per_page": 10,
        "total": 1
    }
}
```

#### Visualizar Atualização Específica

```http
GET /api/atualizacoes/{id}
```

**Resposta de sucesso (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "titulo": "Sistema de Atualizações Implementado",
        "conteudo": "Agora você pode acompanhar...",
        "imagem": "atualizacoes/exemplo.jpg",
        "imagem_url": "http://localhost:8000/storage/atualizacoes/exemplo.jpg",
        "versao": "v1.0.0",
        "tipo": "feature",
        "ativo": true,
        "created_at": "2025-08-30T23:30:00.000000Z",
        "updated_at": "2025-08-30T23:30:00.000000Z",
        "autor": {
            "id": 1,
            "name": "Administrador",
            "email": "admin@blackjack.com"
        }
    }
}
```

### Rotas Protegidas (requer autenticação + permissão de admin)

#### Criar Atualização

```http
POST /api/atualizacoes
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Parâmetros do corpo da requisição:**

-   `titulo` (obrigatório): string, máximo 255 caracteres
-   `conteudo` (obrigatório): texto
-   `imagem` (opcional): arquivo de imagem (jpeg, png, jpg, gif, webp), máximo 2MB
-   `versao` (opcional): string, máximo 50 caracteres
-   `tipo` (obrigatório): feature|bugfix|improvement|breaking
-   `ativo` (opcional): boolean, padrão true

**Resposta de sucesso (201):**

```json
{
    "success": true,
    "message": "Atualização criada com sucesso!",
    "data": {
        // objeto da atualização criada
    }
}
```

#### Editar Atualização

```http
PUT /api/atualizacoes/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Parâmetros:** mesmos da criação

**Resposta de sucesso (200):**

```json
{
    "success": true,
    "message": "Atualização editada com sucesso!",
    "data": {
        // objeto da atualização atualizada
    }
}
```

#### Excluir Atualização

```http
DELETE /api/atualizacoes/{id}
Authorization: Bearer {token}
```

**Resposta de sucesso (200):**

```json
{
    "success": true,
    "message": "Atualização excluída com sucesso!"
}
```

#### Alterar Status (Ativo/Inativo)

```http
PATCH /api/atualizacoes/{id}/toggle-status
Authorization: Bearer {token}
```

**Resposta de sucesso (200):**

```json
{
    "success": true,
    "message": "Status da atualização alterado com sucesso!",
    "data": {
        // objeto da atualização com status alterado
    }
}
```

#### Remover Imagem

```http
DELETE /api/atualizacoes/{id}/imagem
Authorization: Bearer {token}
```

**Resposta de sucesso (200):**

```json
{
    "success": true,
    "message": "Imagem removida com sucesso!",
    "data": {
        // objeto da atualização sem imagem
    }
}
```

## Códigos de Erro

### 400 - Bad Request

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "titulo": ["O título é obrigatório"],
        "imagem": ["A imagem não pode exceder 2MB"]
    }
}
```

### 401 - Unauthorized

```json
{
    "message": "Unauthenticated."
}
```

### 403 - Forbidden

```json
{
    "message": "Acesso negado. Apenas administradores podem realizar esta ação."
}
```

### 404 - Not Found

```json
{
    "message": "No query results for model [App\\Models\\Atualizacao] {id}"
}
```

## Permissões

### Usuários Comuns

-   ✅ Listar atualizações ativas
-   ✅ Visualizar atualização específica
-   ❌ Criar, editar ou excluir atualizações

### Administradores

-   ✅ Todas as permissões dos usuários comuns
-   ✅ Criar novas atualizações
-   ✅ Editar atualizações existentes
-   ✅ Excluir atualizações
-   ✅ Alterar status ativo/inativo
-   ✅ Gerenciar imagens

## Tipos de Atualização

-   **feature**: Nova funcionalidade
-   **bugfix**: Correção de bugs
-   **improvement**: Melhoria/otimização
-   **breaking**: Mudança que quebra compatibilidade

## Upload de Imagens

### Formatos Suportados

-   JPEG (.jpeg, .jpg)
-   PNG (.png)
-   GIF (.gif)
-   WebP (.webp)

### Limitações

-   Tamanho máximo: 2MB
-   Armazenamento: `storage/app/public/atualizacoes/`
-   URL pública: `{domain}/storage/atualizacoes/{filename}`

### Exemplos de Uso

#### Criar atualização com imagem (JavaScript)

```javascript
const formData = new FormData();
formData.append("titulo", "Nova Funcionalidade");
formData.append("conteudo", "Descrição da nova funcionalidade...");
formData.append("tipo", "feature");
formData.append("versao", "v1.1.0");
formData.append("imagem", fileInput.files[0]); // arquivo de imagem

fetch("/api/atualizacoes", {
    method: "POST",
    headers: {
        Authorization: `Bearer ${token}`,
    },
    body: formData,
});
```

#### Listar atualizações por tipo (JavaScript)

```javascript
fetch("/api/atualizacoes?tipo=feature")
    .then((response) => response.json())
    .then((data) => {
        console.log(data.data.data); // array de atualizações
    });
```

## Dados de Exemplo

O sistema inclui um seeder com dados de exemplo que pode ser executado com:

```bash
php artisan db:seed --class=AtualizacaoSeeder
```
