![image](https://raw.github.com/jcemer/calopsita/master/images/calopsita.png)

Um framework PHP (não MVC) que já deu vida a muitos [projetos](http://jcemer.com/portfolio.html).


Gerenciador de conteúdo
---------------

O framework possui uma estrutura de classes e facilitadores para prover um gerenciador simples de conteúdo.

O gerenciador por padrão pode ser acessado no endereço `/manager` com usuário `admin` e senha `123456`. Este usuário com identificação 1 é especial e portanto não é listado no módulo de usuários, sua senha deve ser alterada através do caminho `/manager/user/edit/1`.


Configuração
---------------

### URL e hospedagem

A hospedagem deve ter a reescrita de URL autorizada através do `mod_rewrite` do Apache (ou similiar). Caso contrário, você deve desabilitar a flag `URL_REWRITE` em **config**.

Em **config** as constantes `PATH_INVALID` e `PATH_BASE_HREF` devem refletir o caminho e url respectivamente.

O arquivo `htaccess` deve conter o caminho para o arquivo **config** incluíndo `PATH_INVALID`.

### Banco de dados

O acesso é provido através da extensão de *PDO*. Você deve assegurar que sua hospedagem possua a interface para seu *SGBD* configurada.

As informações para conexão estão detalhadas no arquivo **config**:

- `DB_CONN` - (true) framework deve conectar ao banco de dados por padrão?
- `DB_DRIVER` - (mysql) qual o *SGBD*
- `DB_HOST` - (localhost) endereço do *SGBD*
- `DB_USER` - (root) usuário do *SGBD*
- `DB_PWD` - (root) senha de acesso do usuário no *SGBD*
- `DB_DATA` - (calopsita) *database* padrão

O SQL com os módulos principais está no arquivo `migration.sql`.

### Upload

A pasta `upload`, bem como suas subpastas, devem possuir permissão de escrita para o usuário que executa o interpretador `PHP`.