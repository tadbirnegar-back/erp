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
            $nullIdNodes = [];
            foreach ($this->all() as $item) {
                // If the item has no `id` but also no `parent_id`, treat it as a root node
                if (is_null($item['id']) && is_null($item['parent_id'])) {
                    $nullIdNodes[] = $item;
                } else {
                    $grouped[$item['parent_id']][] = $item;
                }
            }

            // Recursive function to build the tree safely
            $buildTree = function ($parentId, &$visited = []) use (&$buildTree, $grouped) {
                if (!isset($grouped[$parentId])) {
                    return [];
                }
                $result = [];
                foreach ($grouped[$parentId] as $child) {
                    // Skip invalid entries where `id` is explicitly missing
                    if (!isset($child['id']) && !is_null($child['id'])) {
                        continue;
                    }

                    // Prevent infinite recursion by tracking visited nodes
                    $childId = $child['id'];
                    if (in_array($childId, $visited)) {
                        continue; // Avoid circular references
                    }
                    $visited[] = $childId;

                    // Recursively get children
                    $child['children'] = collect($buildTree($childId, $visited))->toArray();

                    // Remove unnecessary properties to prevent circular references
                    unset($child['parent_id']);

                    $result[] = $child;
                }
                return $result;
            };

            // Build the main hierarchy
            $tree = $buildTree($parentId);

            // If there are null `id` nodes that should be at the top level, add them
            if ($parentId === null && !empty($nullIdNodes)) {
                foreach ($nullIdNodes as &$node) {
                    $node['children'] = collect($buildTree(null))->toArray(); // Get its children
                }
                $tree = array_merge($nullIdNodes, $tree);
            }

            return collect($tree);
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
