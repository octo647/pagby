<?php
file_put_contents(__DIR__ . '/../storage/logs/teste_php.log', date('c') . " - Teste PHP OK\n", FILE_APPEND);
echo "<h1>PHP rodando!</h1>";
