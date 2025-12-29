## Comandos Fundamentais (Ciclo Básico)

    git init: Inicializa um novo repositório Git. 

- git clone <url>: Cria uma cópia local de um repositório remoto. 
- git status: Mostra o estado atual dos arquivos (modificados, prontos para commit, etc.). 
- git add <arquivo> / git add .: Adiciona arquivos (ou todos) à área de staging (preparo). 
- git commit -m "mensagem": Salva as alterações preparadas no histórico local. 
- git push: Envia os commits locais para o repositório remoto. 
- git pull: Baixa e mescla as alterações do repositório remoto para o local. 

## Comandos de Ramificação (Branches)

    git branch: Lista, cria ou apaga branches. 

- git checkout <nome-branch>: Muda para outra branch. 
- git merge <nome-branch>: Mescla outra branch na branch atual. 
- git branch -d <nome-branch>: Deleta uma branch (após merge). 

## Comandos de Histórico e Inspeção

    git log: Exibe o histórico de commits.
    git log --oneline: Versão compacta do log.
    git diff: Mos

- git show <hash>: Mostra detalhes de um commit específico. 

## Comandos de Reversão e Limpeza

    git reset <arquivo>: Remove um arquivo da staging area.
    git revert <hash>: Cria um novo commit que desfaz as mudanças de um commit anterior.
    git clean: Remove arquivos não monitorados.
    git stash: Guarda temporariamente alterações não comitadas. 

## Configuração

- git config: Define configurações (usuário, e-mail, etc.). 


## Suponha que está no ramo melhorias

# 1. Verificar o status das mudanças
git status

# 2. Adicionar todas as mudanças
git add .

# 3. Fazer commit com uma mensagem descritiva
git commit -m "Atualiza exibição de planos com layout estilo Alura"

# 4. Enviar para o GitHub (branch melhorias)
git push origin melhorias

# Mudar para a branch principal
git checkout main

# Fazer merge da branch melhorias
git merge melhorias

# Enviar para o GitHub
git push origin main

# Voltar para a branch melhorias
git checkout melhorias