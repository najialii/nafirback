<?php

namespace App\Helpers;

class ImageHelper
{
    public static function generateImageUrl(string $category, array $options = []): string
    {
        if ($category === 'person') {
            $name = $options['name'] ?? 'User';
            $encodedName = urlencode($name);
            return "https://ui-avatars.com/api/?name={$encodedName}&background=random&size=300";
        }

        $width = isset($options['width']) && is_numeric($options['width']) ? $options['width'] : 300;
        $height = isset($options['height']) && is_numeric($options['height']) ? $options['height'] : 300;
        $seed = bin2hex(random_bytes(4));
        return "https://picsum.photos/seed/{$seed}/{$width}/{$height}";
    }
}
