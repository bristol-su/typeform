<?php

return [
    'name' => 'Typeform',
    'description' => 'Connect the portal to typeform and manage responses',
    'settings' => [
        'schema' => [
            'groups' => [
                [
                    'legend' => "Page Design",
                    'fields' => [
                        [
                            'type' => 'input',
                            'inputType' => 'text',
                            'label' => 'Module Title',
                            'model' => 'title'
                        ],
                        [
                            'type' => 'textArea',
                            'label' => 'Description',
                            'hint' => 'This will appear at the top of the page',
                            'rows' => 4,
                            'model' => 'description'
                        ]
                    ]
                ],
                [
                    'legend' => 'Embedded Form',
                    'fields' => [
                        [
                            'type' => 'radios',
                            'label' => 'Type of embed',
                            'hint' => 'Embed the form in the page, or show the form as a popup?',
                            'model' => 'embed_type',
                            'values' => [
                                ['name' => 'Embed the form in the page', 'value' => 'widget'],
                                ['name' => 'Show the form as a popup', 'value' => 'popup'],
                                ['name' => 'Show the form as a drawer from the left', 'value' => 'drawer_left'],
                                ['name' => 'Show the form as a drawer from the right', 'value' => 'drawer_right'],
                            ]
                        ],
                        [
                            'type' => 'input',
                            'inputType' => 'text',
                            'label' => 'Form URL',
                            'hint' => 'The URL of the form. Make sure it\'s published first!',
                            'model' => 'form_url'
                        ],
                        [
                            'type' => 'switch',
                            'label' => 'Hide form headers?',
                            'hint' => 'Should we hide the form headers? This helps integrate the form into the page.',
                            'model' => 'hide_headers',
                            'textOn' => 'Hidden',
                            'textOff' => 'Shown',
                        ],
                        [
                            'type' => 'switch',
                            'label' => 'Hide form footer?',
                            'hint' => 'Should we hide the form footer? This helps integrate the form into the page.',
                            'model' => 'hide_footer',
                            'textOn' => 'Hidden',
                            'textOff' => 'Shown',
                        ],
                    ]
                ],
                [
                    'legend' => 'Responses',
                    'fields' => [
                        [
                            'type' => 'switch',
                            'label' => 'Save responses?',
                            'hint' => 'Do you want responses to be saved on the portal? You will always be able to see responses on typeform.',
                            'model' => 'collect_responses',
                            'textOn' => 'Save',
                            'textOff' => 'Do not save',
                        ],
                        [
                            'type' => 'input',
                            'inputType' => 'text',
                            'label' => 'Form ID',
                            'hint' => 'ID of the form so we can collect responses.',
                            'model' => 'form_id',
                        ]
                    ]
                ],
            ]
        ],
        'model' => [
            'title' => 'Default Title',
            'description' => '',
            'embed_type' => 'widget',
            'form_url' => '',
            'hide_headers' => true,
            'hide_footer' => true,
            'collect_responses' => true,
            'form_id' => ''
        ],
        'options' => [
            'validateDebounceTime' => 0
        ]
    ],
];
