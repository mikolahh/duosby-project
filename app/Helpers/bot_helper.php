<?php
// url разработчика
function developUrl()
{
    return 'https://mikalay.tech/my-bots-logs';
}
// Массив с общими настройками
function botIni()
{
    return  [
        'token_bot' => '6365999856:AAFBLCAXpRKhzElfFMVeRqrIatHEH0ZYxQI',//@DuosSmart_bot
        'host_bot' => 'https://duos.by/smart-bot',              
    ];
}
// Конфигурационный массив  бота
function botConfig()
{
   return [                
        '/search' => ['Прямой поиск', [0, 1, 2, 1, 1], 1],
        '/smart-search' => ['Умный поиск', [0, 1, 2, 1, 1, 1, 1], 2],     
    ];
}
// Тестовые данные телеграм бота
function tgTestData()
{
   return [
        'Mikalai-start' => [
            'update_id' => 936282658,
            'message' => [
                'message_id' => 26,
                'from' => [
                    'id' => 902636138,
                    'is_bot' => '',
                    'first_name' => 'Mikalai',
                    'last_name' => 'Khadanovich',
                    'username' => 'Mikola_H',
                    'language_code' => 'ru',
                ],
                'chat' => [
                    'id' => 902636138,
                    'first_name' => 'Mikalai',
                    'last_name' => 'Khadanovich',
                    'username' => 'Mikola_H',
                    'type' => 'private',
                ],
                'date' => 1684497657,
                'text' => '/start',
                'entities' => [
                    [
                        'offset' => 0,
                        'length' => 6,
                        'type' => 'bot_command',
                    ]
                ]
            ]
        ],       
        'smart-search' => [
            'update_id' => 862813740,
            'callback_query' => [
                'id' => 3876792696870289015,
                    'from' => [
                        'id' => 9902636138,
                        'is_bot' => '',
                        'first_name' => 'Mikalai',
                        'last_name' => 'Khadanovich',
                        'username' => 'Mikola_H',
                        'language_code' => 'ru',
                    ],
                    'message' => [
                        'message_id' => 1511,
                            'from' => [
                                'id' => 5940823121,
                                'is_bot' => 1,
                                'first_name' => 'mikolateach1',
                                'username' => 'mikolateach1_bot',
                            ],
                            'chat' => [
                                'id' => 9902636138,
                                'first_name' => 'Mikalai',
                                'last_name' => 'Khadanovich',
                                'username' => 'Mikola_H',
                                'type' => 'private',
                            ],
                            'date' => 1686424377,
                            'text' => "Здравствуйте, Mikalai! Если вы точно знаете модель мобильного устройства и имеете опыт пользования ботом, выбирайте \"Прямой поиск\". В противном случае рекомендуется пользоваться \"Умным поиском\"",
                            'reply_markup' => [
                                'inline_keyboard' => [
                                        0 => [
                                                0 => [
                                                        'text' => 'Поиск',
                                                            'callback_data' => '/search',
                                                    ],
                                            ],

                                            1 => [
                                                0 => [
                                                        'text' => 'Умный поиск',
                                                            'callback_data' => '/smart-search',
                                                    ],
                                            ],
                                    ],
                            ],
                    ],
                    'chat_instance' => -2488080776525970339,
                    'data' => '/smart-search',
            ]
        ],   
        'touchscreen' => [
            'update_id' => 862813613,
            'callback_query' => [
                'id' => 3876792695863249336,
                'from' => [
                    'id' => 902636138,
                    'is_bot' => '', 
                    'first_name' => 'Mikalai',
                    'last_name' => 'Khadanovich',
                    'username' => 'Mikola_H',
                    'language_code' => 'ru',
                ],
                'message' => [
                    'message_id' => '1348',
                    'from' => [
                        'id' => '5940823121',
                        'is_bot' => 1,
                        'first_name' => 'mikolateach1',
                        'username' => 'mikolateach1_bot',
                    ],
                    'chat' => [
                        'id' => '902636138',
                        'first_name' => 'Mikalai',
                        'last_name' => 'Khadanovich',
                        'username' => 'Mikola_H',
                        'type' => 'private',
                    ],
                    'date' => '1685999432',
                    'text' => 'Здравствуйте, Mikalai!
                    Для быстрого поиска необходимой вам детали выберите вид, к которому она относится.',
                    'reply_markup' => [
                        'inline_keyboard' => [
                            0 => [
                                0 => [
                                    'text' => 'Тачскрины',
                                    'callback_data' => 'touch_screen',
                                ],
                                1 => [
                                    'text' => 'Аккумуляторные батареи',
                                    'callback_data' => 'battery',
                                ],                               
                            ],
                            1 => [
                                0 => [
                                    'text' => 'Дисплейные модули',
                                    'callback_data' => 'lcd_assembly',
                                ],
                                1 => [
                                    'text' => 'Дисплеи',
                                    'callback_data' => 'lcd',
                                ],                               
                            ],
                            2 => [
                                0 => [
                                    'text' => 'Дисплейные шлейфы',
                                    'callback_data' => 'flex_lcd',
                                ],
                                1 => [
                                    'text' => 'Основные платы',
                                    'callback_data' => 'mainboard',
                                ],                               
                            ],
                            3 => [
                                0 => [
                                    'text' => 'Платы зарядки',
                                    'callback_data' => 'flex_charge',
                                ],
                                1 => [
                                    'text' => 'Передние стекла',
                                    'callback_data' => 'glass',
                                ],                               
                            ],
                            4 => [
                                0 => [
                                    'text' => 'Крышки корпуса',
                                    'callback_data' => 'back_cover',
                                ],
                                1 => [
                                    'text' => 'Шлейфы c кнопками',
                                    'callback_data' => 'flex_on_off',
                                ],                               
                            ],
                            5 => [
                                0 => [
                                    'text' => 'Вызывные динамики',
                                    'callback_data' => 'buzzer',
                                ],
                                1 => [
                                    'text' => 'Дисплейные рамки',
                                    'callback_data' => 'frame_lcd',
                                ],                               
                            ],
                            6 => [
                                0 => [
                                    'text' => 'Микросхемы',
                                    'callback_data' => 'ic',
                                ],                                                             
                            ],
                        ]
                    ]

                ],
                'chat_instance' => -2488080776525970339,
                'data' => 'touch_screen',
            ]

        ],
        'touchscreen-continue' => [
            'update_id' => 862813739,
            'callback_query' => [
                'id' => 3876792697118307193,
                'from' => [
                    'id' => 902636138,
                        'is_bot' => '',
                        'first_name' => 'Mikalai',
                        'last_name' => 'Khadanovich',
                        'username' => 'Mikola_H',
                        'language_code' => 'ru',
                ],    
                'message' => [
                    'message_id' => 1510,
                    'from' => [
                        'id' => 5940823121,
                        'is_bot' => 1,
                        'first_name' => 'mikolateach1',
                        'username' => 'mikolateach1_bot',
                    ],    
                    'chat' => [
                        'id' => 902636138,
                        'first_name' => 'Mikalai',
                        'last_name' => 'Khadanovich',
                        'username' => 'Mikola_H',
                        'type' => 'private',
                    ],    
                    'date' => 1686420661,
                    'text' => "Вы выбрали категорию \"Тачскрины\". Отправьте в сообщении ключевое слово или фразу для более быстрого поиска или просто нажмите \"далее\"",
                    'reply_markup' => [
                        'inline_keyboard' => [
                            0 => [
                                0 => [
                                    'text' => 'В начало',
                                        'callback_data' => 'exit',
                                ],

                                1 => [
                                    'text' => 'Далее',
                                    'callback_data' => 'continue',
                                ],
                            ]
                        ]
                    ]    
                ],    
                'chat_instance' => -2488080776525970339,
                'data' => 'continue',                
            ],
        ],
        'match-a5008' => [
            'update_id' => 862813819,
            'message' => [
                'message_id' => 1610,
                    'from' => [
                        'id' => 902636138,
                            'is_bot' => '',
                            'first_name' => 'Mikalai',
                            'last_name' => 'Khadanovich',
                            'username' => 'Mikola_H',
                            'language_code' => 'ru',
                    ],
                    'chat' => [
                        'id' => 902636138,
                            'first_name' => 'Mikalai',
                            'last_name' => 'Khadanovich',
                            'username' => 'Mikola_H',
                            'type' => 'private',
                    ],
                    'date' => 1686431835,
                    'text' => '3327',
            ]
        ],
        'a5008' => [
            'update_id' => 862816600,
            'callback_query' => [
                'id' => 3876792695294512863,
                'from' => [
                    'id' => 902636138,
                    'is_bot' => '',
                    'first_name' => 'Mikalai',
                    'last_name' => 'Khadanovich',
                    'username' => 'Mikola_H',
                    'language_code' => 'ru',
                ],
                'message' => [
                    'message_id' => 5642,
                    'from' => [
                    'id' => 5940823121,
                    'is_bot' => 1,
                    'first_name' => 'mikolateach1',
                    'username' => 'mikolateach1_bot',
                    ],
                    'chat' => [
                        'id' => 902636138,
                        'first_name' => 'Mikalai',
                        'last_name' => 'Khadanovich',
                        'username' => 'Mikola_H',
                        'type' => 'private',
                    ],
                    'date' => 1687079948,
                    'text' => 'Найдены и показаны',
                    'entities' => [
                        0 => [
                            'offset' => 0,
                            'length' => 34,
                            'type' => 'italic',
                        ],
                        1 => [
                            'offset' => 35,
                            'length' => 66,
                            'type' => 'italic',
                        ],
                    ],
                    'reply_markup' => [
                        'inline_keyboard' => [
                            0 => [
                                0 => [
                                    'text' => 'Wize PMT3327 3G',
                                    'callback_data' => 'prestigio-wize-pmt3327-3g-touch-screen',
                                ],
                            ],
                            1 => [
                                0 => [
                                    'text' => 'Назад',
                                    'callback_data' => 'return',
                                ],
                                1 => [
                                    'text' => 'В начало',
                                    'callback_data' => '/start',
                                ],
                            ],
                        ],
                    ],
                ],
                'chat_instance' => -2488080776525970339,
                'data' => 'acer-iconia-one-10-b3-a30-b3-a32-a5008-touch-screen',
            ],
        ],
        'return' => [
            'update_id' => 862813919,
            'callback_query' => [
                'id' => 3876792692929464450,
                    'from' => [
                        'id' => 902636138,
                        'is_bot' => '',
                        'first_name' => 'Mikalai',
                        'last_name' => 'Khadanovich',
                        'username' => 'Mikola_H',
                        'language_code' => 'ru',
                    ],
                    'message' => [
                        'message_id' => 1709,
                            'from' => [                        
                                'id' => 5940823121,
                                'is_bot' => 1,
                                'first_name' => 'mikolateach1',
                                'username' => 'mikolateach1_bot',
                            ],
                            'chat' => [
                                'id' => 902636138,
                                    'first_name' => 'Mikalai',
                                    'last_name' => 'Khadanovich',
                                    'username' => 'Mikola_H',
                                    'type' => 'private',
                            ],
                            'date' => 1686468071,
                            'text' => '',
                            'reply_markup' => [
                                'inline_keyboard' => [
                                        0 => [
                                                0 => [
                                                        'text' => 'Назад',
                                                        'callback_data' => 'return',
                                                    ],
                                                    1 => [
                                                        'text' => 'В начало',
                                                        'callback_data' => 'exit',
                                                    ]
                                            ]
                                    ]
                            ]
                    ],
                'chat_instance' => -2488080776525970339,
                'data' => 'return',
            ]
        ],
        'Mikalai-exit' => [
            'update_id' => 862813919,
            'callback_query' => [
                'id' => 3876792692929464450,
                    'from' => [
                        'id' => 902636138,
                        'is_bot' => '',
                        'first_name' => 'Mikalai',
                        'last_name' => 'Khadanovich',
                        'username' => 'Mikola_H',
                        'language_code' => 'ru',
                    ],
                    'message' => [
                        'message_id' => 1709,
                            'from' => [                        
                                'id' => 5940823121,
                                'is_bot' => 1,
                                'first_name' => 'mikolateach1',
                                'username' => 'mikolateach1_bot',
                            ],
                            'chat' => [
                                'id' => 902636138,
                                    'first_name' => 'Mikalai',
                                    'last_name' => 'Khadanovich',
                                    'username' => 'Mikola_H',
                                    'type' => 'private',
                            ],
                            'date' => 1686468071,
                            'text' => '',
                            'reply_markup' => [
                                'inline_keyboard' => [
                                        0 => [
                                                0 => [
                                                        'text' => 'Назад',
                                                        'callback_data' => 'return',
                                                    ],
                                                    1 => [
                                                        'text' => 'В начало',
                                                        'callback_data' => 'exit',
                                                    ]
                                            ]
                                    ]
                            ]
                    ],
                'chat_instance' => -2488080776525970339,
                'data' => 'exit',
            ]
        ],
        'duos-младший-/start' => [
            'update_id' => 936285004,
            'message' => [
                'message_id' => 3054,
                    'from' => [
                        'id' => 6187505461,
                        'is_bot' => '',
                        'first_name' => 'Николай Младший',
                        'username' => 'Duosby',
                        'language_code' => 'ru',
                    ],
                    'chat' => [
                        'id' => 6187505461,
                        'first_name' => 'Николай Младший',
                        'username' => 'Duosby',
                        'type' => 'private',
                    ],
                    'date' => 1687070761,
                    'text' => '/start',
                    'entities' => [
                        0 => [
                            'offset' => 0,
                            'length' => 6,
                            'type' => 'bot_command',
                        ],
                    ],
            ],
        ],
        'duos-младший-callback-/search' => [
            'update_id' => 862816481,
            'callback_query' => [
                'id' => 8128389527030867003,
                'from' => [
                    'id' => 6187505461,
                    'is_bot' => '',
                    'first_name' => 'Николай Младший',
                    'username' => 'Duosby',
                    'language_code' => 'ru',
                ],
                'message' => [
                    'message_id' => 5423,
                        'from' => [
                            'id' => 5940823121,
                            'is_bot' => 1,
                            'first_name' => 'mikolateach1',
                            'username' => 'mikolateach1_bot',
                        ],
                        'chat' => [
                            'id' => 6187505461,
                            'first_name' => 'Николай Младший',
                            'username' => 'Duosby',
                            'type' => 'private',
                        ],
                        'date' => 1687002812,
                        'text' => 'Здравствуйте, Николай Младший!',
                        'reply_markup' => [
                            'inline_keyboard' => [
                                0 => [
                                    0 => [
                                        'text' => 'Прямой поиск',
                                        'callback_data' => '/search',
                                    ],
                                ],
                                1 => [
                                    0 => [
                                        'text' => 'Умный поиск',
                                        'callback_data' => '/smart-search',
                                    ],
                                ],
                                2 => [
                                    0 => [
                                        'text' => 'Выход',
                                        'callback_data' => 'exit',
                                    ],
                                ],
                            ],
                        ],
                ],
                'chat_instance' => 6169232315188122925,
                'data' => '/search',
            ],
        ],
        'duos-Mikolay-start' => [
            'update_id' => 936284872,
            'message' => [
                'message_id' => 2881,
                'from' => [
                    'id' => 309613879,
                        'is_bot' => '',
                        'first_name' => 'Mikolay',
                        'last_name' => 'DuoS.by',
                        'username' => 'duos_by',
                        'language_code' => 'ru',
                ],
                'chat' => [
                    'id' => 309613879,
                    'first_name' => 'Mikolay',
                    'last_name' => 'DuoS.by',
                    'username' => 'duos_by',
                    'type' => 'private',
                ],
                'date' => 1687009008,
                'text' => '/start',
                'entities' => [
                    0 => [
                        'offset' => 0,
                            'length' => 6,
                            'type' => 'bot_command',
                    ],
                ],
            ],
        ],
        'duos-Mikolay-message-auto-delete-time' => [
            'update_id' => 936284923,
            'message' => [
                'message_id' => 2949,
                'from' => [
                    'id' => 309613879,
                    'is_bot' => '',
                    'first_name' => 'Mikolay',
                    'last_name' => 'DuoS.by',
                    'username' => 'duos_by',
                    'language_code' => 'ru',
                ],
                'chat' => [
                    'id' => 309613879,
                    'first_name' => 'Mikolay',                        
                    'last_name' => 'DuoS.by',
                    'username' => 'duos_by',
                    'type' => 'private',
                ],
                'date' => 1687020857,
                'message_auto_delete_timer_changed' => [
                'message_auto_delete_time' => 86400,
                ],
            ],
        ],        
    ];
}