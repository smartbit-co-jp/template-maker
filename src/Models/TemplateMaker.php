<?php

namespace SmartBit\TemplateMaker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Log;
use stdClass;
use Throwable;

class TemplateMaker
{
    public $type = '';
    public $locales = null;
    public $config_path = '';
    public $layout_path = '';
    public $style_path = '';

    /**
     * 
     * @param string $type 
     * @param array|Illuminate\Support\Collection $locales 
     * @return void 
     */
    public function __construct(string $type, $locales)
    {
        $this->type = $type;
        $this->locales = $locales;
        $this->config_path = config('template-maker.path').$this->type.'/template_config.json';
        $this->layout_path = config('template-maker.path').$this->type.'/template_layout.html';
        $this->style_path = config('template-maker.path').$this->type.'/template_style.css';
    }

    public function getKeyList($model)
    {
        $items = $this->getConfig();

        $keys = [];
        foreach ($items as $field => $item) {
            $keys = array_merge($keys, $this->processKeys($model, $item, $field));
        }

        return $keys;
    }
    
    public function getModel($model, $item, $field): Model
    {
        // dd($field);
       
        $model_object = new $item['class'];
        
        if ($model->$field instanceof Model) {
            $model_object = $model->$field;
        } elseif ($model->$field instanceof stdClass) {
            $model_object->forceFill((array) $model->$field);
        } elseif ($model->getAttributes()[$field] ?? null) {
            if(is_array($model->getAttributes()[$field])){
                $attributes = $model->getAttributes()[$field];
            }else{
                $attributes = json_decode($model->getAttributes()[$field], true);
            }
            $model_object->forceFill($attributes);
        } else {
            Log::error([
                'message' => 'Cant retrieve model',
                'model' => $model,
                'item' => $item,
                'field' => $field
            ]);
        }
        
        return $model_object;
    }

    public function formatAttribute($value, $formater) {
        $formater_args = explode('|',$formater);
        $formater = $formater_args[0];

        $format = $formater_args[1] ?? null;
        $formater = config('document-templates.formaters')[$formater];

        try {
            $formater = new $formater($value);
            return $formater->format($format);
        } catch (\Throwable $th) {
            return $value;
        }
    }

    public function getItemAttributeTemplate($value, $template, $locale ): string
    {
        $data = [
            'locale' => $locale,
            'value' => $value
        ];
        return view($template, $data)->render();
    }
    public function getItemAttribute($model, $attribute, $attribute_name): array
    {
        
        $attribute_key = $attribute['key'];
        $format = $attribute['format'] ?? null;
        $template = $attribute['template'] ?? null;
        $key_values = [];

        $key_name = "@($attribute_key)";
        try {
            if ($format) {
                // if($attribute_name == 'conflict_date_formatted'){
                    // dd($attri);
                    // dd($model->$attribute_name);
                // }
                $key_values[$key_name] = $this->formatAttribute($model->$attribute_name, $format);
            } elseif ( $template ) {
                $key_values[$key_name] = $this->getItemAttributeTemplate($model->$attribute_name, $template, fallback_locale());
            } else {
                $key_values[$key_name] = "{$model->$attribute_name}";
            }
        } catch (\Throwable $th) {
            sbLog($th);
        }

        if (object_get($model, $attribute_name . '_kana')) {
            $key_name = "@({$attribute_key}:kana)";
            $attribute_name_kana = $attribute_name . '_kana';
            $key_values[$key_name] = $model->$attribute_name_kana;              
        }

        if (Schema::hasColumn($model->getTable(), $attribute_name.'_romaji')) {
            $key_name = "@({$attribute_key}:".fallback_locale().")";
            $key_values[$key_name] = $model->$attribute_name;

            $key_name = "@({$attribute_key}:romaji)";
            $attribute_name_romaji = $attribute_name . '_romaji';
            $key_values[$key_name] =  $model->$attribute_name_romaji ?? $model->$attribute_name;
        } 
        
        if( method_exists($model, 'isTranslatableAttribute') && $model->isTranslatableAttribute($attribute_name)) {
            foreach ($this->locales as $locale) {
                $key_name = "@({$attribute_key}:{$locale})";
                if ($template) {
                    $key_values[$key_name] = $this->getItemAttributeTemplate($model->$attribute_name, $template, $locale);
                } else {
                    $key_values[$key_name] = $model->getTranslation($attribute_name, $locale);
                }
                
            }
        }

        return $key_values;
    }

    public function getItemAttributes($model, $item, $field): array
    {
        $key_values = [];
        foreach ($item['attributes'] as $attribute_name => $attribute) {
            if ($attribute['type'] == 'attribute') {
                $key_values = array_merge($key_values, $this->getItemAttribute($model, $attribute, $attribute_name));
            } else if ($attribute['type'] == 'model') {
                $model_object = $this->getModel($model, $attribute, $attribute_name);
                $key_values = array_merge($key_values, $this->getItemAttributes($model_object, $attribute, $attribute_name));
            } else if ($attribute['type'] == 'collection') {
                $collection = $model->$attribute_name;
                $variations = ['original'];
                foreach ($this->locales as $lang) {
                    $variations[] = $lang;
                }

                foreach ($variations as $var_locale) {
                    $key_name = "@($attribute[key])";
                    $locale = '';
                    if($var_locale!='original') {
                        $key_name = "@($attribute[key]:$var_locale)";
                    }
                    $locale = $var_locale=='original'?locale(): $var_locale;
                    $key_values[$key_name] = $this->getCollectionHtml($collection, $attribute['template'], $locale);
                }
            }
        }

        return $key_values;
    }

