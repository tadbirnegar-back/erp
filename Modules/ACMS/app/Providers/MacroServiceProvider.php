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
            // Index items by their ID for quick access
            $items = $this->keyBy('id');
            $roots = collect();

            // Iterate through all items to build the hierarchy
            foreach ($items as $item) {
                // Check if the item is a root item
                if ($item->parent_id === $parentId) {
                    $roots->push($item);
                }
                // Assign the item to its parent's children collection
                if ($item->parent_id !== null && $items->has($item->parent_id)) {
                    $parent = $items->get($item->parent_id);
                    // Initialize children collection if not exists
                    if (!isset($parent->children)) {
                        $parent->children = collect();
                    }
                    $parent->children->push($item);
                }
            }

            // Traverse the hierarchy and set 'children' to an empty collection if not present
            $queue = clone $roots;

            while (!$queue->isEmpty()) {
                $item = $queue->shift();
                // Set 'children' to an empty collection if it doesn't exist
                if (!isset($item->children)) {
                    $item->children = collect();
                }
                // Add children to the queue for processing
                if ($item->children !== null) {
                    $queue = $queue->concat($item->children);
                }
            }

            return $roots->values();
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
