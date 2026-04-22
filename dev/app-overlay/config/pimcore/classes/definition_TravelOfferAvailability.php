<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - supplierOfferId [input]
 * - availableFrom [date]
 * - availableTo [date]
 * - nights [numeric]
 * - priceHint [numeric]
 * - currency [input]
 * - available [checkbox]
 * - status [select]
 * - travelOffer [manyToOneRelation]
 */

return \Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
    'dao' => null,
    'id' => '4',
    'name' => 'TravelOfferAvailability',
    'title' => '',
    'description' => '',
    'creationDate' => null,
    'modificationDate' => 1774960400,
    'userOwner' => 2,
    'userModification' => 2,
    'parentClass' => '',
    'implementsInterfaces' => '',
    'listingParentClass' => '',
    'useTraits' => '',
    'listingUseTraits' => '',
    'encryption' => false,
    'encryptedTables' =>
        array(),
    'allowInherit' => false,
    'allowVariants' => false,
    'showVariants' => false,
    'layoutDefinitions' =>
        \Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
            'name' => 'pimcore_root',
            'type' => null,
            'region' => null,
            'title' => null,
            'width' => 0,
            'height' => 0,
            'collapsible' => false,
            'collapsed' => false,
            'bodyStyle' => null,
            'datatype' => 'layout',
            'children' =>
                array(
                    0 =>
                        \Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                            'name' => 'Layout',
                            'type' => null,
                            'region' => null,
                            'title' => '',
                            'width' => '',
                            'height' => '',
                            'collapsible' => false,
                            'collapsed' => false,
                            'bodyStyle' => '',
                            'datatype' => 'layout',
                            'children' =>
                                array(
                                    0 =>
                                        \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                                            'name' => 'supplierOfferId',
                                            'title' => 'Supplier Offer Id',
                                            'tooltip' => '',
                                            'mandatory' => false,
                                            'noteditable' => false,
                                            'index' => false,
                                            'locked' => false,
                                            'style' => '',
                                            'permissions' => null,
                                            'fieldtype' => '',
                                            'relationType' => false,
                                            'invisible' => false,
                                            'visibleGridView' => false,
                                            'visibleSearch' => false,
                                            'blockedVarsForExport' =>
                                                array(),
                                            'defaultValue' => '',
                                            'columnLength' => 190,
                                            'regex' => '',
                                            'regexFlags' =>
                                                array(),
                                            'unique' => false,
                                            'showCharCount' => false,
                                            'width' => '',
                                            'defaultValueGenerator' => '',
                                        )),
                                    1 =>
                                        \Pimcore\Model\DataObject\ClassDefinition\Data\Date::__set_state(array(
                                            'name' => 'availableFrom',
                                            'title' => 'Available From',
                                            'tooltip' => '',
                                            'mandatory' => false,
                                            'noteditable' => false,
                                            'index' => false,
                                            'locked' => false,
                                            'style' => '',
                                            'permissions' => null,
                                            'fieldtype' => '',
                                            'relationType' => false,
                                            'invisible' => false,
                                            'visibleGridView' => false,
                                            'visibleSearch' => false,
                                            'blockedVarsForExport' =>
                                                array(),
                                            'defaultValue' => null,
                                            'useCurrentDate' => false,
                                            'columnType' => 'date',
                                            'defaultValueGenerator' => '',
                                        )),
                                    2 =>
                                        \Pimcore\Model\DataObject\ClassDefinition\Data\Date::__set_state(array(
                                            'name' => 'availableTo',
                                            'title' => 'Available To',
                                            'tooltip' => '',
                                            'mandatory' => false,
                                            'noteditable' => false,
                                            'index' => false,
                                            'locked' => false,
                                            'style' => '',
                                            'permissions' => null,
                                            'fieldtype' => '',
                                            'relationType' => false,
                                            'invisible' => false,
                                            'visibleGridView' => false,
                                            'visibleSearch' => false,
                                            'blockedVarsForExport' =>
                                                array(),
                                            'defaultValue' => null,
                                            'useCurrentDate' => false,
                                            'columnType' => 'date',
                                            'defaultValueGenerator' => '',
                                        )),
                                    3 =>
                                        \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric::__set_state(array(
                                            'name' => 'nights',
                                            'title' => 'Nights',
                                            'tooltip' => '',
                                            'mandatory' => false,
                                            'noteditable' => false,
                                            'index' => false,
                                            'locked' => false,
                                            'style' => '',
                                            'permissions' => null,
                                            'fieldtype' => '',
                                            'relationType' => false,
                                            'invisible' => false,
                                            'visibleGridView' => false,
                                            'visibleSearch' => false,
                                            'blockedVarsForExport' =>
                                                array(),
                                            'defaultValue' => null,
                                            'integer' => false,
                                            'unsigned' => false,
                                            'minValue' => null,
                                            'maxValue' => null,
                                            'unique' => false,
                                            'decimalSize' => null,
                                            'decimalPrecision' => null,
                                            'width' => '',
                                            'defaultValueGenerator' => '',
                                        )),
                                    4 =>
                                        \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric::__set_state(array(
                                            'name' => 'priceHint',
                                            'title' => 'Price Hint',
                                            'tooltip' => '',
                                            'mandatory' => false,
                                            'noteditable' => false,
                                            'index' => false,
                                            'locked' => false,
                                            'style' => '',
                                            'permissions' => null,
                                            'fieldtype' => '',
                                            'relationType' => false,
                                            'invisible' => false,
                                            'visibleGridView' => false,
                                            'visibleSearch' => false,
                                            'blockedVarsForExport' =>
                                                array(),
                                            'defaultValue' => null,
                                            'integer' => false,
                                            'unsigned' => false,
                                            'minValue' => null,
                                            'maxValue' => null,
                                            'unique' => false,
                                            'decimalSize' => 2,
                                            'decimalPrecision' => 2,
                                            'width' => '',
                                            'defaultValueGenerator' => '',
                                        )),
                                    5 =>
                                        \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                                            'name' => 'currency',
                                            'title' => 'Currency',
                                            'tooltip' => '',
                                            'mandatory' => false,
                                            'noteditable' => false,
                                            'index' => false,
                                            'locked' => false,
                                            'style' => '',
                                            'permissions' => null,
                                            'fieldtype' => '',
                                            'relationType' => false,
                                            'invisible' => false,
                                            'visibleGridView' => false,
                                            'visibleSearch' => false,
                                            'blockedVarsForExport' =>
                                                array(),
                                            'defaultValue' => '',
                                            'columnLength' => 190,
                                            'regex' => '',
                                            'regexFlags' =>
                                                array(),
                                            'unique' => false,
                                            'showCharCount' => false,
                                            'width' => '',
                                            'defaultValueGenerator' => '',
                                        )),
                                    6 =>
                                        \Pimcore\Model\DataObject\ClassDefinition\Data\Checkbox::__set_state(array(
                                            'name' => 'available',
                                            'title' => 'Available',
                                            'tooltip' => '',
                                            'mandatory' => false,
                                            'noteditable' => false,
                                            'index' => false,
                                            'locked' => false,
                                            'style' => '',
                                            'permissions' => null,
                                            'fieldtype' => '',
                                            'relationType' => false,
                                            'invisible' => false,
                                            'visibleGridView' => false,
                                            'visibleSearch' => false,
                                            'blockedVarsForExport' =>
                                                array(),
                                            'defaultValue' => null,
                                            'defaultValueGenerator' => '',
                                        )),
                                    7 =>
                                        \Pimcore\Model\DataObject\ClassDefinition\Data\Select::__set_state(array(
                                            'name' => 'status',
                                            'title' => 'Status',
                                            'tooltip' => '',
                                            'mandatory' => false,
                                            'noteditable' => false,
                                            'index' => false,
                                            'locked' => false,
                                            'style' => '',
                                            'permissions' => null,
                                            'fieldtype' => '',
                                            'relationType' => false,
                                            'invisible' => false,
                                            'visibleGridView' => false,
                                            'visibleSearch' => false,
                                            'blockedVarsForExport' =>
                                                array(),
                                            'options' =>
                                                array(
                                                    0 =>
                                                        array(
                                                            'key' => 'available',
                                                            'value' => 'available',
                                                        ),
                                                    1 =>
                                                        array(
                                                            'key' => 'limited',
                                                            'value' => 'limited',
                                                        ),
                                                    2 =>
                                                        array(
                                                            'key' => 'sold_out',
                                                            'value' => 'sold_out',
                                                        ),
                                                    3 =>
                                                        array(
                                                            'key' => 'inactive',
                                                            'value' => 'inactive',
                                                        ),
                                                ),
                                            'defaultValue' => 'available',
                                            'columnLength' => 190,
                                            'dynamicOptions' => false,
                                            'defaultValueGenerator' => '',
                                            'width' => '',
                                            'optionsProviderType' => 'configure',
                                            'optionsProviderClass' => '',
                                            'optionsProviderData' => '',
                                        )),
                                    8 =>
                                        \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation::__set_state(
                                            array(
                                                'name' => 'travelOffer',
                                                'title' => 'Travel Offer',
                                                'tooltip' => '',
                                                'mandatory' => false,
                                                'noteditable' => false,
                                                'index' => false,
                                                'locked' => false,
                                                'style' => '',
                                                'permissions' => null,
                                                'fieldtype' => '',
                                                'relationType' => true,
                                                'invisible' => false,
                                                'visibleGridView' => false,
                                                'visibleSearch' => false,
                                                'blockedVarsForExport' =>
                                                    array(),
                                                'classes' =>
                                                    array(
                                                        0 =>
                                                            array(
                                                                'classes' => 'TravelOffer',
                                                            ),
                                                    ),
                                                'displayMode' => 'grid',
                                                'pathFormatterClass' => '',
                                                'assetInlineDownloadAllowed' => false,
                                                'assetUploadPath' => '',
                                                'allowToClearRelation' => true,
                                                'objectsAllowed' => true,
                                                'assetsAllowed' => false,
                                                'assetTypes' =>
                                                    array(),
                                                'documentsAllowed' => false,
                                                'documentTypes' =>
                                                    array(),
                                                'width' => '',
                                            )
                                        ),
                                ),
                            'locked' => false,
                            'blockedVarsForExport' =>
                                array(),
                            'fieldtype' => 'panel',
                            'layout' => null,
                            'border' => false,
                            'icon' => '',
                            'labelWidth' => 100,
                            'labelAlign' => 'left',
                        )),
                ),
            'locked' => false,
            'blockedVarsForExport' =>
                array(),
            'fieldtype' => 'panel',
            'layout' => null,
            'border' => false,
            'icon' => null,
            'labelWidth' => 100,
            'labelAlign' => 'left',
        )),
    'icon' => '',
    'group' => '',
    'showAppLoggerTab' => false,
    'linkGeneratorReference' => '',
    'previewGeneratorReference' => '',
    'compositeIndices' =>
        array(),
    'showFieldLookup' => false,
    'propertyVisibility' =>
        array(
            'grid' =>
                array(
                    'id' => true,
                    'key' => false,
                    'path' => true,
                    'published' => true,
                    'modificationDate' => true,
                    'creationDate' => true,
                ),
            'search' =>
                array(
                    'id' => true,
                    'key' => false,
                    'path' => true,
                    'published' => true,
                    'modificationDate' => true,
                    'creationDate' => true,
                ),
        ),
    'enableGridLocking' => false,
    'deletedDataComponents' =>
        array(),
    'blockedVarsForExport' =>
        array(),
    'fieldDefinitionsCache' =>
        array(),
    'activeDispatchingEvents' =>
        array(),
));
