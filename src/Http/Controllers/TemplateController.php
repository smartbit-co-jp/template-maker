<?php

namespace SmartBit\TemplateMaker\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SmartBit\TemplateMaker\Models\TemplateMaker;
use SmartBit\TemplateMaker\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SmartBit\TemplateMakerEditor;
use Support\Requests\ExportTemplateRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TemplateController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = [];
        foreach (Template::getTypes() as $type) {
            $types[$type] = [
                'stored_templates' => Template::whereType($type)->get()
            ];
        }

        $stored_templates = '';
        return view('template-maker::index', compact('types', 'stored_templates'));
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

        $template = Template::make(request()->type);
        $template->locale = 'ja';
        $template->is_default = 0;

        $template->save();

        if ($template->id) {
            return redirect()->route('template.edit', $template)
                ->with('sucess', __('Template created with success.'));
        }

        return redirect()->back()->with('wanrnig', __('document_template.save_failed'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function show(Template $template)
    {
        dd($template);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function edit(Template $template)
    {
        $template = Template::whereType('sample')->InRandomOrder()->first();

        $locales = ['en'];
        $locale = 'ja';
        $lang = $locale;

        $templater = new TemplateMaker($template->type, $locales);

        $doc_types = Template::getTypes();
        return view(
            'template-maker::edit', 
            compact('template', 'lang', 'doc_types', 'templater')
        );
        //     compact('documentTemplate','lang','doc_types', 'templater')
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
     * @param  \App\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Template $template)
    {
        if($request->make_default) {
            haken()->documentTemplates()->where('type', $template->type)
                                        ->update(['is_default' => false]);

            $template->is_default = true;
            $template->save();

            return redirect()->back()->with('success', __('document_template.edit_success'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function destroy(Template $template)
    {
        $url = app('url')->previous();

        $url = urlAppendQuery($url,[
            'tab' => 'documents',
            'subtab' => $template->type,
        ]);
            
        if($template->delete()) {
            return redirect()->to($url)->with('success', __('global.deleted'));
        } else {
            flashError(__('global.delete.failed'));
            return redirect()->to($url);
        }

    }
}