# luminix/frontend

O `luminix/frontend` é o pacote responsável por coletar os dados de inicialização da aplicação Laravel e entregá-los ao frontend JavaScript via diretiva Blade `@luminixEmbed()`.

> Documentação de referência do pacote [`luminix/frontend`](https://github.com/luminix-cms/frontend).

---

## Como funciona

Ao renderizar a view do painel, a diretiva `@luminixEmbed()` injeta um elemento HTML oculto com os dados de boot serializados em JSON:

```html
<div id="luminix-embed" data-boot="{...}" style="display:none;"></div>
```

O React SPA lê esse elemento na inicialização e usa os dados para configurar modelos, rotas, autenticação e traduções — sem precisar de chamadas API adicionais no carregamento da página.

---

## Estrutura dos dados de boot

```json
{
    "app": {
        "name": "Minha Aplicação",
        "env": "production",
        "debug": false,
        "url": "https://meusite.com",
        "locale": "pt_BR",
        "fallback_locale": "en"
    },
    "auth": {
        "user": { "id": 1, "name": "João Silva", "email": "joao@email.com" },
        "csrf": "token-csrf-aqui"
    },
    "manifest": {
        "models": {
            "post": {
                "displayName": "Post",
                "fillable": ["title", "body", "published_at"],
                "casts": { "published_at": "datetime" },
                "primaryKey": "id",
                "softDeletes": true,
                "relations": { "tags": "BelongsToMany" }
            }
        },
        "routes": {
            "posts.index": ["/luminix-api/posts", "get"],
            "posts.store": ["/luminix-api/posts", "post"],
            "posts.show":  ["/luminix-api/posts/{post}", "get"]
        }
    }
}
```

---

## Manifest de modelos {#manifest}

O manifest descreve cada modelo exposto pelo `luminix/backend`. Campos disponíveis por modelo:

| Campo | Descrição |
|-------|-----------|
| `displayName` | Nome legível do modelo (usado na interface) |
| `fillable` | Campos preenchíveis (base para formulários e busca) |
| `casts` | Casts definidos no modelo |
| `primaryKey` | Nome da chave primária |
| `labeledBy` | Campo usado como label do registro na interface |
| `timestamps` | Se o modelo usa `created_at`/`updated_at` |
| `softDeletes` | Se o modelo usa exclusão suave |
| `relations` | Relações registradas e seus tipos |
| `attributes` | Atributos e tipos de coluna (via `spatie/laravel-model-info`) |

### Visibilidade por autenticação

- Usuários **autenticados** recebem todos os modelos e rotas (exceto os em `exclude`)
- Usuários **não autenticados** recebem apenas os listados em `public`

Configure em `config/luminix/frontend.php`:

```php
'models' => [
    'public'  => ['post'],    // visíveis para visitantes
    'exclude' => ['audit'],   // nunca expostos
],
'routes' => [
    'public'  => ['login', 'logout', 'posts.index', 'posts.show'],
    'exclude' => ['horizon.*', 'telescope.*'],
],
```

---

## Comando Artisan

```bash
php artisan luminix:manifest
```

Gera um arquivo JSON com o manifest em `resources/js/config/manifest.json`. Útil quando `boot.includes_manifest` é `false` e o manifest deve ser importado estaticamente no bundle.

```bash
# Manifest completo (autenticado)
php artisan luminix:manifest

# Manifest público (apenas modelos e rotas liberados para visitantes)
php artisan luminix:manifest --no-auth

# Caminho personalizado
php artisan luminix:manifest --path=public/manifest.json
```

---

## Reducers {#reducers}

Os reducers são a forma recomendada de personalizar os dados entregues ao frontend. Eles transformam os dados em pontos específicos do pipeline, antes de serem serializados.

Registre reducers no método `boot()` de um Service Provider Laravel.

### `BootService::reducer('wireConfig', ...)`

Transforma o array completo de boot:

```php
use Luminix\Frontend\Services\BootService;

BootService::reducer('wireConfig', function (array $boot) {
    return [
        ...$boot,
        'app' => [
            ...$boot['app'],
            'version'       => config('app.version'),
            'support_email' => 'suporte@empresa.com',
        ],
    ];
});
```

### `ManifestService::reducer('modelManifest', ...)`

Aplicado a **todos os modelos** durante a montagem do manifest:

```php
use Luminix\Frontend\Services\ManifestService;

ManifestService::reducer('modelManifest', function (array $data, string $modelClass) {
    return [
        ...$data,
        'searchable' => in_array(\Laravel\Scout\Searchable::class, class_uses_recursive($modelClass)),
    ];
});
```

### `ManifestService::reducer('model{Nome}Manifest', ...)`

Aplicado apenas ao modelo específico:

```php
// Apenas para App\Models\User
ManifestService::reducer('modelUserManifest', function (array $data) {
    return [...$data, 'avatar_url' => true];
});

// Apenas para App\Models\BlogPost
ManifestService::reducer('modelBlogPostManifest', function (array $data) {
    return [...$data, 'has_variants' => true];
});
```

### Prioridade de execução

Menor valor = executa primeiro. Padrão: `10`.

```php
// Executa antes dos demais
ManifestService::reducer('modelManifest', fn($data) => [...$data, 'step' => 'a'], 5);

// Executa depois (padrão)
ManifestService::reducer('modelManifest', fn($data) => [...$data, 'step' => 'b']);
```

### Removendo um reducer

`reducer()` retorna uma função de cancelamento:

```php
$unsubscribe = BootService::reducer('wireConfig', fn($boot) => $boot);

// Depois:
$unsubscribe();
```

---

## Evento `Init`

Disparado **após** todos os reducers do `BootService` serem aplicados. Use para logging ou auditoria — não para transformar dados (prefira reducers para isso):

```php
use Luminix\Frontend\Events\Init;
use Illuminate\Support\Facades\Event;

Event::listen(Init::class, function (Init $event) {
    Log::info('Boot gerado', ['url' => $event->boot['app']['url']]);
});
```

---

## Exibindo erros de validação

A diretiva aceita nomes de campos de formulário separados por `|`. Quando presentes na sessão do Laravel, os erros de validação correspondentes são incluídos no boot data:

```blade
@luminixEmbed('email|password')
```

---

## Configuração {#configuração}

```php
// config/luminix/frontend.php
return [
    'boot' => [
        'includes_manifest' => true,  // inclui manifest inline no boot data
    ],
    'models' => [
        'public'  => [],
        'exclude' => [],
    ],
    'routes' => [
        'public'  => [],
        'exclude' => [],
    ],
];
```

---

## Próximos passos

- Entenda como o backend gera os modelos expostos em [luminix/backend](backend.md).
- Veja como os dados são usados pelos componentes em [@luminix/mui-cms](js-mui-cms.md).
- Customize os dados de boot em [Personalização](personalizacao.md).
- Volte ao [índice](index.md).
