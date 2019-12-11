#Alerj Mobile

API para dados variados da ALERJ 

## Características da aplicação
- [Git](https://git-scm.com/docs/user-manual.html)
- [PHP 7.2 ou superior](http://php.net/)
- [Composer](https://getcomposer.org/)
- [Redis](https://redis.io/topics/quickstart)
- [PostgreSQL](https://www.postgresql.org/)

### Instalação 

#### Guia genérico de uma aplicação desenvolvida em PHP pelo Projetos Especiais

- Clonar o repositório (branch: staging [homologação] or production [produção])
- Configurar servidor web para apontar para a **`<pasta-aonde-o-site-foi-instalado>`/public**
- Instalar certificado SSL (precisamos que a página seja acessível **via https apenas**)
- Criar o banco do dados
- Entrar na `<pasta-aonde-o-site-foi-instalado>`
- Copiar o arquivo `.env.example` para `.env`
- Editar o arquivo `.env` e configurar todos dados do sistema
- Ainda no arquivo `.env`, alterar a variável `APP_ENV` para o ambiente correto (testing, staging, production)
- Ainda no arquivo `.env`, configurar banco de dados
- Executar o comando `composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev` para instalar todas as dependências da aplicação
- Banco de dados
    - Caso **não** haja backup: executar o comando `php artisan migrate` para **criar** a estrutura do banco de dados
    - Caso haja backup: restaurar o banco e executar o comando `php artisan migrate` para **atualizar** a estrutura do banco de dados
- Linkar a pasta storage
```
php artisan storage:link
```

### Atualizando a aplicação

- Entrar na `<pasta-aonde-o-site-foi-instalado>`
- Baixar as atualizações de código fonte usando Git (git pull ou git fetch + git merge, isso depende de como operador prefere trabalhar com Git)
- Executar os comandos em sequência:
```
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force
php artisan docigp:sync:roles
```
- Reiniciar o Horizon
