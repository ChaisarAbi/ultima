<?php
// Convert logo.webp to logo.png for dompdf compatibility
$webpPath = __DIR__ . '/../public/logo.webp';
$pngPath = __DIR__ . '/../public/logo.png';

if (file_exists($webpPath)) {
    $image = imagecreatefromwebp($webpPath);
    if ($image) {
        imagepng($image, $pngPath, 9);
        imagedestroy($image);
        echo "Converted logo.webp to logo.png successfully!\n";
    } else {
        echo "Error: Could not read webp file\n";
    }
} else {
    echo "logo.webp not found at: " . $webpPath . "\n";
}