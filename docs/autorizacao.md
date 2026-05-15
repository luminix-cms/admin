# Autorização

O `luminix/admin` usa o sistema nativo de autorização do Laravel (Gates) para controlar acesso ao painel e às operações individuais de cada modelo.

---

## Acesso ao painel

O acesso ao painel é controlado pela *ability* `view-admin-panel`. Esta verificação está incluída no middleware padrão:

```php
// config/luminix/admin.php
'middleware' => ['web', 'auth', 'can:view-admin-panel'],
```

Registre o Gate no `AppServiceProvider` da sua aplicação:

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('view-admin-panel', function ($user) {
        return $user->is_admin;
    });
}
```

Qualquer lógica válida pode ser usada:

```php
// Por atributo booleano
Gate::define('view-admin-panel', fn ($user) => $user->is_admin);

// Por role/papel (ex.: Spatie Permission)
Gate::define('view-admin-panel', fn ($user) => $user->hasRole('admin'));

// Por e-mail específico
Gate::define('view-admin-panel', fn ($user) => str_ends_with($user->email, '@suaempresa.com'));
```

---

## Autorização por operação de modelo

O `luminix/backend` verifica Gates para cada operação CRUD. A convenção de nomes é:

| Operação | Ability |
|----------|---------|
| Listar | `read-{modelo}` |
| Criar | `create-{modelo}` |
| Visualizar | `read-{modelo}` |
| Atualizar | `update-{modelo}` |
| Excluir | `delete-{modelo}` |
| Restaurar | `restore-{modelo}` |

O nome do modelo é derivado do nome da classe em *snake-case*. Para `App\Models\BlogPost`, as abilities seriam `read-blog-post`, `create-blog-post`, etc.

### Exemplo completo

```php
// app/Providers/AppServiceProvider.php
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    // Acesso ao painel
    Gate::define('view-admin-panel', fn ($user) => $user->is_admin);

    // Leitura pública (visitantes podem ver)
    Gate::define('read-post', fn (?User $user) => true);

    // Criação apenas para autenticados
    Gate::define('create-post', fn (?User $user) => (bool) $user);

    // Edição e exclusão apenas pelo dono
    Gate::define('update-post', fn (User $user, Post $post) => $user->id === $post->user_id);
    Gate::define('delete-post', fn (User $user, Post $post) => $user->id === $post->user_id);
}
```

> **Filosofia *deny first*:** se um Gate não estiver definido para uma operação, o acesso é negado por padrão.

---

## Controle de acesso em nível de linha

Além dos Gates por operação, o `luminix/backend` chama o scope `scopeAllowed` em cada consulta. Use-o para filtrar quais registros o usuário pode visualizar ou modificar:

```php
// app/Models/Post.php
use Illuminate\Database\Eloquent\Builder;

public function scopeAllowed(Builder $query, string $permission): void
{
    // Em listagem e leitura, qualquer usuário vê apenas posts publicados
    if ($permission === 'read') {
        $query->where('published', true);
    }

    // Em atualização e exclusão, apenas o próprio dono
    if (in_array($permission, ['update', 'delete'])) {
        $query->where('user_id', auth()->id());
    }
}
```

O parâmetro `$permission` recebe o nome da operação: `read`, `create`, `update`, `delete` ou `restore`.

---

## Desabilitando a verificação por Gates

Para desabilitar a verificação por Gates em toda a API (não recomendado em produção), edite a configuração do `luminix/backend`:

```php
// config/luminix/backend.php
'security' => [
    'gates_enabled' => false,
],
```

Para desabilitar apenas em um modelo específico, use o atributo `#[WithoutGates]` no modelo:

```php
use Luminix\Backend\Attributes\WithoutGates;

#[WithoutGates]
class PublicPost extends Model
{
    use LuminixModel;
}
```

---

## Personalização do middleware

Para remover ou adicionar middlewares ao painel, edite `config/luminix/admin.php`:

```php
'middleware' => [
    'web',
    'auth',
    'verified',           // exige e-mail verificado
    'can:view-admin-panel',
    'throttle:60,1',      // rate limiting
],
```

---

## Próximos passos

- Veja como expor modelos com segurança em [luminix/backend](backend.md#segurança).
- Configure middlewares da API em [Configuração](configuracao.md).
- Personalize o painel em [Personalização](personalizacao.md).
