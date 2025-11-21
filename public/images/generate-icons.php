#!/usr/bin/env php
<?php
/**
 * Script para gerar ícones PWA em múltiplos tamanhos
 * 
 * Uso: php generate-icons.php
 * 
 * Requer: PHP com extensão GD ativada
 */

$sourceImage = __DIR__ . '/logo_sige_app.png';
$outputDir = __DIR__ . '/icons/';

// Tamanhos necessários para PWA
$sizes = [72, 96, 128, 144, 152, 192, 384, 512];

if (!file_exists($sourceImage)) {
    die("❌ Erro: logo_sige_app.png não encontrada em " . __DIR__ . "\n");
}

if (!extension_loaded('gd')) {
    die("❌ Erro: Extensão GD não está ativada no PHP.\n   Ative no php.ini: extension=gd\n");
}

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

echo "🎨 Gerando ícones PWA a partir de logo_sige_app.png...\n\n";

$source = imagecreatefrompng($sourceImage);
if (!$source) {
    die("❌ Erro ao carregar imagem fonte\n");
}

// Ativa antialiasing para qualidade
imageantialias($source, true);

foreach ($sizes as $size) {
    $output = imagecreatetruecolor($size, $size);
    
    // Preserva transparência
    imagealphablending($output, false);
    imagesavealpha($output, true);
    $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
    imagefill($output, 0, 0, $transparent);
    imagealphablending($output, true);
    
    // Redimensiona com alta qualidade
    imagecopyresampled(
        $output, 
        $source, 
        0, 0, 0, 0, 
        $size, $size, 
        imagesx($source), 
        imagesy($source)
    );
    
    $filename = $outputDir . "icon-{$size}x{$size}.png";
    imagepng($output, $filename, 9); // Máxima compressão
    imagedestroy($output);
    
    $filesize = round(filesize($filename) / 1024, 1);
    echo "✅ Criado: icon-{$size}x{$size}.png ({$filesize} KB)\n";
}

imagedestroy($source);

$total = count($sizes);
echo "\n🎉 Concluído! {$total} ícones gerados em /images/icons/\n";
echo "📱 Seu PWA está pronto para ser instalado!\n";
