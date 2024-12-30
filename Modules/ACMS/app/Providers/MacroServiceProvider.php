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

            $grouped = $this->groupBy('parent_id');

            $buildHierarchy = function ($parentId) use (&$buildHierarchy, $grouped) {
                return $grouped->get($parentId, collect())->map(function ($item) use ($buildHierarchy) {
                    $item['children'] = $buildHierarchy($item['id']);
                    return $item;
                });
            };

            return $buildHierarchy($parentId)->values();
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
