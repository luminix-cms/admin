# luminix/backend

O `luminix/backend` gera automaticamente endpoints RESTful a partir de modelos Eloquent, com filtragem avançada, controle de acesso por Gates e gerenciamento de relacionamentos.

> Documentação de referência do pacote [`luminix/backend`](https://github.com/luminix-cms/backend).

---

## Instalação

O pacote é instalado automaticamente como dependência do `luminix/admin`. Para instalá-lo isoladamente:

```bash
composer require luminix/backend
```

Publique a configuração:

```bash
php artisan vendor:publish --tag=luminix-config
```

---

## Uso básico

Adicione a trait `LuminixModel` a qualquer modelo Eloquent para expô-lo via API e no painel:

```php
// app/Models/Post.php
use Illuminate\Database\Eloquent\Model;
use Luminix\Backend\Model\LuminixModel;

class Post extends Model
{
    use LuminixModel;

    protected $fillable = ['title', 'body', 'published_at'];
}
```

O modelo aparece automaticamente na navegação lateral do painel e recebe os seguintes endpoints:

| Método | URL | Ação |
|--------|-----|------|
| `GET` | `/luminix-api/posts` | Listagem paginada |
| `POST` | `/luminix-api/posts` | Criação |
| `GET` | `/luminix-api/posts/{id}` | Busca por ID |
| `POST` | `/luminix-api/posts/{id}` | Atualização |
| `DELETE` | `/luminix-api/posts/{id}` | Exclusão |
| `DELETE` | `/luminix-api/posts` | Exclusão em lote |
| `POST` | `/luminix-api/posts/restore` | Restauração em lote (requer SoftDeletes) |

---

## Filtragem e busca

### Busca textual

```
GET /luminix-api/posts?q=laravel
```

Busca o termo em todos os campos `fillable` do modelo.

### Operadores de filtro

```
GET /luminix-api/posts?where[title]=contains:laravel
GET /luminix-api/posts?where[published_at]=between:2024-01-01,2024-12-31
GET /luminix-api/posts?where[body]=null
```

Operadores disponíveis:

| Operador | Descrição | Exemplo |
|----------|-----------|---------|
| `equals` | Igual a | `where[status]=equals:active` |
| `notEquals` | Diferente de | `where[status]=notEquals:draft` |
| `contains` | Contém (LIKE) | `where[title]=contains:laravel` |
| `startsWith` | Começa com | `where[title]=startsWith:Intro` |
| `endsWith` | Termina com | `where[title]=endsWith:Guide` |
| `greaterThan` | Maior que | `where[views]=greaterThan:100` |
| `lessThan` | Menor que | `where[views]=lessThan:1000` |
| `greaterThanOrEquals` | Maior ou igual | `where[price]=greaterThanOrEquals:50` |
| `lessThanOrEquals` | Menor ou igual | `where[price]=lessThanOrEquals:200` |
| `between` | Entre dois valores | `where[date]=between:2024-01-01,2024-12-31` |
| `notBetween` | Fora do intervalo | `where[date]=notBetween:2024-01-01,2024-12-31` |
| `null` | Campo nulo | `where[deleted_at]=null` |
| `notNull` | Campo não nulo | `where[deleted_at]=notNull` |
| `in` | Em uma lista | `where[status]=in:active,pending` |
| `relation` | Filtro em relação | `where[author]=relation:1` |

### Paginação e ordenação

```
GET /luminix-api/posts?page=2&per_page=25&order_by=created_at&order=desc
```

| Parâmetro | Descrição | Padrão |
|-----------|-----------|--------|
| `page` | Página atual | `1` |
| `per_page` | Itens por página | `15` |
| `order_by` | Campo de ordenação | `id` |
| `order` | Direção (`asc`/`desc`) | `asc` |

### Abas (tabs)

Filters predefinidos acessíveis via parâmetro `tab`:

```
GET /luminix-api/posts?tab=trashed   # apenas registros excluídos (SoftDeletes)
```

---

## Segurança {#segurança}

### Gates por operação

O backend verifica Gates seguindo a convenção `{permissao}-{modelo}`:

```php
Gate::define('read-post', fn (?User $user) => true);           // leitura pública
Gate::define('create-post', fn (?User $user) => (bool) $user); // apenas autenticados
Gate::define('update-post', fn (User $user, Post $post) => $user->id === $post->user_id);
Gate::define('delete-post', fn (User $user, Post $post) => $user->id === $post->user_id);
```

### Controle por linha com `scopeAllowed`

```php
public function scopeAllowed(Builder $query, string $permission): void
{
    if (in_array($permission, ['update', 'delete'])) {
        $query->where('user_id', auth()->id());
    }
}
```

### Configuração de segurança

```php
// config/luminix/backend.php
'security' => [
    'gates_enabled' => true,
    'middleware' => ['api', 'auth'],
    'permissions' => [
        'index'   => 'read',
        'show'    => 'read',
        'store'   => 'create',
        'update'  => 'update',
        'destroy' => 'delete',
        'restore' => 'restore',
    ],
],
```

---

## Validação

### Inline no modelo

```php
public function getValidationRules(string $operation): array
{
    return [
        'title' => 'required|string|max:255',
        'body'  => 'required|string',
    ];
}
```

### Via atributo `#[WithValidator]`

```php
use Luminix\Backend\Attributes\WithValidator;

#[WithValidator(PostValidator::class)]
class Post extends Model
{
    use LuminixModel;
}
```

```php
// app/Http/Requests/PostValidator.php
class PostValidator
{
    public function rules(string $operation): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body'  => ['required', 'string'],
        ];
    }
}
```

---

## Eventos

O backend dispara 10 eventos específicos do Luminix durante operações de API:

| Evento | Quando |
|--------|--------|
| `luminixCreating` | Antes de criar |
| `luminixCreated` | Após criar |
| `luminixUpdating` | Antes de atualizar |
| `luminixUpdated` | Após atualizar |
| `luminixSaving` | Antes de criar ou atualizar |
| `luminixSaved` | Após criar ou atualizar |
| `luminixDeleting` | Antes de excluir |
| `luminixDeleted` | Após excluir |
| `luminixRestoring` | Antes de restaurar |
| `luminixRestored` | Após restaurar |

```php
use Illuminate\Support\Facades\Event;
use Luminix\Backend\Events\LuminixSaved;

Event::listen(LuminixSaved::class, function (LuminixSaved $event) {
    // $event->model → instância do modelo
    // $event->operation → 'create' ou 'update'
});
```

---

## Relacionamentos

O backend suporta operações de relacionamento `BelongsToMany` e `MorphToMany`:

```
POST /luminix-api/posts/{id}/sync    → sincroniza relação (substitui)
POST /luminix-api/posts/{id}/attach  → adiciona à relação
POST /luminix-api/posts/{id}/detach  → remove da relação
```

```json
// Body da requisição
{
    "relation": "tags",
    "ids": [1, 2, 3],
    "pivot": { "order": 1 }
}
```

---

## Configuração {#configuração}

```php
// config/luminix/backend.php
return [
    'api' => [
        'prefix'       => 'luminix-api',   // prefixo das rotas
        'max_per_page' => 150,              // máximo de itens por página
        'filter' => [
            'enable'  => true,
            'throw'   => true,
            'exclude' => [],                // campos excluídos do filtro
        ],
    ],
    'security' => [
        'gates_enabled' => true,
        'middleware'    => ['api', 'auth'],
    ],
];
```

---

## Próximos passos

- Entenda como os modelos aparecem no frontend em [luminix/frontend](frontend.md).
- Configure o controle de acesso em [Autorização](autorizacao.md).
- Veja os componentes do painel em [@luminix/mui-cms](js-mui-cms.md).
- Volte ao [índice](index.md).
