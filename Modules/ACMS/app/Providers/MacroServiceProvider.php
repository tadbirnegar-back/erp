<?php

namespace Modules\ACMS\app\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        Collection::macro('toHierarchy', function ($parentId = null) {
            // Group items by their parent_id
            $grouped = [];
            foreach ($this->all() as $item) {
                $grouped[$item['parent_id']][] = $item;
            }

            // Recursive function to build the tree
            $buildTree = function ($parentId) use (&$buildTree, $grouped) {
                $children = isset($grouped[$parentId]) ? $grouped[$parentId] : [];
                $result = [];
                foreach ($children as $child) {
                    $child['children'] = collect($buildTree($child['id'])); // Recursive children
                    $result[] = $child;
                }
                return $result;
            };

            // Build the hierarchy and return as a collection
            return collect($buildTree($parentId));
        });


    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
