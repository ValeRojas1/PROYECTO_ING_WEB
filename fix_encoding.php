<?php
$dir = dirname(__FILE__) . '/frontend';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$replacements = [
    'Ã¡' => 'á',
    'Ã©' => 'é',
    'Ã­' => 'í',
    'Ã³' => 'ó',
    'Ãº' => 'ú',
    'Ã±' => 'ñ',
    'Ã‘' => 'Ñ',
    'Â¿' => '¿',
    'Â¡' => '¡',
    'Ã' => 'Á',
    'Ã‰' => 'É',
    'Ã' => 'Í',
    'Ã“' => 'Ó',
    'Ãš' => 'Ú'
];

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
        $content = file_get_contents($file);
        $modified = false;
        
        foreach ($replacements as $broken => $fixed) {
            if (strpos($content, $broken) !== false) {
                $content = str_replace($broken, $fixed, $content);
                $modified = true;
            }
        }
        
        if ($modified) {
            file_put_contents($file, $content);
            echo "Fixed encoding in: " . $file->getFilename() . "\n";
        }
    }
}
echo "Done.\n";
?>
