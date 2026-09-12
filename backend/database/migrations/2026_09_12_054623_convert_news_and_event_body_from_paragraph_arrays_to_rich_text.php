<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('news_posts')->select('id', 'body')->get() as $post) {
            DB::table('news_posts')->where('id', $post->id)->update([
                'body' => $this->paragraphsToHtml($post->body),
            ]);
        }

        DB::statement('ALTER TABLE events MODIFY body LONGTEXT NULL');

        foreach (DB::table('events')->select('id', 'body')->get() as $event) {
            DB::table('events')->where('id', $event->id)->update([
                'body' => $this->paragraphsToHtml($event->body),
            ]);
        }
    }

    public function down(): void
    {
        foreach (DB::table('news_posts')->select('id', 'body')->get() as $post) {
            DB::table('news_posts')->where('id', $post->id)->update([
                'body' => $this->htmlToParagraphsJson($post->body),
            ]);
        }

        foreach (DB::table('events')->select('id', 'body')->get() as $event) {
            DB::table('events')->where('id', $event->id)->update([
                'body' => $this->htmlToParagraphsJson($event->body),
            ]);
        }

        DB::statement('ALTER TABLE events MODIFY body JSON NULL');
    }

    /**
     * The repeater used to store `body` as a JSON array of plain-text
     * paragraphs. Wrap each surviving paragraph in its own <p> so the new
     * RichEditor field (and the frontend's dangerouslySetInnerHTML render)
     * see the same content, just as real HTML instead of an array.
     */
    private function paragraphsToHtml(?string $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return $raw;
        }

        $paragraphs = json_decode($raw, true);

        if (! is_array($paragraphs)) {
            return $raw;
        }

        return collect($paragraphs)
            ->filter(fn ($paragraph) => filled($paragraph))
            ->map(fn ($paragraph) => '<p>'.e($paragraph).'</p>')
            ->implode('');
    }

    private function htmlToParagraphsJson(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        preg_match_all('/<p>(.*?)<\/p>/s', $html, $matches);
        $paragraphs = $matches[1] !== [] ? $matches[1] : [strip_tags($html)];

        return json_encode(array_map(
            fn ($paragraph) => html_entity_decode(strip_tags($paragraph)),
            $paragraphs,
        ));
    }
};
