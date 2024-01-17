<?php

namespace App\Services;

class RouteService 
{
    /**
     * @param string $title
     * 
     * @return string
     */
    public static function parseTitleSlug(string $title): string
    {
        $lcTitle = strtolower($title);
        $filteredTitle = str_replace(['+','<','>','@','(',')','~','%','*','"',':','.'], '', $lcTitle);
        $slug = str_replace(' ', '-', $filteredTitle);
        return $slug;
    }
}