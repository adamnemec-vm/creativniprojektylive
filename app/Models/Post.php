<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'work_type_id',
        'school_year_id',
        'grade_id',
        'user_id',
        'thumbnail_path',
        'slug',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function workType(): BelongsTo
    {
        return $this->belongsTo(FilterOption::class, 'work_type_id');
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(FilterOption::class, 'school_year_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(FilterOption::class, 'grade_id');
    }

    /** Vyplněné hodnoty filtrů v pořadí Typ práce, Školní rok, Ročník: [label, param, option]. */
    public function filterBadges(): array
    {
        $badges = [];
        foreach (FilterOption::FILTERS as $filter => $config) {
            $option = $this->{Str::camel($filter)};
            if ($option) {
                $badges[] = ['label' => $config['label'], 'param' => $config['param'], 'option' => $option];
            }
        }

        return $badges;
    }

    public function scopeMissingFilters(Builder $query): void
    {
        $query->where(fn ($q) => $q
            ->whereNull('posts.work_type_id')
            ->orWhereNull('posts.school_year_id')
            ->orWhereNull('posts.grade_id'));
    }

    public function hasAllFilters(): bool
    {
        return $this->work_type_id && $this->school_year_id && $this->grade_id;
    }

    /** Fulltext a filtry z veřejného výpisu (?q=, ?typ=, ?rok=, ?rocnik=). */
    public function scopeListing(Builder $query, array $input): void
    {
        $search = is_string($input['q'] ?? null) ? trim($input['q']) : '';

        $query->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
            ->where('title', 'like', "%{$search}%")
            ->orWhere('content', 'like', "%{$search}%")));

        // V rámci skupiny "nebo" (klauzurní i maturitní), mezi skupinami "a zároveň".
        foreach (FilterOption::FILTERS as $config) {
            $ids = FilterOption::parseIds($input[$config['param']] ?? null);
            if ($ids !== []) {
                $query->whereIn($config['column'], $ids);
            }
        }
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->lte(now());
    }

    public function isScheduled(): bool
    {
        return $this->published_at !== null && $this->published_at->isFuture();
    }

    public function statusLabel(): string
    {
        return match (true) {
            $this->isPublished() => 'Publikováno',
            $this->isScheduled() => 'Naplánováno',
            default => 'Koncept',
        };
    }

    /** Prostý text bez HTML; výstup se musí v šabloně escapovat přes {{ }}. */
    public function excerpt(int $limit = 100): string
    {
        // Blokové tagy nahradíme mezerou, aby se neslepila slova z různých odstavců.
        $text = preg_replace('#</?(p|div|br|li|ul|ol|h[1-6]|blockquote|pre|table|tr|td|th|figure|figcaption)\b[^>]*>#i', ' ', $this->content);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $text));

        return Str::limit($text, $limit);
    }

    public function coverImagePath(): ?string
    {
        return $this->thumbnail_path ?? $this->images->first()?->image_path;
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'prispevek';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
