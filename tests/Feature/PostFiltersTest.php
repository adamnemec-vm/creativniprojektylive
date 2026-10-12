<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FilterOption;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostFiltersTest extends TestCase
{
    use RefreshDatabase;

    private function option(string $filter, string $name): FilterOption
    {
        return FilterOption::where('filter', $filter)->where('name', $name)->sole();
    }

    public function test_migration_creates_default_values(): void
    {
        $this->assertEquals(['Klauzurní práce', 'Maturitní práce', 'Práce z hodin'], FilterOption::grouped()['work_type']->pluck('name')->all());
        $this->assertEquals(['2027/28', '2026/27', '2025/26'], FilterOption::grouped()['school_year']->pluck('name')->all());
        $this->assertEquals(['1. ročník', '2. ročník', '3. ročník', '4. ročník'], FilterOption::grouped()['grade']->pluck('name')->all());
    }

    public function test_home_filters_by_each_parameter_and_combination(): void
    {
        $maturita = $this->option('work_type', 'Maturitní práce');
        $klauzura = $this->option('work_type', 'Klauzurní práce');
        $rok25 = $this->option('school_year', '2025/26');
        $rok26 = $this->option('school_year', '2026/27');
        $ctvrty = $this->option('grade', '4. ročník');
        $prvni = $this->option('grade', '1. ročník');

        Post::factory()->withFilters($maturita->id, $rok25->id, $ctvrty->id)->create(['title' => 'Maturita 25 čtvrťák']);
        Post::factory()->withFilters($klauzura->id, $rok25->id, $prvni->id)->create(['title' => 'Klauzura 25 prvák']);
        Post::factory()->withFilters($maturita->id, $rok26->id, $ctvrty->id)->create(['title' => 'Maturita 26 čtvrťák']);
        Post::factory()->create(['title' => 'Starý bez zařazení']);

        $this->get('/?typ='.$maturita->id)->assertOk()
            ->assertSee('Maturita 25 čtvrťák')->assertSee('Maturita 26 čtvrťák')
            ->assertDontSee('Klauzura 25 prvák')->assertDontSee('Starý bez zařazení');

        $this->get('/?rok='.$rok25->id)->assertOk()
            ->assertSee('Maturita 25 čtvrťák')->assertSee('Klauzura 25 prvák')
            ->assertDontSee('Maturita 26 čtvrťák');

        $this->get('/?rocnik='.$prvni->id)->assertOk()
            ->assertSee('Klauzura 25 prvák')->assertDontSee('Maturita 25 čtvrťák');

        $this->get("/?typ={$maturita->id}&rok={$rok26->id}&rocnik={$ctvrty->id}")->assertOk()
            ->assertSee('Maturita 26 čtvrťák')->assertDontSee('Maturita 25 čtvrťák');

        $this->get('/')->assertSee('Starý bez zařazení');
    }

    public function test_filters_combine_with_search_and_show_empty_message(): void
    {
        $klauzura = $this->option('work_type', 'Klauzurní práce');
        Post::factory()->withFilters($klauzura->id)->create(['title' => 'Plakát na festival']);
        Post::factory()->withFilters($klauzura->id)->create(['title' => 'Logo pro kavárnu']);

        $this->get("/?typ={$klauzura->id}&q=plakát")->assertOk()
            ->assertSee('Plakát na festival')->assertDontSee('Logo pro kavárnu');

        $this->get('/?typ='.$this->option('work_type', 'Práce z hodin')->id)->assertOk()
            ->assertSee('Zvoleným filtrům neodpovídá žádný příspěvek.');
    }

    public function test_active_filters_are_shown_as_removable_chips(): void
    {
        $maturita = $this->option('work_type', 'Maturitní práce');
        $rocnik = $this->option('grade', '4. ročník');
        Post::factory()->withFilters($maturita->id, null, $rocnik->id)->create();

        $this->get("/?typ={$maturita->id}&rocnik={$rocnik->id}&q=x")->assertOk()
            ->assertSee('title="Odebrat filtr Typ práce: Maturitní práce"', false)
            // Odebrání jednoho filtru zachová ostatní i hledání.
            ->assertSee('?rocnik='.$rocnik->id.'&amp;q=x#prispevky', false)
            ->assertSee('Zrušit vše');

        $this->get('/')->assertOk()->assertDontSee('Odebrat filtr', false);
    }

    public function test_invalid_filter_values_are_ignored(): void
    {
        Post::factory()->create(['title' => 'Viditelný příspěvek']);

        $this->get('/?typ=abc&rok[a][b]=1&rocnik=,,')->assertOk()->assertSee('Viditelný příspěvek');
    }

    public function test_multiple_values_within_group_are_combined_with_or(): void
    {
        $klauzura = $this->option('work_type', 'Klauzurní práce');
        $maturita = $this->option('work_type', 'Maturitní práce');
        $hodiny = $this->option('work_type', 'Práce z hodin');
        $treti = $this->option('grade', '3. ročník');
        $ctvrty = $this->option('grade', '4. ročník');
        $prvni = $this->option('grade', '1. ročník');

        Post::factory()->withFilters($klauzura->id, null, $treti->id)->create(['title' => 'Klauzura třeťák']);
        Post::factory()->withFilters($maturita->id, null, $ctvrty->id)->create(['title' => 'Maturita čtvrťák']);
        Post::factory()->withFilters($hodiny->id, null, $ctvrty->id)->create(['title' => 'Hodina čtvrťák']);
        Post::factory()->withFilters($klauzura->id, null, $prvni->id)->create(['title' => 'Klauzura prvák']);

        $this->get("/?typ={$klauzura->id},{$maturita->id}")->assertOk()
            ->assertSee('Klauzura třeťák')->assertSee('Maturita čtvrťák')->assertSee('Klauzura prvák')
            ->assertDontSee('Hodina čtvrťák');

        // Typ (klauzura NEBO maturita) A ZÁROVEŇ ročník (3. NEBO 4.)
        $this->get("/?typ={$klauzura->id},{$maturita->id}&rocnik={$treti->id},{$ctvrty->id}")->assertOk()
            ->assertSee('Klauzura třeťák')->assertSee('Maturita čtvrťák')
            ->assertDontSee('Hodina čtvrťák')->assertDontSee('Klauzura prvák')
            ->assertSee('<span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-brand text-black text-xs">4</span>', false);

        // Formulář bez JavaScriptu posílá pole typ[]=…
        $this->get("/?typ[]={$klauzura->id}&typ[]={$maturita->id}")->assertOk()
            ->assertSee('Klauzura třeťák')->assertSee('Maturita čtvrťák')->assertDontSee('Hodina čtvrťák');
    }

    public function test_removing_one_value_keeps_the_others(): void
    {
        $klauzura = $this->option('work_type', 'Klauzurní práce');
        $maturita = $this->option('work_type', 'Maturitní práce');
        Post::factory()->withFilters($klauzura->id)->create();
        Post::factory()->withFilters($maturita->id)->create();

        $this->get("/?typ={$klauzura->id},{$maturita->id}")->assertOk()
            ->assertSee('?typ='.$maturita->id.'#prispevky', false)
            ->assertSee('?typ='.$klauzura->id.'#prispevky', false);
    }

    public function test_dialog_hides_values_without_posts_and_shows_counts(): void
    {
        $maturita = $this->option('work_type', 'Maturitní práce');
        Post::factory()->count(2)->withFilters($maturita->id, $this->option('school_year', '2025/26')->id)->create();
        Post::factory()->draft()->withFilters($this->option('work_type', 'Práce z hodin')->id)->create();

        $response = $this->get('/')->assertOk();
        $response->assertSee('value="'.$maturita->id.'"', false);
        $response->assertDontSee('value="'.$this->option('work_type', 'Práce z hodin')->id.'"', false);
        $response->assertDontSee('value="'.$this->option('school_year', '2027/28')->id.'"', false);
        $this->assertMatchesRegularExpression('/Maturitní práce\s*<span class="text-xs opacity-60">2<\/span>/', $response->getContent());
    }

    public function test_school_years_are_newest_first_and_older_ones_are_collapsed(): void
    {
        foreach (['2028/29', '2029/30', '2030/31'] as $name) {
            FilterOption::create(['filter' => 'school_year', 'name' => $name, 'sort_order' => 0]);
        }
        foreach (FilterOption::where('filter', 'school_year')->get() as $year) {
            Post::factory()->withFilters(null, $year->id)->create();
        }

        $this->assertEquals(
            ['2030/31', '2029/30', '2028/29', '2027/28', '2026/27', '2025/26'],
            FilterOption::grouped()['school_year']->pluck('name')->all()
        );

        $html = $this->get('/')->assertOk()->assertSee('Starší školní roky (2)')->getContent();
        $older = $this->option('school_year', '2025/26');
        $this->assertMatchesRegularExpression('/<label\s+x-show="more"\s+x-cloak\s*>\s*<input type="checkbox" name="rok\[\]" value="'.$older->id.'"/', $html);

        // Vybraný starší rok se zobrazí rozbalený.
        $html = $this->get('/?rok='.$older->id)->assertOk()->getContent();
        $this->assertStringContainsString("x-data=\"{ more: true }\"", $html);
    }

    public function test_school_year_must_have_expected_format(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.filters.store'), ['filter' => 'school_year', 'name' => '2028'])
            ->assertSessionHasErrors(['name' => 'Školní rok zadejte ve tvaru 2028/29.']);

        $this->actingAs($admin)->post(route('admin.filters.store'), ['filter' => 'grade', 'name' => '5. ročník'])
            ->assertSessionHasNoErrors();
    }

    public function test_category_page_filters(): void
    {
        $category = Category::factory()->create();
        $hodiny = $this->option('work_type', 'Práce z hodin');
        Post::factory()->for($category)->withFilters($hodiny->id)->create(['title' => 'Cvičení z hodiny']);
        Post::factory()->for($category)->withFilters($this->option('work_type', 'Maturitní práce')->id)->create(['title' => 'Maturitní projekt']);

        $this->get(route('categories.show', ['category' => $category, 'typ' => $hodiny->id]))->assertOk()
            ->assertSee('Cvičení z hodiny')->assertDontSee('Maturitní projekt');
    }

    public function test_post_detail_shows_filter_badges_linking_to_filtered_listing(): void
    {
        $maturita = $this->option('work_type', 'Maturitní práce');
        $rok = $this->option('school_year', '2025/26');
        $rocnik = $this->option('grade', '4. ročník');
        $post = Post::factory()->withFilters($maturita->id, $rok->id, $rocnik->id)->create();

        $this->get(route('posts.show', $post))->assertOk()
            ->assertSeeInOrder(['Maturitní práce', '2025/26', '4. ročník'])
            ->assertSee(route('home', ['typ' => $maturita->id]).'#prispevky', false);
    }

    public function test_post_requires_all_filters_from_the_right_group(): void
    {
        $editor = User::factory()->create();
        $payload = [
            'title' => 'Projekt', 'content' => '<p>x</p>', 'status' => 'published',
            'category_id' => Category::factory()->create()->id,
        ];

        $this->actingAs($editor)->post(route('admin.posts.store'), $payload)
            ->assertSessionHasErrors(['work_type_id', 'school_year_id', 'grade_id']);

        // Hodnota ze špatné skupiny (školní rok místo typu práce) neprojde.
        $this->actingAs($editor)->post(route('admin.posts.store'), $payload + [
            'work_type_id' => $this->option('school_year', '2025/26')->id,
            'school_year_id' => $this->option('school_year', '2025/26')->id,
            'grade_id' => $this->option('grade', '1. ročník')->id,
        ])->assertSessionHasErrors('work_type_id')->assertSessionDoesntHaveErrors(['school_year_id', 'grade_id']);

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_admin_list_warns_about_posts_without_filters(): void
    {
        $admin = User::factory()->admin()->create();
        Post::factory()->create(['title' => 'Bez zařazení A']);
        Post::factory()->withFilters()->create(['title' => 'Kompletní příspěvek']);

        $this->actingAs($admin)->get(route('admin.posts.index'))->assertOk()
            ->assertSee('příspěvek nemá', false);

        $this->actingAs($admin)->get(route('admin.posts.index', ['status' => 'incomplete']))->assertOk()
            ->assertSee('Bez zařazení A')->assertDontSee('Kompletní příspěvek');
    }

    public function test_admin_can_manage_filter_values(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.filters.index'))->assertOk()->assertSee('Klauzurní práce');
        $this->actingAs($admin)->get(route('admin.filters.create', ['filter' => 'school_year']))->assertOk();

        $this->actingAs($admin)->post(route('admin.filters.store'), ['filter' => 'school_year', 'name' => '2028/29'])
            ->assertRedirect(route('admin.filters.index'));
        $new = $this->option('school_year', '2028/29');
        $this->assertSame(4, $new->sort_order, 'Bez zadaného pořadí se zařadí na konec.');

        $this->actingAs($admin)->post(route('admin.filters.store'), ['filter' => 'school_year', 'name' => '2028/29'])
            ->assertSessionHasErrors('name');

        $this->actingAs($admin)->get(route('admin.filters.edit', $new))->assertOk();
        $this->actingAs($admin)->put(route('admin.filters.update', $new), ['name' => '2029/30', 'sort_order' => 10])
            ->assertSessionHasNoErrors();
        $this->assertSame('2029/30', $new->fresh()->name);
        $this->assertSame('school_year', $new->fresh()->filter);

        $this->actingAs($admin)->delete(route('admin.filters.destroy', $new))->assertRedirect();
        $this->assertModelMissing($new);
    }

    public function test_used_filter_value_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $maturita = $this->option('work_type', 'Maturitní práce');
        $post = Post::factory()->withFilters($maturita->id)->create();

        $this->actingAs($admin)->delete(route('admin.filters.destroy', $maturita))->assertSessionHasErrors();

        $this->assertModelExists($maturita);
        $this->assertSame($maturita->id, $post->fresh()->work_type_id);
    }

    public function test_editor_cannot_manage_filters(): void
    {
        $editor = User::factory()->create();

        $this->actingAs($editor)->get(route('admin.filters.index'))->assertForbidden();
        $this->actingAs($editor)->post(route('admin.filters.store'), ['filter' => 'grade', 'name' => '5. ročník'])->assertForbidden();
    }
}
