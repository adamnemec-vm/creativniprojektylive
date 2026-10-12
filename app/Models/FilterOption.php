<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilterOption extends Model
{
    /** @use HasFactory<\Database\Factories\FilterOptionFactory> */
    use HasFactory;

    /** Skupina => [popisek, parametr v URL, sloupec v posts]. */
    public const FILTERS = [
        'work_type' => ['label' => 'Typ práce', 'param' => 'typ', 'column' => 'work_type_id'],
        'school_year' => ['label' => 'Školní rok', 'param' => 'rok', 'column' => 'school_year_id'],
        'grade' => ['label' => 'Ročník', 'param' => 'rocnik', 'column' => 'grade_id'],
    ];

    /** Formát školního roku; řadí se podle něj automaticky od nejnovějšího. */
    public const SCHOOL_YEAR_PATTERN = '/^\d{4}\/\d{2}$/';

    protected $fillable = ['filter', 'name', 'sort_order'];

    /**
     * Hodnoty po skupinách v pořadí pro nabídky. Školní roky od nejnovějšího,
     * ostatní podle pořadí nastaveného adminem.
     *
     * @return array<string, Collection<int, FilterOption>>
     */
    public static function grouped(): array
    {
        $all = static::orderBy('sort_order')->orderBy('name')->get()->groupBy('filter');

        return collect(self::FILTERS)->mapWithKeys(function ($config, $filter) use ($all) {
            $options = $all->get($filter, new Collection);

            return [$filter => $filter === 'school_year' ? $options->sortByDesc('name')->values() : $options];
        })->all();
    }

    /**
     * ID z parametru URL: "1,2" i pole ["1", "2"] (formulář bez JavaScriptu).
     *
     * @return list<int>
     */
    public static function parseIds(mixed $value): array
    {
        $values = is_array($value) ? $value : explode(',', is_scalar($value) ? (string) $value : '');

        return collect($values)
            ->filter(fn ($id) => is_scalar($id) && ctype_digit((string) $id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(50)
            ->values()
            ->all();
    }

    /** Je ve veřejném výpisu aktivní hledání nebo některý filtr? */
    public static function isFiltering(array $input): bool
    {
        $search = $input['q'] ?? null;

        return (is_string($search) && trim($search) !== '')
            || collect(self::FILTERS)->contains(fn ($config) => self::parseIds($input[$config['param']] ?? null) !== []);
    }

    public function postsCount(): int
    {
        return Post::where(self::FILTERS[$this->filter]['column'], $this->id)->count();
    }

    public function label(): string
    {
        return self::FILTERS[$this->filter]['label'];
    }
}
