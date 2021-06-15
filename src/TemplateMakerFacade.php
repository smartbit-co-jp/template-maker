<?php

namespace SB\DocumentTemplateEditor;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SB\DocumentTemplateEditor\Skeleton\SkeletonClass
 */
class DocumentTemplateEditorFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'document-template-editor';
    }
}
