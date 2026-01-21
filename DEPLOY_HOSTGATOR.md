# Instruções de Deploy na HostGator

## Após descompactar o projeto na HostGator:

1. **Criar symlink para storage:**
   ```bash
   php artisan storage:link
   ```

2. **Instalar dependências (se não fizer automaticamente):**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Gerar APP_KEY (se necessário):**
   ```bash
   php artisan key:generate
   ```

4. **Rodar migrações:**
   ```bash
   php artisan migrate --force
   ```

## Notas importantes:

- A pasta `public/storage` não é enviada no zip (está em `.gitignore`)
- O comando `php artisan storage:link` cria o symlink automaticamente
- Novos uploads funcionarão corretamente após o symlink ser criado
- Se precisar resetar, delete `public/storage` e rode novamente o comando

