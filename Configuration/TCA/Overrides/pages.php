<?php

declare(strict_types=1);

call_user_func(
    function ($extKey, $table): void {
        $additionalColumns = [
            'tx_prefetch_enable' => [
                'label'       => 'LLL:EXT:prefetch/Resources/Private/Language/locallang.xlf:field.pages.tx_prefetch_enable.label',
                'description' => 'LLL:EXT:prefetch/Resources/Private/Language/locallang.xlf:field.pages.tx_prefetch_enable.description',
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
        ];
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $additionalColumns);

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
            $table,
            'caching',
            'tx_prefetch_enable'
        );

    },
    'prefetch',
    'pages'
);
