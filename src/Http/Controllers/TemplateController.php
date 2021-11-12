<?php

namespace SmartBit\TemplateMaker\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use SmartBit\TemplateMaker\Models\TemplateMaker;
use SmartBit\TemplateMaker\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SmartBit\TemplateMakerEditor;
use Support\Requests\ExportDocumentTemplateRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TemplateController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

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
        $template->locale = 'en';
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
    public function edit($id)
    {
        $template = Template::find($id);
        $locales = ['ja', 'en'];
        $locale = 'en';
        $lang = $locale;

        $templater = new TemplateMaker($template->type, $locales);

        $doc_types = Template::getTypes();
        return view(
            'template-maker::edit', 
            compact('template', 'lang', 'doc_types', 'templater')
        );
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
    public function update(Request $request, Template $template)
    {

        if($request->make_default) {
            dump($template);
            dd($request);
            // haken()->documentTemplates()->where('type', $documentTemplate->type)
            //                             ->update(['is_default' => false]);

            // $documentTemplate->is_default = true;
            // $documentTemplate->save();

            return redirect()->back()->with('success', __('document_template.edit_success'));
        } else {
            $form = $request->form_data;


            $template->data = $form['data'];
            $template->style = $form['style'];
            $template->name = $form['name'];
            $template->type = $form['type'];
            $template->locale = $form['locale'];

            $template->update();

            return response()->json(['message' => __('global.update.success'), 'model' => $template, 'request' => $request->form_data, 'data' => $template->data['en']]);
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

        // check if its using this helpers method, else remove/update
        $url = urlAppendQuery($url,[
            'tab' => 'documents',
            'subtab' => $documentTemplate->type,
        ]);
            
        if($documentTemplate->delete()) {
            return redirect()->to($url)->with('success', __('global.deleted'));
        } else {
            // check if its using the helper method, else remove/update it
            flashError(__('global.delete.failed'));
            return redirect()->to($url);
        }

    }
}