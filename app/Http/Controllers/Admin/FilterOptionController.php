<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FilterOption;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FilterOptionController extends Controller
{
    public function index()
    {
        $counts = [];
        foreach (FilterOption::FILTERS as $config) {
            $column = $config['column'];
            $counts = array_replace($counts, Post::whereNotNull($column)
                ->groupBy($column)
                ->selectRaw("{$column} as option_id, count(*) as total")
                ->pluck('total', 'option_id')
                ->all());
        }

        return view('admin.filters.index', [
            'groups' => FilterOption::grouped(),
            'counts' => $counts,
        ]);
    }

    public function create(Request $request)
    {
        $filter = $request->query('filter');
        abort_unless(array_key_exists($filter, FilterOption::FILTERS), 404);

        return view('admin.filters.form', ['option' => new FilterOption(['filter' => $filter])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['sort_order'] ??= (int) FilterOption::where('filter', $data['filter'])->max('sort_order') + 1;

        FilterOption::create($data);

        return redirect()->route('admin.filters.index')->with('success', 'Hodnota filtru byla přidána.');
    }

    public function edit(FilterOption $filter)
    {
        return view('admin.filters.form', ['option' => $filter]);
    }

    public function update(Request $request, FilterOption $filter)
    {
        $filter->update($this->validated($request, $filter));

        return redirect()->route('admin.filters.index')->with('success', 'Hodnota filtru byla upravena.');
    }

    public function destroy(FilterOption $filter)
    {
        if ($filter->postsCount() > 0) {
            return back()->withErrors(["Hodnotu „{$filter->name}“ nelze smazat, používají ji příspěvky. Nejdřív jim nastavte jinou."]);
        }

        $filter->delete();

        return redirect()->route('admin.filters.index')->with('success', 'Hodnota filtru byla smazána.');
    }

    private function validated(Request $request, ?FilterOption $option = null): array
    {
        $filter = $option?->filter ?? $request->input('filter');

        $data = $request->validate([
            'filter' => $option ? ['prohibited'] : ['required', Rule::in(array_keys(FilterOption::FILTERS))],
            'name' => [
                'required', 'string', 'max:50',
                Rule::unique('filter_options')->where('filter', $filter)->ignore($option?->id),
                ...($filter === 'school_year' ? ['regex:'.FilterOption::SCHOOL_YEAR_PATTERN] : []),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'name.regex' => 'Školní rok zadejte ve tvaru 2028/29.',
        ], [
            'name' => 'název',
            'sort_order' => 'pořadí',
        ]);

        return $option ? array_merge($data, ['sort_order' => $data['sort_order'] ?? $option->sort_order]) : $data;
    }
}
