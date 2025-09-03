# API do Fórum - BlackJack Laravel

## Visão Geral

O sistema de fórum permite que os usuários criem tópicos de discussão, comentem, respondam a comentários, curtam conteúdo e mencionem outros usuários usando @username.

## Estrutura das Tabelas

### forum_topics

-   `id`: ID do tópico
-   `user_id`: ID do usuário que criou
-   `titulo`: Título do tópico
-   `conteudo`: Conteúdo do tópico
-   `imagem`: Path da imagem (opcional)
-   `fixado`: Se o tópico está fixado (boolean)
-   `fechado`: Se o tópico está fechado para novos comentários (boolean)
-   `visualizacoes`: Contador de visualizações

### forum_comments

-   `id`: ID do comentário
-   `forum_topic_id`: ID do tópico
-   `user_id`: ID do usuário que comentou
-   `parent_id`: ID do comentário pai (para respostas)
-   `conteudo`: Conteúdo do comentário
-   `imagem`: Path da imagem (opcional)
-   `editado`: Se o comentário foi editado (boolean)

### forum_likes

-   `id`: ID do like
-   `user_id`: ID do usuário que curtiu
-   `likeable_type`: Tipo do modelo (ForumTopic ou ForumComment)
-   `likeable_id`: ID do tópico ou comentário curtido

### forum_mentions

-   `id`: ID da menção
-   `user_id`: ID do usuário mencionado
-   `mentioned_by_user_id`: ID do usuário que fez a menção
-   `mentionable_type`: Tipo do modelo onde foi mencionado
-   `mentionable_id`: ID do tópico ou comentário
-   `lida`: Se a menção foi lida (boolean)

## Rotas da API

### Rotas Públicas (Visualização)

#### GET /api/forum/topics

Lista todos os tópicos do fórum
**Parâmetros de Query:**

-   `fixados`: boolean - Filtrar por tópicos fixados
-   `fechados`: boolean - Filtrar por tópicos fechados
-   `search`: string - Buscar por título ou conteúdo
-   `per_page`: integer - Itens por página (padrão: 15)

#### GET /api/forum/topics/{id}

Visualiza um tópico específico com seus comentários

#### GET /api/forum/comments

Lista comentários
**Parâmetros de Query:**

-   `topic_id`: integer - Filtrar por tópico
-   `parent_id`: integer - Filtrar por comentário pai
-   `per_page`: integer - Itens por página (padrão: 20)

### Rotas Protegidas (Requer Autenticação)

#### Tópicos

-   `POST /api/forum/topics` - Criar novo tópico
-   `PUT/PATCH /api/forum/topics/{id}` - Atualizar tópico
-   `DELETE /api/forum/topics/{id}` - Deletar tópico
-   `POST /api/forum/topics/{id}/like` - Toggle like no tópico

#### Comentários

-   `POST /api/forum/comments` - Criar novo comentário
-   `PUT/PATCH /api/forum/comments/{id}` - Atualizar comentário
-   `DELETE /api/forum/comments/{id}` - Deletar comentário
-   `POST /api/forum/comments/{id}/like` - Toggle like no comentário
-   `GET /api/forum/comments/{id}/replies` - Obter respostas de um comentário

#### Likes

-   `POST /api/forum/like/toggle` - Toggle like genérico
-   `GET /api/forum/like/liked-by` - Ver quem curtiu um item

#### Menções

-   `GET /api/forum/mentions` - Listar menções do usuário
-   `PATCH /api/forum/mentions/{id}/read` - Marcar menção como lida
-   `PATCH /api/forum/mentions/read-all` - Marcar todas as menções como lidas
-   `GET /api/forum/mentions/unread-count` - Contar menções não lidas

## Funcionalidades Implementadas

### 1. Sistema de Tópicos

-   ✅ Criação, edição, exclusão de tópicos
-   ✅ Upload de imagens
-   ✅ Tópicos fixados (pinned)
-   ✅ Tópicos fechados (apenas admins podem comentar)
-   ✅ Contador de visualizações
-   ✅ Sistema de likes

