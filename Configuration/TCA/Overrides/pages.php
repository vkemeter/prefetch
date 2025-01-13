<?php

declare(strict_types=1);

call_user_func(
    function ($extKey, $table): void {
        $additionalColumns = [
            'tx_prefetch_enable' => [
                'label'       => 'LLL:EXT:prefetch/Resources/Private/Language/locallang.xlf:field.pages.tx_prefetch_enable.label',
                'description' => 'LLL:EXT:prefetch/Resources/Private/Language/locallang.xlf:field.pages.tx_prefetch_enable.description',
                'onChange'    => 'reload',
                'config'      => [
                    'type'       => 'check',
                    'renderType' => 'checkboxLabeledToggle',
                    'items'      => [
                        [
                            'label'          => '',
                            'labelChecked'   => 'LLL:EXT:prefetch/Resources/Private/Language/locallang.xlf:field.pages.tx_prefetch_enable.items.labelChecked',
                            'labelUnchecked' => 'LLL:EXT:prefetch/Resources/Private/Language/locallang.xlf:field.pages.tx_prefetch_enable.items.labelUnchecked',
                        ],
                    ],
                ],
            ],
            'tx_prefetch_type' => [
                'label'       => 'LLL:EXT:prefetch/Resources/Private/Language/locallang.xlf:field.pages.tx_prefetch_type.label',
                'description' => 'LLL:EXT:prefetch/Resources/Private/Language/locallang.xlf:field.pages.tx_prefetch_type.description',
                'displayCond' => 'FIELD:tx_prefetch_enable:REQ:true',
                'config'      => [
                    'type'       => 'select',
                    'renderType' => 'selectSingle',
                    'items'      => [
                        ['prefetch', \Supseven\Prefetch\Enumerations\Types::PREFETCH],
                        ['prerender', \Supseven\Prefetch\Enumerations\Types::PRERENDER],
                    ],
                ],

            ],
            'tx_prefetch_eagerness' => [
                'label'       => 'LLL:EXT:prefetch/Resources/Private/Language/locallang.xlf:field.pages.tx_prefetch_eagerness.label',
                'description' => 'LLL:EXT:prefetch/Resources/Private/Language/locallang.xlf:field.pages.tx_prefetch_eagerness.description',
                'displayCond' => 'FIELD:tx_prefetch_enable:REQ:true',
                'config'      => [
                    'type'       => 'select',
                    'renderType' => 'selectSingle',
                    'items'      => [
                        ['immediate', 0],
                        ['eager', 1],
                        ['moderate', 2],
                        ['conservative', 3],
                    ],
                ],

            ],
        ];
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $additionalColumns);

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
            $table,
            'caching',
            '--linebreak--,tx_prefetch_enable,tx_prefetch_type,tx_prefetch_eagerness'
        );

    },
    'prefetch',
    'pages'
);
