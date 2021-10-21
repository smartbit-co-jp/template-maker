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
        'other sample',
        'another sample'
    ],
    'classes' => [
        // 'kobetsu' => 'App\Kobetsu',
        // 'roudou' => 'App\Roudou',
        // 'working_condition' => 'App\WorkingCondition',
        // 'notice' => 'App\WorkingCondition\Notice',
        // 'ledger' => 'App\Kobetsu\Ledger',
    ],
    'path' => base_path('resources/assets/document_template/'),
    'paths' => [
        'sample'           => base_path('resources/assets/document_template/sample/template_config.json'),
        'roudou'            => base_path('resources/assets/document_template/roudou/template_config.json'),
        'working_condition' => base_path('resources/assets/document_template/working_condition/template_config.json'),
        'notice'            => base_path('resources/assets/document_template/notice/template_config.json'),
        'ledger'            => base_path('resources/assets/document_template/ledger/template_config.json'),
    ],
    'formaters' => [
        'phone'           => 'App\CustomClasses\Phone',
        'gender'           => 'App\CustomClasses\Gender',
        'date'           => 'Carbon\Carbon',
	],

];