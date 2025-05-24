<?php

namespace Modules\ODOC\app\Observers;

use Modules\ODOC\app\Events\DocumentEvent;
use Modules\ODOC\app\Events\OdocApproversEvent;
use Modules\ODOC\app\Models\Document;

class DocumentObserver
{
    /**
     * Handle the Document "created" event.
     */
    public function created(Document $document): void
    {
        event(new DocumentEvent($document));
    }

    /**
     * Handle the Document "updated" event.
     */
    public function updated(Document $document): void
    {
        //
    }

    /**
     * Handle the Document "deleted" event.
     */
    public function deleted(Document $document): void
    {
        //
    }

    /**
     * Handle the Document "restored" event.
     */
    public function restored(Document $document): void
    {
        //
    }

    /**
     * Handle the Document "force deleted" event.
     */
    public function forceDeleted(Document $document): void
    {
        //
    }
}
