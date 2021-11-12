<?php

/*
 * You can place your custom package configuration in here.
 */
return [

 //    /**
 //     * Define supported document template types
 //     *
 //     * @type array
 //     *
 //     *    array(
 //     *        'invitation',
 //     *        'contract',
 //     *    )
 //     */
    'types' => [
        'sample',    
        'company_contract',    
    ],
    'classes' => [
        'company_contract' => 'App\\Models\\Company',
        // 'roudou' => 'App\Roudou',
        // 'working_condition' => 'App\WorkingCondition',
        // 'notice' => 'App\WorkingCondition\Notice',
        // 'ledger' => 'App\Kobetsu\Ledger',
    ],
    'path' => base_path('resources/assets/template-maker/document_template/'),
    'paths' => [
        'sample'           => base_path('resources/assets/template-maker/document_template/sample/template_config.json'),
        'company_contract'           => base_path('resources/assets/template-maker/document_template/company_contract/template_config.json'),
    ],
    'formaters' => [
        'phone'           => 'App\CustomClasses\Phone',
        'gender'           => 'App\CustomClasses\Gender',
        'date'           => 'Carbon\Carbon',
	],

];