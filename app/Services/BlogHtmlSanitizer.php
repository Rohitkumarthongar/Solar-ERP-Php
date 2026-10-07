<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

class BlogHtmlSanitizer
{
    private const TAGS = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'blockquote', 'a', 'pre', 'code'];

    public function clean(string $html): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8"><div id="blog-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        $root = $document->getElementById('blog-root');
        if (!$root) return '';
        $this->filter($root);
        $output = '';
        foreach ($root->childNodes as $node) $output .= $document->saveHTML($node);
        return $output;
    }

    private function filter(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if (!$node instanceof DOMElement) {
                if ($node->nodeType !== XML_TEXT_NODE) $parent->removeChild($node);
                continue;
            }
            $tag = strtolower($node->tagName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'form', 'template'], true)) {
                $parent->removeChild($node);
                continue;
            }
            $this->filter($node);
            if (!in_array($tag, self::TAGS, true)) {
                while ($node->firstChild) $parent->insertBefore($node->firstChild, $node);
                $parent->removeChild($node);
                continue;
            }
            $href = $node->getAttribute('href');
            while ($node->attributes->length) $node->removeAttributeNode($node->attributes->item(0));
            if ($tag === 'a' && preg_match('~^(https?://|mailto:|/[^/]|\#)[^\x00-\x1f]*$~i', trim($href))) {
                $node->setAttribute('href', trim($href));
                $node->setAttribute('rel', 'nofollow noopener noreferrer');
            }
        }
    }
}
