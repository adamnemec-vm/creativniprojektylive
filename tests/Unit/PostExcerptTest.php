<?php

namespace Tests\Unit;

use App\Models\Post;
use PHPUnit\Framework\TestCase;

class PostExcerptTest extends TestCase
{
    public function test_excerpt_strips_tags_and_decodes_entities(): void
    {
        $post = new Post(['content' => '<p>Kočka&nbsp;&amp;&nbsp;pes</p><p>Druhý&nbsp;<strong>odstavec</strong>.</p>']);

        $this->assertSame('Kočka & pes Druhý odstavec.', $post->excerpt());
    }

    public function test_excerpt_keeps_escaped_markup_as_plain_text(): void
    {
        // TinyMCE ukládá text "<script>" jako entity; excerpt z něj udělá prostý text,
        // který šablona escapuje přes {{ }}.
        $post = new Post(['content' => '<p>&lt;script&gt;alert(1)&lt;/script&gt;</p>']);

        $this->assertSame('<script>alert(1)</script>', $post->excerpt());
    }

    public function test_excerpt_is_limited(): void
    {
        $post = new Post(['content' => '<p>'.str_repeat('a', 300).'</p>']);

        $this->assertSame(str_repeat('a', 50).'...', $post->excerpt(50));
    }
}
