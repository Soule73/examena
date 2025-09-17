<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;

class MarkdownRenderer extends Component
{
    public string $content;
    public string $renderedContent;

    /**
     * Create a new component instance.
     */
    public function __construct(string $content)
    {
        $this->content = $content;
        $this->renderedContent = $this->renderMarkdown($content);
    }

    /**
     * Convertir le markdown en HTML
     */
    private function renderMarkdown(string $markdown): string
    {
        // Configuration de l'environnement CommonMark
        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 20,
        ];

        // Créer l'environnement avec les extensions
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new TaskListExtension());

        // Créer le convertisseur
        $converter = new CommonMarkConverter($config, $environment);

        // Préserver les équations LaTeX avant la conversion
        $markdown = $this->preserveLatexEquations($markdown);

        // Convertir le markdown en HTML
        $html = $converter->convert($markdown)->getContent();

        // Restaurer les équations LaTeX après la conversion
        $html = $this->restoreLatexEquations($html);

        return $html;
    }

    /**
     * Préserver les équations LaTeX avant la conversion Markdown
     */
    private function preserveLatexEquations(string $content): string
    {
        // Préserver les équations block ($$...$$)
        $content = preg_replace_callback('/\$\$(.*?)\$\$/s', function($matches) {
            return '%%LATEX_BLOCK_' . base64_encode($matches[1]) . '%%';
        }, $content);

        // Préserver les équations inline ($...$)
        $content = preg_replace_callback('/\$([^$\n]+?)\$/', function($matches) {
            return '%%LATEX_INLINE_' . base64_encode($matches[1]) . '%%';
        }, $content);

        return $content;
    }

    /**
     * Restaurer les équations LaTeX après la conversion Markdown
     */
    private function restoreLatexEquations(string $content): string
    {
        // Restaurer les équations block
        $content = preg_replace_callback('/%%LATEX_BLOCK_(.*?)%%/', function($matches) {
            $equation = base64_decode($matches[1]);
            return '$$' . $equation . '$$';
        }, $content);

        // Restaurer les équations inline
        $content = preg_replace_callback('/%%LATEX_INLINE_(.*?)%%/', function($matches) {
            $equation = base64_decode($matches[1]);
            return '$' . $equation . '$';
        }, $content);

        return $content;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.markdown-renderer');
    }
}
