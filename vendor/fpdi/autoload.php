<?php
// Autoloader mínimo para o FPDI (sem Composer). Requer a classe FPDF já carregada.
spl_autoload_register(static function (string $class): void {
    $prefix = 'setasign\\Fpdi\\';
    if (strncmp($class, $prefix, strlen($prefix)) === 0) {
        $file = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});