    public function getCollectionHtml($collection, $template, $locale)
    {
        $html = '';
        try {
            if($collection) {
                foreach ($collection as $collection_item) {
                    $data = [
                        'locale' => $locale,
                        'item' => (object) $collection_item
                    ];
                    $html .= view($template, $data)->render();
                }
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
        return $html;
    }
   
    public function processKeys($model, $item, $field): array
    {
        $key_values = [];

        switch ($item['type']) {
            case 'group':
                $key_values = array_merge($key_values, $this->getItemAttributes($model, $item, $field));       
                break;
            
            case 'model':
                $model_object = $this->getModel($model, $item, $field);
                $key_values = array_merge($key_values, $this->getItemAttributes($model_object, $item, $field));       
                break;
            default:
                # code...
                break;
        }

        return $key_values;
    }

    /**
     * 
     * @return string 
     * @throws Throwable 
     */
    public function makeKeyList()
    {
        $items = $this->getConfig();

        $html = '';
        foreach ($items as $field => $item) {
            $html .= $this->makeGroup($item);
        }

        return $html;
    }

    public function processItem($field, $item, $model)
    {
        $item['field'] = $field;

        switch ($item['type']) {
            case 'group':
                return $this->makeGroup($item, false);
                break;
            case 'model':
                return $this->makeModel($item);
                break;
            case 'attribute':
                return $this->makeItem($item, $model, $field);
                break;
            case 'collection':
                return $this->makeItem($item, $model, $field);
                break;

            default:
                break;
        }
    }

    public function trans(array $item, string $key)
    {
        $result = isset($item[$key]) ? __($item[$key]) : null;
        if (is_array($result)) {
            sbLog([
                'can\'t translate'=>[
                    'from_item'=>$item,
                    'from_key'=>$key,
                    'result' => $result,
                ]
            ]);
            return $key;
        } else {
            return $result;
        }
    }

    public function makeModel($model_item)
    {
        $title = $this->trans($model_item,'label');
        $contents = '';

        $model = isset($model_item['class'])?new $model_item['class']:null;

        if (isset($model_item['key'])&&$model_item['key']=='client.branch') {
            sbLog(compact('model_item'));
        }
        $contents .= $this->processAttributes($model_item['attributes'], $model);

        $data = compact('title', 'contents');

        return view('template-maker::template-maker.list-group-item-class', $data)->render();
    }

    public function makeItem($item, $model, $field)
    {
        $label = $this->trans($item,'label');
        $key = $item['key'];
        $formated_key = "@($key)";
        
        $content = View::make('template-maker::template-maker.list-group-item-key',compact('formated_key'));

        if ($model) {
            // if(has_kana($model,$field)) {
            //     $formated_key = "@($item[key]:kana)";
            //     $content .= View::make('template-maker::template-maker.list-group-item-key',compact('formated_key'));
            // }
            // if (has_romaji($model, $field)) {
            //     $formated_key = "@($item[key]:".fallback_locale().")";
            //     $content .= View::make('template-maker::template-maker.list-group-item-key',compact('formated_key'));
            //     $formated_key = "@($item[key]:romaji)";
            //     $content .= View::make('template-maker::template-maker.list-group-item-key',compact('formated_key'));
            // } elseif (method_exists($model, 'isTranslatableAttribute') && $model->isTranslatableAttribute($field)) {
                foreach ($this->locales as $locale) {
                    $formated_key = "@({$key}:{$locale})";
                    $content .= View::make('template-maker::template-maker.list-group-item-key',compact('formated_key'));
                }
            // }
            
        }elseif($item['type'] == 'collection'){
            foreach ($this->locales as $locale) {
                $formated_key = "@({$key}:{$locale})";
                $content .= View::make('template-maker::template-maker.list-group-item-key',compact('formated_key'));
            }
        }

        $data = compact('label', 'content', 'key');

        return view('template-maker::template-maker.list-group-item', $data)->render();
    }

    public function processAttributes(array $attributes, $model = null)
    {
        $contents = '';
        foreach ($attributes as $field => $item) {
            $contents .= $this->processItem($field, $item, $model);
            if($model) {

            }
        }
        return $contents;
    }

    public function makeGroup($group, $card = true) {
        $id = uniqid('group_');
        $title = $this->trans($group,'label');
        $contents = '';
        $model = null;
        if($group['type']=='model'){
            $class = $group['class'];
            $model = new $class;
        }
        $contents = $this->processAttributes($group['attributes'],$model);
        $data = compact('id', 'title', 'contents');

        if ( $card ) {
            return view('template-maker::template-maker.list-group', $data)->render();
        } else {
            return view('template-maker::template-maker.list-group-item-class', $data)->render();
        }

    }

    public function getConfig()
    {
        $path = $this->config_path;
        $json = file_get_contents($path);
        $arr = json_decode($json, true);

        return $arr;
    }
}