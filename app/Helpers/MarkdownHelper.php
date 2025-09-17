<?php

if (!function_exists('markdown')) {
    /**
     * Convertir le markdown en HTML
     * 
     * @param string $content
     * @return string
     */
    function markdown(string $content): string
    {
        return app(\App\View\Components\MarkdownRenderer::class, ['content' => $content])->renderedContent;
    }
}

if (!function_exists('markdown_safe')) {
    /**
     * Convertir le markdown en HTML et l'afficher de manière sécurisée
     * 
     * @param string $content
     * @return \Illuminate\Support\HtmlString
     */
    function markdown_safe(string $content): \Illuminate\Support\HtmlString
    {
        return new \Illuminate\Support\HtmlString(markdown($content));
    }
}