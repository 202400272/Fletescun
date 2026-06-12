<?php
$path = 'c:/xampp/htdocs/Fletescun/fletescun/app/Services/AnexoFotograficoService.php';
$content = file_get_contents($path);

// Replace css classes for images
$content = str_replace(
    '.foto-img {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 3px;
        }',
    '.foto-img {
            max-width: 200px;
            max-height: 200px;
            width: auto;
            height: auto;
            border-radius: 3px;
        }',
    $content
);

file_put_contents($path, $content);
echo "CSS REPLACED";
?>
