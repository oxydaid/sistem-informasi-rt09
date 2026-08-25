<?php

namespace App\Services;

use App\Models\Media;

class TiptapContentService
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Convert Tiptap document JSON array to clean HTML.
     */
    public function renderToHtml(array|string|null $doc): string
    {
        if (empty($doc)) {
            return '';
        }

        if (is_string($doc)) {
            $decoded = json_decode($doc, true);
            if (! is_array($decoded)) {
                return nl2br(e($doc));
            }
            $doc = $decoded;
        }

        if (! isset($doc['content']) || ! is_array($doc['content'])) {
            return '';
        }

        return $this->renderNodes($doc['content']);
    }

    protected function renderNodes(array $nodes): string
    {
        $html = '';
        foreach ($nodes as $node) {
            $html .= $this->renderNode($node);
        }

        return $html;
    }

    protected function renderNode(array $node): string
    {
        $type = $node['type'] ?? 'text';
        $attrs = $node['attrs'] ?? [];
        $content = $node['content'] ?? [];
        $text = $node['text'] ?? '';
        $marks = $node['marks'] ?? [];

        if ($type === 'text') {
            $escapedText = e($text);

            return $this->applyMarks($escapedText, $marks);
        }

        $innerHtml = ! empty($content) ? $this->renderNodes($content) : '';

        // Check text alignment style
        $alignStyle = '';
        if (isset($attrs['textAlign'])) {
            $alignStyle = ' style="text-align: '.e($attrs['textAlign']).';"';
        }

        return match ($type) {
            'paragraph' => "<p{$alignStyle}>".($innerHtml ?: '<br>')."</p>\n",
            'heading' => $this->renderHeading($attrs, $innerHtml),
            'bulletList' => "<ul>\n{$innerHtml}</ul>\n",
            'orderedList' => "<ol>\n{$innerHtml}</ol>\n",
            'listItem' => "<li>{$innerHtml}</li>\n",
            'taskList' => "<ul class=\"task-list\">\n{$innerHtml}</ul>\n",
            'taskItem' => '<li class="task-item" data-checked="'.($attrs['checked'] ?? false ? 'true' : 'false')."\">{$innerHtml}</li>\n",
            'blockquote' => "<blockquote>\n{$innerHtml}</blockquote>\n",
            'codeBlock' => '<pre><code>'.e($innerHtml ?: $text)."</code></pre>\n",
            'horizontalRule' => "<hr>\n",
            'image' => $this->renderImage($attrs),
            'table' => "<div class=\"table-wrapper\"><table class=\"table-tiptap\">\n{$innerHtml}</table></div>\n",
            'tableRow' => "<tr>\n{$innerHtml}</tr>\n",
            'tableHeader' => $this->renderTableCell($attrs, $innerHtml, 'th'),
            'tableCell' => $this->renderTableCell($attrs, $innerHtml, 'td'),
            'attachment' => $this->renderAttachment($attrs),
            default => $innerHtml,
        };
    }

    protected function renderHeading(array $attrs, string $innerHtml): string
    {
        $level = $attrs['level'] ?? 2;
        $level = max(1, min(6, (int) $level));
        $align = isset($attrs['textAlign']) ? ' style="text-align: '.e($attrs['textAlign']).';"' : '';

        return "<h{$level}{$align}>{$innerHtml}</h{$level}>\n";
    }

    protected function renderImage(array $attrs): string
    {
        $src = e($attrs['src'] ?? '');
        $alt = e($attrs['alt'] ?? '');
        $title = e($attrs['title'] ?? '');
        $align = $attrs['align'] ?? 'center';

        $titleAttr = $title ? " title=\"{$title}\"" : '';

        return "<figure class=\"tiptap-image align-{$align}\"><img src=\"{$src}\" alt=\"{$alt}\"{$titleAttr} loading=\"lazy\" /></figure>\n";
    }

    protected function renderTableCell(array $attrs, string $innerHtml, string $tag): string
    {
        $colSpan = isset($attrs['colspan']) && (int) $attrs['colspan'] > 1 ? ' colspan="'.(int) $attrs['colspan'].'"' : '';
        $rowSpan = isset($attrs['rowspan']) && (int) $attrs['rowspan'] > 1 ? ' rowspan="'.(int) $attrs['rowspan'].'"' : '';

        return "<{$tag}{$colSpan}{$rowSpan}>{$innerHtml}</{$tag}>\n";
    }

    protected function renderAttachment(array $attrs): string
    {
        $url = e($attrs['url'] ?? '#');
        $name = e($attrs['name'] ?? 'Dokumen');
        $size = e($attrs['size'] ?? '');

        return "<div class=\"tiptap-attachment-box\"><a href=\"{$url}\" target=\"_blank\" class=\"attachment-link\"><span class=\"attachment-icon\">📎</span><span class=\"attachment-name\">{$name}</span>".($size ? "<span class=\"attachment-size\">({$size})</span>" : '')."</a></div>\n";
    }

    protected function applyMarks(string $text, array $marks): string
    {
        foreach ($marks as $mark) {
            $type = $mark['type'] ?? '';
            $attrs = $mark['attrs'] ?? [];

            $text = match ($type) {
                'bold' => "<strong>{$text}</strong>",
                'italic' => "<em>{$text}</em>",
                'strike' => "<del>{$text}</del>",
                'underline' => "<u>{$text}</u>",
                'code' => "<code>{$text}</code>",
                'link' => '<a href="'.e($attrs['href'] ?? '#').'"'.(isset($attrs['target']) ? ' target="'.e($attrs['target']).'"' : ' target="_blank" rel="noopener noreferrer"').">{$text}</a>",
                default => $text,
            };
        }

        return $text;
    }

    /**
     * Extract all media IDs referenced in Tiptap JSON doc.
     *
     * @return array<int>
     */
    public function extractMediaIds(array|string|null $doc): array
    {
        if (empty($doc)) {
            return [];
        }

        if (is_string($doc)) {
            $doc = json_decode($doc, true);
        }

        if (! is_array($doc)) {
            return [];
        }

        $mediaIds = [];
        $this->collectMediaIds($doc, $mediaIds);

        return array_unique(array_filter($mediaIds));
    }

    protected function collectMediaIds(array $node, array &$mediaIds): void
    {
        if (isset($node['attrs']['mediaId'])) {
            $mediaIds[] = (int) $node['attrs']['mediaId'];
        }

        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as $child) {
                if (is_array($child)) {
                    $this->collectMediaIds($child, $mediaIds);
                }
            }
        }
    }

    /**
     * Clean up orphaned media when content is updated or deleted.
     */
    public function cleanupOrphanedMedia(array|string|null $oldDoc, array|string|null $newDoc = null): void
    {
        $oldMediaIds = $this->extractMediaIds($oldDoc);
        $newMediaIds = $newDoc !== null ? $this->extractMediaIds($newDoc) : [];

        $orphanedIds = array_diff($oldMediaIds, $newMediaIds);

        if (! empty($orphanedIds)) {
            $orphanMedia = Media::whereIn('id', $orphanedIds)->get();
            foreach ($orphanMedia as $media) {
                // Ensure it's not referenced by other contents or activities
                $isUsedInOtherAttachments = $media->contentAttachments()->exists();
                $isUsedInOtherActivities = $media->activityMedia()->exists();

                if (! $isUsedInOtherAttachments && ! $isUsedInOtherActivities) {
                    $this->fileUploadService->deleteMedia($media);
                }
            }
        }
    }

    /**
     * Convert simple Markdown text to basic Tiptap JSON doc.
     */
    public function markdownToTiptapJson(string $markdown): array
    {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $markdown));
        $content = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            if (preg_match('/^(#{1,6})\s+(.*)$/', $trimmed, $matches)) {
                $level = strlen($matches[1]);
                $content[] = [
                    'type' => 'heading',
                    'attrs' => ['level' => $level],
                    'content' => [['type' => 'text', 'text' => $matches[2]]],
                ];
            } elseif (preg_match('/^[-*]\s+(.*)$/', $trimmed, $matches)) {
                $content[] = [
                    'type' => 'bulletList',
                    'content' => [
                        [
                            'type' => 'listItem',
                            'content' => [
                                [
                                    'type' => 'paragraph',
                                    'content' => [['type' => 'text', 'text' => $matches[1]]],
                                ],
                            ],
                        ],
                    ],
                ];
            } elseif (preg_match('/^>\s+(.*)$/', $trimmed, $matches)) {
                $content[] = [
                    'type' => 'blockquote',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [['type' => 'text', 'text' => $matches[1]]],
                        ],
                    ],
                ];
            } elseif ($trimmed === '---' || $trimmed === '***') {
                $content[] = ['type' => 'horizontalRule'];
            } else {
                $content[] = [
                    'type' => 'paragraph',
                    'content' => [['type' => 'text', 'text' => $line]],
                ];
            }
        }

        return [
            'type' => 'doc',
            'content' => $content,
        ];
    }
}
