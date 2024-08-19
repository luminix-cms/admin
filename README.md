## Requisitos funcionais package ADMIN

### 1. Configurações

Configuração deverá ser publicável em `config/luminix/admin.php`.

Estrutura de configuração inicial:

```php
[
    'url' => env('LUMINIX_ADMIN_URL', '/admin'),
    'middleware' => ['web', 'auth'],
    'locales' => ['en', 'pt-BR'],
]
```

 i.   A configuração deverá ser documentada seguindo os padrões dos outros pacotes luminix
 ii.  A configuração deverá registrar o seu padrão e também ser publicável para alterações no projeto
 iii. A configuração 'luminix.admin.url' deverá ser passada ao frontend APENAS QUANDO o usuário estiver acessando o painel Admin

### 2. Views e Rotas

As rotas do Luminix Admin serão registradas em um grupo com o prefixo `/admin` e as middlewares configuradas.

Deverão ser registradas as rotas para um painel e duas rotas para cada modelo.

As rotas seguirão o seguinte padrão:

```php
// group: /admin/*

Route::get('/'); // Dashboard

// as models terão o nome plural traduzido convertido em kebab-case
$modelPrefix = Str::kebab(__(Model::getDisplayName()['plural']));

Route::get('/' . $modelPrefix); // Lista de itens

Route::get('/' . $modelPrefix . '/create'); // Criar um item

// rotas de telas de edição terão que usar como parâmetro o nome da chave primária e também considerar o tipo da chave primaria
// https://laravel.com/docs/11.x/routing#parameters-regular-expression-constraints

$primaryKey = (new Model)->getKeyName();
$keyType = (new Model)->getKeyType();

$rule = $keyType === 'string'
    ? '[A-Za-z]+'
    : '[0-9]+';

Route::get('/' . $modelPrefix . '/{' . $primaryKey . '}')->where($primaryKey, $rule); // Editar item
```

Todas as rotas deverão apontar para um único método em uma controller, que retornará a única view do projeto.

Esta view será um html para uma aplicação react, com uma `<div id="root">`, onde será renderizado o `js-mui-cms`.

### 3. Traduções

O pacote Admin poderá prover traduções para o painel. As traduções seguirão o padrão de localização do Laravel 11 usando arquivos JSON. Inicialmente o projeto terá apenas tradução para o português. Uma versão inicial dessa tradução ja foi feita em um projeto de teste:

```json
// lang/pt-BR.json
{
    ":from–:to of :total": ":from–:to de :total",
    ":model saved successfully": ":model salvo com sucesso",
    "Add Column": "Adicionar Coluna",
    "All": "Todos",
    "Apply": "Aplicar",
    "Are you sure you want to delete permanently :count :model?": "Tem certeza que deseja excluir :count :model permanentemente?",
    "Are you sure you want to delete :model “:label” permanently?": "Tem certeza que deseja excluir o(a) :model “:label” permanentemente?",
    "Are you sure you want to restore :count :model?": "Tem certeza que deseja restaurar :count :model?",
    "Are you sure you want to restore :model “:label”?": "Tem certeza que deseja restaurar o(a) :model “:label”?",
    "Are you sure you want to send to trash :count :model?": "Tem certeza que deseja enviar :count :model para a lixeira?",
    "Are you sure you want to send :model “:label” to trash?": "Tem certeza que deseja enviar o(a) :model “:label” para a lixeira?",
    "Ascending": "Ascendente",
    "between": "entre",
    "Clear": "Limpar",
    "Column": "Coluna",
    "Confirm Password": "Confirmar Senha",
    "Confirm permanent deletion": "Confirmar exclusão permanente",
    "Confirm restore": "Confirmar restauração",
    "Confirm send to trash": "Confirmar envio para a lixeira",
    "Create :model": "Criar :model",
    "Created At": "Criado em",
    "Dashboard": "Painel",
    "Date": "Data",
    "Delete permanently": "Excluir permanentemente",
    "Deleted At": "Excluído em",
    "Descending": "Descendente",
    "Direction": "Direção",
    "Edit :model “:label“": "Editar :model “:label”",
    "Email": "Email",
    "Email Verified At": "Email Verificado Em",
    "like": "similar",
    "Name": "Nome",
    "New": "Novo",
    "No": "Não",
    "No :model found": "Não há :model correspondentes",
    "None": "Nenhuma",
    "notBetween": "não está entre",
    "null": "nulo",
    "notNull": "não nulo",
    "Ok": "Ok",
    "Operator": "Operador",
    "Password": "Senha",
    "Restore": "Restaurar",
    "Rows per page": "Linhas por página",
    "Search...": "Buscar...",
    "Select action": "Selecione a ação",
    "Select items to apply": "Selecione um item",
    "Send to trash": "Enviar para a lixeira",
    "Sort :model": "Ordenar :model",
    "Successfully deleted :count :model": ":count :model excluídos(as) com sucesso",
    "Successfully deleted :model “:label”": ":model “:label” excluído(a) com sucesso",
    "Successfully restored :count :model": ":count :model restaurados(as) com sucesso",
    "Successfully restored :model “:label”": ":model “:label” restaurado(a) com sucesso",
    "Successfully sent to trash :count :model": ":count :model enviados(as) para a lixeira com sucesso",
    "Successfully sent :model “:label” to trash": ":model “:label” enviado(a) para a lixeira com sucesso",
    "Trashed": "Lixeira",
    "Updated At": "Atualizado em",
    "User": "Usuário",
    "Users": "Usuários",
    "Value": "Valor",
    "Yes": "Sim"
}
```

As traduções deverão ser passadas ao frontend para ser consumido pelo i18NextPlugin.
Para isso a configuração deverá ser passada da seguinte forma:

```php
BootService::reducer('wireConfig', function ($config) {
    return [
        ...$config,
        'trans' => __('*', [], config('app.locale', 'en')), // todas as traduções serão disponibilizadas nesta chave
        'luminix' => [
            ...$config['luminix'] ?? [],
            'admin' => [
                'locales' => config('luminix.admin.locales', ['en', 'pt-BR']),
                'filter' => [
                    'operators' => ModelFilter::operators(),
                ],
            ]
        ]
    ];
});
```

Por fim, deverá haver um endpoint para a [troca do locale da aplicação](https://laravel.com/docs/11.x/localization#configuring-the-locale).

### 4. Publicáveis com `vendor:publish`

#### 4.1. `--tag=luminix-admin-scaffold`

Deverá publicar um esqueleto para o CMS no projeto, para que o usuário possa acrescentar plugins e modificar o seu CMS. A utilização sem publicar é possível, mas sem nenhum plugin e com o tema padrão. Este esqueleto precisa ser criado em uma pasta `resources/js`.

#### 4.2. `--tag=luminix-config`

Publicação do arquivo de configuração

### 5. Dependências

Além das dependências do Laravel, o projeto dependerá do Luminix/Frontend e Luminix/Backend
