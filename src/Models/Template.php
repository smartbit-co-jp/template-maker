<?php

namespace SmartBit\TemplateMaker\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Exception;
use App\Exceptions\FileNotFoundException;

class Template extends Model
{
    use HasFactory;

    protected $table = 'document_templates';
    protected $casts = [
        'data' => 'array',
        'style' => 'array'
    ];

    protected static function booted()
    {
        static::deleting(function ($template) {
            if($template->isInUse()){
                flashError(__('contract.this_template_is_in_use'));
                return false;
            }
        });
    }

    /**
     * @throws Throwable
     */
    public function export(): string
    {
        $fileName = $this->generateFileName();
        $type = request('type') === 'html' ? 'data' : 'style';
        try {
            self::createIfNotExists(storage_path('document_template'));
            file_put_contents(storage_path('/document_template/' . $fileName), $this->{$type}[request('lang')]);
            return $fileName;
        }catch (Throwable $th) {
            throw $th;
        }
    }

    public static function createIfNotExists(string $folderName): void
    {
        if (!file_exists($folderName)) {
            mkdir($folderName, 0777, true);
        }
    }

    /**
     * @throws FileNotFoundException
     */
    public function import(): void
    {
        if (! $content = $this->getFile()) {
            throw new FileNotFoundException();
        }

        $type = request('type') === 'html' ? 'data' : 'style';
        $data = $this->{$type};
        $data[request('lang')] = $content;
        $this->{$type} = $data;
        $this->save();
    }

    public function generateFileName(): string
    {
        return $this->id . '_' . request('lang') . "_" . request('type') . "." . request('type');
    }

    public function getFile()
    {
        try {
            return file_get_contents(storage_path("/document_template/{$this->generateFileName()}"));
        } catch (Exception $e) {
            return false;
        }
    }

    static function make($type)
    {
        $template = new DocumentTemplate();
        $template->type = $type;
        $template->name = '書類テンプレート' . carbon('')->format('Y-m-d H:i:s');
        // $config_path = config('template-maker.path') . $template->type . '/template_config.json';
        $layout_path = config('template-maker.path') . $template->type . '/template_layout.html';
        $style_path = config('template-maker.path')  . $template->type . '/template_style.css';

        $template->data = [
            fallback_locale() => file_get_contents($layout_path)
        ];

        $template->style = [
            fallback_locale() => file_get_contents($style_path)
        ];

        return $template;
    }

    public function getFields(Model $model)
    {
        app()->setLocale($this->locale);
        // $contract_fields = $model->contract_fields??[];
        $contract_fields = $model->templateFields()??[];

        $fields = [];
        foreach ($contract_fields as $campo => $contract_field) {
            if($contract_field['type']=='class') {
                $class = $contract_field['class_name'];

                $object = new $class();

                $object->forceFill(
                    json_decode($model->getRawOriginal($campo),true)
                );
                
                foreach ($contract_field['properties'] as $key => $property) {
                    $fields[$key] = $object->$property;

                    if (isset($object->translatable)) {
                        if (in_array($property, $object->translatable)) {
                            foreach (haken()->settings->languages as $lang) {
                                $field = substr_replace($key, ":$lang", -1, 0);
                                $fields[$field] = $object->getTranslation($property, $lang);
                            }
                        }
                    }

                    if (isset($object->romanizable)) {
                        if (in_array($property, $object->romanizable)) {
                            $field = substr_replace($key, ":ja", -1, 0);;
                            $fields[$field] = $object->getAttribute($property);

                            $field = substr_replace($key, ":romaji", -1, 0);
                            $property_romaji = $property.'_romaji';
                            $fields[$field] = $object->$property_romaji;
                        }
                    }
                }
            } else if ($contract_field['type'] == 'property') {
                $key = $contract_field['class_name'];
                $fields[$key] = $model->$campo;

                if (isset($model->translatable)) {
                    if (in_array($campo, $model->translatable)) {
                        foreach (haken()->settings->languages as $lang) {
                            $field = substr_replace($key, ":$lang", -1, 0);
                            $fields[$field] = $model->getTranslation($campo, $lang);
                        }
                    }
                }

                if (isset($model->romanizable)) {
                    if (in_array($campo, $model->romanizable)) {
                        $field = substr_replace($key, ":ja", -1, 0);;
                        $fields[$field] = $model->getAttribute($campo);

                        $field = substr_replace($key, ":romaji", -1, 0);
                        $campo_romaji = $campo . '_romaji';
                        $fields[$field] = $model->$campo_romaji;
                    }
                }
            }
        }

        return $fields;
	}
	
	public function getConfig($type) {
		$path = config('template-maker.paths')[$type];
		$json = file_get_contents($path);
		$arr = json_decode($json, true);
		
		// return $path;
		// die($json);
		return $arr;
	}

    public function parent()
    {
        return $this->belongsTo('App\DocumentTemplate', 'parent_id');
    }

    public function withLocale(string $locale)
    {
        return $this->documentTemplates()->where('locale',$locale)->first();
    }

    public function addLocale(string $locale)
    {
        $data = $this->data;
        $style = $this->style;
        $data[$locale] = $this->data[fallback_locale()];
        $style[$locale] = $this->style[fallback_locale()];

        $this->data = $data;
        $this->style = $style;
    }

    public function hasLocale(string $locale)
    {
        return $this->getLocales()->contains($locale);
    }

    public function getLocales()
    {
        return collect($this->data)->keys();
    }

    static function getTypes()
    {
        return collect(config('template-maker.types')??[]);
    }

    static function getClasses()
    {
        return collect(config('template-maker.classes')??[]);
    }

    public function isInUse()
    {
        $model = self::getClasses()[$this->type];
        return (new $model)::where('document_template->id', $this->id)->get()->count() > 0;
    }

}