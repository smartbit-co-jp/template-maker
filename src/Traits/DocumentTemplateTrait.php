<?php
namespace SmartBit\TemplateMaker\Traits;

use SmartBit\TemplateMaker\Models\Template;
use SmartBit\TemplateMaker\Models\TemplateMaker;
use Illuminate\Contracts\Container\BindingResolutionException;
use Throwable;

trait DocumentTemplateTrait
{
    public function getDocumentTemplateAttribute($value)
    {
        $value = json_decode($value, true)??[];

        if (method_exists($this, 'isDraft')) {
            if ($this->isDraft()) {
                if(isset($value['id'])){
                    return Template::find($value['id']);
                }else{
                    return null;
                }
            } else {
                $template = new Template();
                $template->forceFill($value);
                return $template;
            }
        }
    }
    public function setDocumentTemplateAttribute($value)
    {
        if (method_exists($this, 'isDraft')) {
            if ($this->isDraft()) {
                $value = collect($value->toArray())->only('id');
                $this->attributes['document_template'] = json_encode($value);
            } else {
                // check if it was using the helpers method
                $this->attributes['document_template'] = $value->toJson();
            }
        }
    }

    /**
     * temporarily store the user current locale
     * @var mixed
     */
    protected $locale_memory;

    /**
     * set the locale to use during document processing
     * @param mixed $locale 
     * @return void 
     */
    private function useLocale($locale)
    {
        $this->locale_memory = 'en';
        app()->setLocale($locale);
    }

    /**
     * restore locale to user locale
     * @return void 
     * @throws BindingResolutionException 
     */
    private function restoreLocale()
    {
        app()->setLocale($this->locale_memory);
        $this->locale_memory = null;
    }

    /**
     * get the document data with keys to replace in template
     * @param string|null $locale 
     * @return array 
     */
    public function getDocumentData(string $locale = null)
    {
        $locale = $locale??'en';

        $templater = new TemplateMaker($this->document_template->type, ['en', 'ja']);

        $this->useLocale('en');
        $keys = $templater->getKeyList($this);
        $this->restoreLocale();

        return $keys;
    }

    public function getDocumentLocales()
    {
        return array_keys($this->document_template->data);
    }

    /**
     * get final html rendered with all keys replaced with values
     * @param string|null $locale 
     * @return array|string 
     * @throws Throwable 
     */
    public function getDocumentHtml(string $locale = null)
    {
        $template = $this->document_template;

        $locale = $locale ?? 'en';
        if(!isset($template->data[$locale])){
            $locale = 'en';
            // $locale = fallback_locale();
        }
        
        $css = $template->style[$locale]??'';
        $html = $template->data[$locale]??'';
        
        $doc_data = $this->getDocumentData($locale);
        $keys = array_keys($doc_data);

        $html = str_replace($keys, $doc_data, $html);

        return view('template-maker::template-maker.base-layout', compact('css','html'))->render();
    }

    public function isDraft()
    {
		return $this->status =='draft';
	}
}