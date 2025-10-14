<?php

namespace Modules\Inventory\Livewire\Recipes;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Entities\Recipe;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\ItemCategory;
use App\Models\MenuItem;
use App\Models\MenuItemVariation;
use App\Models\ModifierOption;

class RecipesList extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';
    public $category = '';
    public $sortBy = 'menu_items.item_name';
    public $perPage = 10;
    public $page = 1;
    public $showAddRecipe = false;
    public $isEditing = false;
    public $confirmDeleteRecipe = false;
    public $recipeToDelete = null;

    protected $queryString = [
        'page' => ['except' => 1],
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'sortBy' => ['except' => 'menu_items.item_name'],
    ];

    protected $listeners = [
        'recipeUpdated' => '$refresh',
        'closeAddRecipeModal' => 'closeModal'
    ];

    // Add watchers for real-time filtering
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function render()
    {
        // Set MySQL to non-strict mode for this query
        DB::statement("SET SESSION sql_mode=''");

        // Get all menu items with recipes
        $menuItemsWithRecipes = MenuItem::with(['variations', 'category'])
            ->where('branch_id', branch()->id)
            ->where('is_available', 1)
            ->whereHas('recipes')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('item_name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('recipes.inventoryItem', function($subquery) {
                          $subquery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->category, function ($query) {
                $query->whereHas('category', function($q) {
                    $q->where('category_name', 'like', '%' . $this->category . '%');
                });
            })
            ->get();

        // Get all modifier options with recipes
        $modifierOptionsWithRecipes = ModifierOption::with('modifierGroup')
            ->where('is_available', 1)
            ->whereHas('recipes')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('recipes.inventoryItem', function($subquery) {
                          $subquery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->get();

        // Process menu items to create recipe entries
        $recipeEntries = collect();
        
        foreach ($menuItemsWithRecipes as $menuItem) {
            if ($menuItem->variations->count() > 0) {
                // Menu item has variations - create separate entries for each variation
                foreach ($menuItem->variations as $variation) {
                    $variationRecipes = Recipe::where('menu_item_id', $menuItem->id)
                        ->where('menu_item_variation_id', $variation->id)
                        ->with(['inventoryItem', 'unit'])
                        ->get();

                    if ($variationRecipes->count() > 0) {
                        $recipeEntries->push([
                            'menu_item' => [
                                'id' => $menuItem->id,
                                'name' => $menuItem->item_name,
                                'image' => $menuItem->item_photo_url,
                                'preparation_time' => $menuItem->preparation_time,
                                'category' => $menuItem->category->category_name ?? '',
                                'variation' => $variation->variation,
                                'variation_id' => $variation->id
                            ],
                            'ingredients' => $variationRecipes->map(function ($ingredient) {
                                return [
                                    'name' => $ingredient->inventoryItem->name,
                                    'quantity' => $ingredient->quantity,
                                    'unit' => $ingredient->unit->symbol
                                ];
                            }),
                            'ingredients_cost' => $variationRecipes->sum(function ($ingredient) {
                                return $ingredient->inventoryItem->unit_purchase_price * $ingredient->quantity;
                            })
                        ]);
                    }
                }
            } else {
                // Menu item has no variations - create entry for base menu item
                $baseRecipes = Recipe::where('menu_item_id', $menuItem->id)
                    ->whereNull('menu_item_variation_id')
                    ->with(['inventoryItem', 'unit'])
                    ->get();

                if ($baseRecipes->count() > 0) {
                    $recipeEntries->push([
                        'menu_item' => [
                            'id' => $menuItem->id,
                            'name' => $menuItem->item_name,
                            'image' => $menuItem->item_photo_url,
                            'preparation_time' => $menuItem->preparation_time,
                            'category' => $menuItem->category->category_name ?? '',
                            'variation' => null,
                            'variation_id' => null
                        ],
                        'ingredients' => $baseRecipes->map(function ($ingredient) {
                            return [
                                'name' => $ingredient->inventoryItem->name,
                                'quantity' => $ingredient->quantity,
                                'unit' => $ingredient->unit->symbol
                            ];
                        }),
                        'ingredients_cost' => $baseRecipes->sum(function ($ingredient) {
                            return $ingredient->inventoryItem->unit_purchase_price * $ingredient->quantity;
                        })
                    ]);
                }
            }
        }

        // Process modifier options to create recipe entries
        foreach ($modifierOptionsWithRecipes as $modifierOption) {
            $modifierRecipes = Recipe::where('modifier_option_id', $modifierOption->id)
                ->with(['inventoryItem', 'unit'])
                ->get();

            if ($modifierRecipes->count() > 0) {
                $recipeEntries->push([
                    'menu_item' => [
                        'id' => $modifierOption->id,
                        'name' => $modifierOption->name,
                        'image' => null,
                        'preparation_time' => 0,
                        'category' => $modifierOption->modifierGroup->name ?? 'Modifier',
                        'variation' => null,
                        'variation_id' => null,
                        'is_modifier_option' => true,
                        'modifier_option_id' => $modifierOption->id
                    ],
                    'ingredients' => $modifierRecipes->map(function ($ingredient) {
                        return [
                            'name' => $ingredient->inventoryItem->name,
                            'quantity' => $ingredient->quantity,
                            'unit' => $ingredient->unit->symbol
                        ];
                    }),
                    'ingredients_cost' => $modifierRecipes->sum(function ($ingredient) {
                        return $ingredient->inventoryItem->unit_purchase_price * $ingredient->quantity;
                    })
                ]);
            }
        }

        // Sort the entries
        $recipeEntries = $recipeEntries->sortBy(function ($entry) {
            switch ($this->sortBy) {
                case 'name':
                    return $entry['menu_item']['name'] . ($entry['menu_item']['variation'] ? ' - ' . $entry['menu_item']['variation'] : '');
                case 'category':
                    return $entry['menu_item']['category'] . ' - ' . $entry['menu_item']['name'];
                case 'prep_time':
                    return $entry['menu_item']['preparation_time'] . ' - ' . $entry['menu_item']['name'];
                default:
                    return $entry['menu_item']['name'] . ($entry['menu_item']['variation'] ? ' - ' . $entry['menu_item']['variation'] : '');
            }
        });

        // Get total count for pagination
        $total = $recipeEntries->count();

        // Get paginated results
        $recipes = $recipeEntries->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage);

        // Create paginator instance
        $recipes = new LengthAwarePaginator(
            $recipes,
            $total,
            $this->perPage,
            $this->page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        // Get categories for filter dropdown
        $categories = ItemCategory::get();

        // Get statistics
        $totalRecipes = $total;

        $mainCourseCount = $recipeEntries->filter(function ($entry) {
            return str_contains(strtolower($entry['menu_item']['category']), 'main course');
        })->count();

        $avgPrepTime = $recipeEntries->avg('menu_item.preparation_time');

        // Reset SQL mode back to default after query execution
        DB::statement("SET SESSION sql_mode=(SELECT @@global.sql_mode)");
        
        return view('inventory::livewire.recipes.recipes-list', [
            'recipes' => $recipes,
            'categories' => $categories,
            'totalRecipes' => $totalRecipes,
            'mainCoursesCount' => $mainCourseCount,
            'avgPrepTime' => round($avgPrepTime ?? 0)
        ]);
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'sortBy']);
        $this->resetPage();
    }

    public function addRecipe()
    {
        $this->isEditing = false;
        $this->showAddRecipe = true;
        $this->dispatch('showRecipeForm');
    }

    public function editRecipe($recipeId, $variationId = null, $modifierOptionId = null)
    {
        $this->isEditing = true;
        $this->showAddRecipe = true;
        
        // Pass the appropriate parameters based on what we're editing
        if ($modifierOptionId) {
            $this->dispatch('editRecipe', $recipeId, $variationId, $modifierOptionId);
        } else {
            $this->dispatch('editRecipe', $recipeId, $variationId);
        }
    }

    public function closeModal()
    {
        $this->showAddRecipe = false;
        $this->isEditing = false;
    }

    public function showDeleteRecipe($recipeId, $variationId = null, $modifierOptionId = null)
    {
        $this->recipeToDelete = [
            'menu_item_id' => $recipeId, 
            'variation_id' => $variationId,
            'modifier_option_id' => $modifierOptionId
        ];
        $this->confirmDeleteRecipe = true;
    }

    public function deleteRecipe()
    {
        if ($this->recipeToDelete['modifier_option_id']) {
            // Delete recipes for modifier option
            Recipe::where('modifier_option_id', $this->recipeToDelete['modifier_option_id'])
                ->delete();
        } elseif ($this->recipeToDelete['variation_id']) {
            // Delete recipes for specific variation
            Recipe::where('menu_item_id', $this->recipeToDelete['menu_item_id'])
                ->where('menu_item_variation_id', $this->recipeToDelete['variation_id'])
                ->delete();
        } else {
            // Delete all recipes for the menu item (including variations)
            Recipe::where('menu_item_id', $this->recipeToDelete['menu_item_id'])->delete();
        }

        $this->alert('success', __('inventory::modules.recipe.recipe_deleted'));
        $this->confirmDeleteRecipe = false;
        $this->recipeToDelete = null;
    }
}
