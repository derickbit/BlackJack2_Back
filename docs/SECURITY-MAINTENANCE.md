# Manutenção pontual de segurança — CardNest

Base revisada: `db71561e4dafcf5eb403825999404bed48a16aac`.
Branch local: `maintenance/security-local-setup`.

## Estado

Correções enviadas somente à branch de manutenção para validação pelo GitHub Actions.
Não há merge na master, deploy, migração ou alteração de credenciais de produção
nesta etapa. Não considerar esta revisão uma auditoria
completa nem uma confirmação de exploração das falhas. A versão publicada continua
vulnerável até que uma versão corrigida seja validada e implantada.

Verificação local realizada: 131 arquivos PHP analisados pelo parser JavaScript
`php-parser` 3.7.0, sem erros sintáticos; `git diff --check` sem erros; configuração
XML da suite válida; coleções Postman válidas e sem os tokens literais detectados.
Foram definidos 27 testes. O GitHub Actions executa agora a suite com PHP 8.3 e
SQLite em memória, sem instalar PHP no PC. Consulte o resultado do commit exato
na aba Actions antes de aprovar um deploy; a análise estática sozinha não basta.

## Alterações

- A atualização de perfil só aceita a própria conta; nem administrador pode usar
  esse endpoint para trocar a senha de outra pessoa. Exclusão: dono ou administrador.
- A troca de senha valida `current_password`. O formulário publicado de alteração
  de nome envia a senha atual em `password`; esse formato permanece aceito apenas
  se a senha estiver correta, sem permitir trocar a senha por essa alternativa.
- A resposta de edição preserva `id` e `role`, pois o frontend substitui o estado
  da sessão pela resposta. Ela continua sem senha/hash nem `current_password`.
- Consultas públicas de usuários mantêm as URLs e o envelope `data`, mas retornam
  apenas `id` e `name`. `/api/user` continua autenticado, sem envelope, com os dados
  da própria conta. A resposta de atualização não contém mais o hash da senha.
- A serialização genérica de `User` oculta e-mail e verificação de e-mail para evitar
  vazamento em relacionamentos do fórum e do suporte. Nome e papel continuam disponíveis.
- Chamados e mensagens só podem ser lidos/respondidos pelo dono ou administrador.
  Exclusão de chamado: dono ou administrador. Mudança de status: apenas administrador,
  tanto na rota dedicada quanto na rota de atualização do recurso.
- Métodos de edição/exclusão de mensagens, ainda não expostos nas rotas atuais,
  também verificam vínculo com o chamado e autoria/administração.
- Sete ocorrências de tokens Postman foram substituídas por `{{auth_token}}`.
  Configure essa variável localmente, sem exportar seu valor para o repositório.
- Seeders de demonstração recusam ambientes diferentes de `local`/`testing` e não
  criam administradores com senhas fixas. Isso NÃO muda contas já existentes.
- `.env.testing` não guarda mais a chave anterior e aponta para SQLite em memória.

## Validação isolada

Não é necessário instalar PHP ou Docker no PC para usar o workflow preparado.
O workflow `.github/workflows/security-tests.yml` roda no envio da branch
`maintenance/security-local-setup`, em pull requests destinados à `master` ou por
acionamento manual quando disponível no GitHub. Ele não faz deploy,
não recebe secrets e instala as versões de `composer.lock`, sem scripts da aplicação.
As Actions ainda precisam ser habilitadas/disponíveis na conta.

Para executar em outro ambiente descartável com PHP 8.3, Composer e SQLite:

```sh
composer install --no-interaction --prefer-dist --no-scripts --no-plugins
vendor/bin/phpunit -c phpunit.security.xml --fail-on-warning --fail-on-risky
```

Não executar testes na Heroku nem em uma pasta contendo dados reais. O harness
recusa configuração em cache, gera uma APP_KEY efêmera, força ambiente de testes,
remove conexões de banco externas e recria apenas um banco SQLite em memória.
E-mail usa o transporte `array`; os testes não fazem uploads nem chamadas à AWS.

Esta suite é focada nos endpoints alterados. Os testes legados `Denuncia*` se
referem a um modelo/rota antigos e não são cobertos por este workflow. A aprovação
desta suite não equivale à aprovação de toda a aplicação.

## Antes de qualquer deploy

1. Confirmar a aprovação da suite no GitHub Actions para o commit que será publicado
   e revisar eventuais falhas. A execução usa ambiente descartável, não a Heroku.
2. Revisão estática do frontend publicado concluída: login/perfil usam `/api/user`;
   a lista de menções usa `id`/`name` e trata e-mail como opcional; o suporte usa as
   rotas autenticadas existentes e só mostra mudança de status para administrador.
   O retorno de edição foi ajustado para preservar a sessão. As pontuações de
   HiLo/BlackJack são enviadas por `/partidas`, que não foi alterada neste patch.
   Isso não substitui um teste completo no navegador com o backend atualizado.
   Há uma página antiga `EditarPerfil.jsx`, não referenciada nas rotas atuais,
   que precisa de campo `current_password` se vier a ser reativada para trocar senha.
3. Validar login, cadastro/verificação, edição do próprio perfil, ranking/jogos,
   abertura/resposta de chamado e atendimento administrativo em homologação.
4. Identificar o provedor do banco da Heroku, confirmar um backup e o procedimento
   de recuperação. Não executar `migrate:fresh`, `db:wipe` ou seeders em produção.
   Este patch não altera o esquema do banco e não exige migrações.
5. Conferir se contas de administrador criadas por seeders ainda usam as senhas
   antigas. Se sim, trocar senhas e revogar os tokens correspondentes. Verificar
   se os tokens Postman pertencem ao banco publicado e revogá-los se necessário.
6. Comparar privadamente a APP_KEY publicada com a antiga chave de testes. Se
   coincidem, planejar rotação com cuidado com dados criptografados e sessões.
   Não gerar uma chave nova às cegas nem enviar os valores por chat.
7. Confirmar `APP_ENV=production` e `APP_DEBUG=false` na Heroku, sem copiar suas
   configurações para o computador ou para o GitHub.
8. Publicar somente com aprovação: a `master` está ligada ao deploy automático.
   Registrar a release anterior e um plano de recuperação; voltar à release antiga
   também reintroduz as vulnerabilidades. Considerar manutenção temporária.

## Limites e pendências

- Os valores removidos continuam no histórico público. Limpeza do código não
  revoga credenciais; corrigir ou reescrever histórico não substitui a rotação.
- Anexos do suporte ainda usam armazenamento público. Este patch protege as rotas,
  mas não torna privados arquivos já acessíveis por URL. É necessário revisar
  os objetos/políticas do bucket e implementar acesso autorizado em etapa própria.
- A API de partidas ainda precisa de revisão de autoria, pontuação enviada pelo
  cliente e operações de alteração/exclusão. Este patch não implementa antitrapaça.
- Laravel 11 e dependências precisam de atualização/auditoria separadas.
- Não foram testados a configuração real da Heroku, o banco de produção, os uploads,
  o envio real de e-mails nem as integrações de frontend e Unity.
