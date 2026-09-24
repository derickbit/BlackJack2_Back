# CardNest — API backend

Backend da **CardNest**, plataforma web de jogos de cartas desenvolvida como
Trabalho de Conclusão de Curso em Tecnologia em Sistemas para Internet no IFSul,
Campus Pelotas, por **Dérick Bitencourte da Silva**.

O projeto conecta uma interface em React a jogos Hi-Lo e Blackjack desenvolvidos
em Unity, reunindo contas de jogadores, partidas, rankings e recursos de comunidade.
Este repositório contém a API em Laravel; a interface e os jogos ficam em projetos
separados.

[Conheça a plataforma](https://card-nest.vercel.app) ·
[Perfil do desenvolvedor](https://github.com/derickbit)

## Funcionalidades

- Cadastro, verificação de e-mail, login e recuperação de senha.
- Perfis de jogadores, registro de partidas, histórico e ranking.
- Fórum com tópicos, comentários, respostas, curtidas e menções.
- Chamados de suporte com troca de mensagens e anexos.
- Publicação de atualizações da plataforma (patch notes).
- Recursos administrativos para atendimento e gerenciamento de conteúdo.

## Tecnologias e integração

- **API:** PHP 8.2+, Laravel 11 e Laravel Sanctum.
- **Persistência:** MySQL e Eloquent ORM.
- **Arquivos:** integração com armazenamento compatível com Amazon S3.
- **Hospedagem do backend:** Heroku, com MySQL no JawsDB.
- **Clientes da plataforma:** React e jogos Unity/C# exportados para WebGL.
- **Validação:** PHPUnit e GitHub Actions, com banco SQLite em memória nos testes.

A API expõe recursos JSON sob `/api`. A interface web consome esses recursos para
autenticação, comunidade e suporte, além de intermediar o envio de pontuações dos
jogos. As rotas estão em [`routes/api.php`](routes/api.php).

## Organização do código

- `app/Http/Controllers`: endpoints e coordenação das operações.
- `app/Http/Requests`: validação e autorização de requisições.
- `app/Http/Resources`: formatos das respostas JSON.
- `app/Models`: entidades e relacionamentos.
- `app/Policies`: regras de autorização dos chamados.
- `database/migrations`: estrutura do banco de dados.
- `tests/Feature/Security`: testes de regressão de contas e suporte.
- `tests/API`: coleções do Postman; use uma variável local `auth_token`.

## Testes em ambiente isolado

Com PHP 8.3, Composer e as extensões `mbstring`, `dom`, `xml` e `pdo_sqlite`, em
uma cópia local descartável, sem credenciais de produção:

```sh
composer install --no-interaction --prefer-dist --no-scripts --no-plugins
vendor/bin/phpunit -c phpunit.security.xml --fail-on-warning --fail-on-risky
```

O harness de segurança gera uma chave efêmera, usa SQLite em memória e substitui
o envio de e-mails pelo transporte de testes. Não execute essa suite na Heroku
nem contra dados reais. O workflow também verifica a atualização do cache de
configuração durante a inicialização dos dynos.

## Estado do projeto

Projeto acadêmico em manutenção para apresentação no portfólio. A branch
`maintenance/security-local-setup` reúne correções e testes; suas alterações
não devem ser consideradas publicadas até a conclusão do deploy.

Os testes focados não equivalem a uma auditoria completa de segurança. O escopo,
as verificações e as pendências estão em
[`docs/SECURITY-MAINTENANCE.md`](docs/SECURITY-MAINTENANCE.md).

Não publique arquivos `.env` com credenciais, tokens, backups ou dados de usuários.
Para testar a aplicação completa, utilize banco, contas e serviços próprios de
desenvolvimento. Os seeders de demonstração são restritos a `local` e `testing`.
