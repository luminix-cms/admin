# luminix/admin

Painel de administração CMS para aplicações Laravel, construído com React e Material-UI. Parte do ecossistema [Luminix](https://github.com/luminix-cms).

## Instalação rápida

```bash
# 1. Instalar o pacote
composer require luminix/admin

# 2. Publicar os arquivos de UI
php artisan luminix:admin-ui

# 3. Instalar dependências e compilar
npm install && npm run dev
```

Após isso, defina o Gate de autorização no seu `AppServiceProvider`:

```php
Gate::define('view-admin-panel', fn ($user) => $user->is_admin);
```

O painel estará disponível em `/admin`.

## Documentação completa

Consulte a [documentação completa](docs/index.md) para guias detalhados sobre instalação, configuração, autorização, personalização e integração com os pacotes do ecossistema Luminix.

## Licença

MIT — veja o arquivo [LICENSE](LICENSE) para detalhes.