### 2. Sistema de Comentários

-   ✅ Comentários aninhados (respostas)
-   ✅ Upload de imagens
-   ✅ Edição de comentários (marca como editado)
-   ✅ Sistema de likes
-   ✅ Verificação se tópico está fechado

### 3. Sistema de Likes

-   ✅ Like/Unlike em tópicos e comentários
-   ✅ Contador de likes
-   ✅ Verificação se usuário já curtiu
-   ✅ Lista de usuários que curtiram

### 4. Sistema de Menções

-   ✅ Detecção automática de @username no conteúdo
-   ✅ Criação de notificações de menção
-   ✅ Marcar menções como lidas
-   ✅ Contador de menções não lidas

### 5. Permissões e Segurança

-   ✅ Usuários só podem editar/deletar próprio conteúdo
-   ✅ Admins podem editar/deletar qualquer conteúdo
-   ✅ Admins podem fixar/fechar tópicos
-   ✅ Verificação de tópicos fechados

### 6. Upload de Imagens

-   ✅ Suporte a imagens em tópicos e comentários
-   ✅ Integração com AWS S3
-   ✅ Validação de tipos de arquivo
-   ✅ Remoção automática de imagens ao deletar conteúdo

## Exemplos de Uso

### Criar um Tópico

```bash
curl -X POST "http://localhost:8000/api/forum/topics" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "titulo": "Estratégias de BlackJack",
    "conteudo": "Quais são as melhores estratégias para jogar BlackJack? @admin gostaria da sua opinião!"
  }'
```

### Comentar em um Tópico

```bash
curl -X POST "http://localhost:8000/api/forum/comments" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "forum_topic_id": 1,
    "conteudo": "Ótima pergunta! @usuario1 você já testou a estratégia básica?"
  }'
```

### Curtir um Tópico

```bash
curl -X POST "http://localhost:8000/api/forum/topics/1/like" \
  -H "Authorization: Bearer {token}"
```

### Ver Menções Não Lidas

```bash
curl -X GET "http://localhost:8000/api/forum/mentions?unread=true" \
  -H "Authorization: Bearer {token}"
```

## Models e Relacionamentos

### ForumTopic

-   `belongsTo(User::class)` - Usuário criador
-   `hasMany(ForumComment::class)` - Comentários
-   `morphMany(ForumLike::class)` - Likes
-   `morphMany(ForumMention::class)` - Menções

### ForumComment

-   `belongsTo(ForumTopic::class)` - Tópico
-   `belongsTo(User::class)` - Usuário
-   `belongsTo(ForumComment::class, 'parent_id')` - Comentário pai
-   `hasMany(ForumComment::class, 'parent_id')` - Respostas
-   `morphMany(ForumLike::class)` - Likes
-   `morphMany(ForumMention::class)` - Menções

### ForumLike

-   `belongsTo(User::class)` - Usuário que curtiu
-   `morphTo()` - Item curtido (tópico ou comentário)

### ForumMention

-   `belongsTo(User::class, 'user_id')` - Usuário mencionado
-   `belongsTo(User::class, 'mentioned_by_user_id')` - Usuário que mencionou
-   `morphTo()` - Item onde foi mencionado

### User (relacionamentos adicionados)

-   `hasMany(ForumTopic::class)` - Tópicos criados
-   `hasMany(ForumComment::class)` - Comentários feitos
-   `hasMany(ForumLike::class)` - Likes dados
-   `hasMany(ForumMention::class, 'user_id')` - Menções recebidas
-   `hasMany(ForumMention::class, 'mentioned_by_user_id')` - Menções feitas

## Próximos Passos Sugeridos

1. **Notificações em Tempo Real**: Implementar WebSockets para notificações de menções
2. **Sistema de Moderação**: Relatórios de conteúdo impróprio
3. **Badges/Conquistas**: Sistema de gamificação para usuários ativos
4. **Busca Avançada**: Busca por tags, categorias, etc.
5. **Sistema de Categorias**: Organizar tópicos por categorias
6. **Histórico de Edições**: Manter histórico das edições de posts
