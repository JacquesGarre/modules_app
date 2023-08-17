<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Exception;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('json_decode', [$this, 'jsonDecode']),
        ];
    }

    public function jsonDecode(string $json): array
    {
        try {
            $array = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            return [$json];
        }
        return $array;
    }
}