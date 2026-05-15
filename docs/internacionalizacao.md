# Internacionalização

O `luminix/admin` inclui suporte a múltiplos idiomas. As traduções da interface são carregadas automaticamente conforme o locale ativo da aplicação Laravel.

---

## Idiomas incluídos

| Locale | Idioma |
|--------|--------|
| `en` | Inglês (padrão de fallback) |
| `pt-BR` | Português Brasileiro |

---

## Como funciona

1. A configuração `app.locale` do Laravel define o idioma ativo
2. O `AdminServiceProvider` carrega o arquivo de tradução correspondente (`lang/{locale}.json`)
3. As strings traduzidas são injetadas no frontend via `BootService::reducer('wireConfig', ...)`
4. O React SPA recebe as traduções no boot data e as usa via [i18next](https://www.i18next.com/)

---

## Configurando os locales suportados

Edite a opção `locales` em `config/luminix/admin.php`:

```php
'locales' => ['en', 'pt-BR', 'es'],
```

Somente locales listados aqui são carregados. Se o locale ativo não estiver na lista, o painel usará o fallback do i18next (geralmente `en`).

---

## Adicionando um novo idioma

### 1. Criar o arquivo de tradução

Crie um arquivo JSON em `lang/{locale}.json` na raiz da sua aplicação (não dentro do pacote):

```bash
# Exemplo: Espanhol
touch lang/es.json
```

Copie a estrutura do arquivo `pt-BR.json` do pacote como referência:

```json
{
    "Dashboard": "Panel",
    "Create": "Crear",
    "Edit": "Editar",
    "Delete": "Eliminar",
    "Restore": "Restaurar",
    "Cancel": "Cancelar",
    "Confirm": "Confirmar",
    "Save": "Guardar",
    "Search": "Buscar",
    "Filter": "Filtrar",
    "Actions": "Acciones",
    "Loading": "Cargando",
    "No results found": "No se encontraron resultados",
    "Are you sure you want to delete :count item(s)?": "¿Está seguro de que desea eliminar :count elemento(s)?",
    "Item deleted successfully": "Elemento eliminado correctamente",
    "Item created successfully": "Elemento creado correctamente",
    "Item updated successfully": "Elemento actualizado correctamente"
}
```

### 2. Adicionar o locale à configuração

```php
// config/luminix/admin.php
'locales' => ['en', 'pt-BR', 'es'],
```

### 3. Configurar o locale no Laravel

```php
// config/app.php
'locale' => 'es',
```

Ou dinamicamente, via middleware:

```php
app()->setLocale($user->locale);
```

---

## Strings com placeholders

Algumas strings usam placeholders dinâmicos com `:variavel`:

```json
{
    "Create :model": "Criar :model",
    "Edit :model": "Editar :model",
    "Delete :count item(s)?": "Excluir :count item(ns)?",
    ":label is required": ":label é obrigatório"
}
```

Esses placeholders são preenchidos automaticamente pelo frontend com o nome do modelo ou outros valores contextuais.

---

## Sobrescrevendo traduções

Para sobrescrever strings específicas de um idioma já existente, publique o arquivo de língua do pacote:

```bash
php artisan vendor:publish --provider="Luminix\Admin\AdminServiceProvider" --tag="luminix-lang"
```

Isso copia o arquivo para `lang/vendor/admin/{locale}.json`, onde você pode editá-lo livremente. Alterações neste arquivo têm prioridade sobre as traduções do pacote.

---

## Locales no frontend (i18next)

O painel usa [i18next](https://www.i18next.com/) e [react-i18next](https://react.i18next.com/) para gerenciar as traduções no frontend. As traduções são recebidas via boot data e configuradas automaticamente pelo `@luminix/mui-cms`.

Você pode adicionar namespaces de tradução extras via `AppServiceProvider` JavaScript:

```js
// resources/js/Providers/AppServiceProvider.js
import { ServiceProvider } from '@luminix/support';
import i18n from 'i18next';

export default class AppServiceProvider extends ServiceProvider
{
    boot() {
        i18n.addResourceBundle('pt-BR', 'myapp', {
            'Welcome back, :name': 'Bem-vindo de volta, :name',
        });
    }
}
```

---

## Próximos passos

- Personalize o painel visualmente em [Personalização](personalizacao.md).
- Entenda como os dados de configuração chegam ao frontend em [luminix/frontend](frontend.md).
- Volte ao [índice](index.md) para ver todos os tópicos.
