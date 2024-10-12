## Documentação

### Requerido

- [Docker](https://www.docker.com/get-started).
- [Docker-composer](https://docs.docker.com/compose/install/)


### Executando o sistema

Para rodar a aplicacão você deve executar o comando
```shel
docker-composer up -d
```

Uma vez executado o comando e não ocorra nenhum erro, você terá 3 containers rodando em seu ambiente.

Rode o comando abaixo e certifique-se que os 3 containers estão rodando
```shel
docker ps
```

O resultado deve ser algo parecido com:

```shel
0369fe6b0cc7   ... so_play_phpfpm

1fd556149625   ... so_play_webserver

7881e26c4c0a   ... so_play_mysql
```

### Instalando as dependências

O sistema é desenvolvido utilizando o [Laravel](https://laravel.com) e trabalha com o gerenciador de dependências do php, o [composer](https://getcomposer.org/). 

Como usamos o docker como ambiente de desenvolvimento, todas os comandos devem ser executado dentro do container para maior confiabilidade dos pacotes e controle de versão, garantindo que aplicação funcione normalmente com qualquer desenvolvedor.

Para instalar as dependências precisamos rodar o comando composer install dentro do container **so_play_phpfpm**, para tal devemos executar o seguinte comando

```shel
docker exec -it so_play_phpfpm composer install
```

Esse comando, permite que você acesse o container e rode o composer install, utilizando todas as configurações

### Configuração do .env

O .env é onde você controla as variaveis de ambiente da aplicação. Copie o arquivo .env.example e altere de acordo com a sua necessidade local.

Se, você adicionar novas linhas no seu .env, não se esqueça de sicronizar com o .env.example, para que os demais membros do time aplique nos seus ambientes locais.


### Estrategia de versionamento e branch

#### Branch

Para o controle de branch, utilizaremos o [gitflow](https://blog.betrybe.com/git/git-flow/). Recomendamos o uso de um utilitário de linha de comando que permite seguir o fluxo do gitflow com maior facilidade

Link: [https://danielkummer.github.io/git-flow-cheatsheet/index.pt_BR.html](https://danielkummer.github.io/git-flow-cheatsheet/index.pt_BR.html)

### Versionamento
Para o controle de versionamento devemos seguir o [Versionamento Semantico 2.0](https://semver.org/lang/pt-BR/)


### Guia de estilo

O php conta com um poderoso guia de estilo unificado, chamado [PSR](https://www.php-fig.org/psr/), que permite aos nossos desenvolvedores seguir uma forma de escrita de código padronizada. Devemos ajustar nossa base de códigos para seguir todas elas

Existe alguns plugins para todas as IDEs/Editores que já apontam erros comuns de PSR que já auxiliar a nós desenvolvedores.

- Vscode [php cs fixer](https://marketplace.visualstudio.com/items?itemName=junstyle.php-cs-fixer) 

**Algumas ferramentas**

- EditorConfig

