<?php

namespace App\Core;

class View
{
    private static string $viewsPath;

    public static function init(): void
    {
        self::$viewsPath = dirname(__DIR__, 2) . '/resources/views';
    }

    public static function render(string $template, array $data = [], ?string $layout = 'public-layout'): string
    {
        if (!isset(self::$viewsPath)) {
            self::init();
        }

        $templateFile = self::$viewsPath . '/' . str_replace('.', '/', $template) . '.php';
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("View template not found: {$templateFile}");
        }

        // Render inner template
        extract($data, EXTR_SKIP);
        ob_start();
        require $templateFile;
        $content = ob_get_clean();

        // If no layout is requested, return content directly
        if ($layout === null || $layout === false) {
            return $content;
        }

        // Render layout
        $layoutFile = self::$viewsPath . '/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout not found: {$layoutFile}");
        }

        ob_start();
        require $layoutFile;
        return ob_get_clean();
    }

    public static function component(string $name, array $data = []): string
    {
        if (!isset(self::$viewsPath)) {
            self::init();
        }

        $file = self::$viewsPath . '/components/' . str_replace('.', '/', $name) . '.php';
        if (!file_exists($file)) {
            throw new \RuntimeException("Component not found: {$file}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return ob_get_clean();
    }
}
