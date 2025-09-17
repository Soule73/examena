<div class="markdown-content prose prose-sm max-w-none">
    {!! $renderedContent !!}
</div>

<style>
    .markdown-content {
        line-height: 1.6;
    }

    .markdown-content h1 {
        font-size: 1.5em;
        font-weight: bold;
        margin: 1em 0 0.5em 0;
        color: #1f2937;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 0.3em;
    }

    .markdown-content h2 {
        font-size: 1.3em;
        font-weight: bold;
        margin: 0.8em 0 0.4em 0;
        color: #374151;
    }

    .markdown-content h3 {
        font-size: 1.1em;
        font-weight: bold;
        margin: 0.6em 0 0.3em 0;
        color: #4b5563;
    }

    .markdown-content p {
        margin: 0.8em 0;
    }

    .markdown-content ul,
    .markdown-content ol {
        margin: 0.8em 0;
        padding-left: 1.5em;
    }

    .markdown-content li {
        margin: 0.3em 0;
    }

    .markdown-content blockquote {
        border-left: 4px solid #d1d5db;
        margin: 1em 0;
        padding-left: 1em;
        color: #6b7280;
        font-style: italic;
    }

    .markdown-content code {
        background-color: #f3f4f6;
        padding: 0.2em 0.4em;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
    }

    .markdown-content pre {
        background-color: #f3f4f6;
        padding: 1em;
        border-radius: 5px;
        overflow-x: auto;
        margin: 1em 0;
    }

    .markdown-content pre code {
        background-color: transparent;
        padding: 0;
    }

    .markdown-content table {
        border-collapse: collapse;
        width: 100%;
        margin: 1em 0;
    }

    .markdown-content table th,
    .markdown-content table td {
        border: 1px solid #d1d5db;
        padding: 0.5em;
        text-align: left;
    }

    .markdown-content table th {
        background-color: #f9fafb;
        font-weight: bold;
    }

    .markdown-content strong {
        font-weight: bold;
    }

    .markdown-content em {
        font-style: italic;
    }

    .markdown-content hr {
        border: none;
        border-top: 1px solid #d1d5db;
        margin: 2em 0;
    }

    /* Styles pour les équations LaTeX (si MathJax/KaTeX est utilisé) */
    .markdown-content .math {
        font-family: 'Times New Roman', serif;
    }

    .markdown-content .math-display {
        text-align: center;
        margin: 1em 0;
    }

    .markdown-content .math-inline {
        display: inline;
    }
</style>
