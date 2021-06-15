<?php

namespace SmartBit\TemplateMaker\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SmartBit\TemplateMaker\Models\TemplateMaker;
use SmartBit\TemplateMaker\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SmartBit\TemplateMakerEditor;
use Support\Requests\ExportDocumentTemplateRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TemplateMakerController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $template_types = Template::getTypes();
        $stored_templates = Template::where('type', $type)->get();
        $subtab = '';
        return view('template-maker::index', compact('subtab', 'template_types'));
    }

    public function export(DocumentTemplate $documentTemplate, ExportDocumentTemplateRequest $request): ?JsonResponse
    {
        try {
            $file_name = $documentTemplate->export();
            return response()->json(['file_name' => $file_name]);
        } catch (Throwable $th) {
            return response()->json(['exceptions' => [$th->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
    }

    public function import(DocumentTemplate $documentTemplate, ExportDocumentTemplateRequest $request): ?JsonResponse
    {
        try {
            $documentTemplate->import();
            return response()->json(['saved' => true]);
        } catch (Throwable $th) {
            return response()->json(['exceptions' => [$th->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'type' => 'required'
        ]);

        $template = DocumentTemplate::make(request()->type);
        $template->locale = fallback_locale();
        $template->is_default = haken()->documentTemplates()->where('type', request()->type)->get()->count() == 0;

        haken()->documentTemplates()->save($template);

        if ($template->id) {
            return redirect()->action('DocumentTemplateController@edit', $template)
                ->with('sucess', __('document_template.create_success'));
        }

        return redirect()->back()->with('wanrnig', __('document_template.save_failed'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentTemplate $documentTemplate)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentTemplate $documentTemplate)
    {
        $templater = new TemplateMaker($documentTemplate->type, haken()->languages);

        $doc_types = DocumentTemplate::getTypes();
        $lang = request()->lang??fallback_locale();
        if (haken()->languages->contains($lang)) {
            if($lang != fallback_locale()) {
                if(!$documentTemplate->hasLocale($lang)) {
                    $documentTemplate->addLocale($lang);
                    $documentTemplate->save();
                }
            }
        } else {
            abort(404);
        }

        return view('document_template.edit', compact('documentTemplate','lang','doc_types', 'templater'));
    }

    public function getTemplates(string $type)
    {
        $templates =  haken()->documentTemplates()->where('type', $type)->get();
        return response()->json($templates);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        if($request->make_default) {
            haken()->documentTemplates()->where('type', $documentTemplate->type)
                                        ->update(['is_default' => false]);

            $documentTemplate->is_default = true;
            $documentTemplate->save();

            return redirect()->back()->with('success', __('document_template.edit_success'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DocumentTemplate  $documentTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentTemplate $documentTemplate)
    {
        $url = app('url')->previous();

        $url = urlAppendQuery($url,[
            'tab' => 'documents',
            'subtab' => $documentTemplate->type,
        ]);
            
        if($documentTemplate->delete()) {
            return redirect()->to($url)->with('success', __('global.deleted'));
        } else {
            flashError(__('global.delete.failed'));
            return redirect()->to($url);
        }

    }
}