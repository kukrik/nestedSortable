<?php
namespace QCubed\Plugin;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Project\Jqui\Sortable;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class NestedSortableGen
 *
 * This is the SortableGen class which ..........
 *
 *
 *
 *
 *
 *
 *
 *
 */

class NestedSortableGen extends Sortable
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var boolean */
    protected $blnDisableParentChange = null;
    /** @var boolean */
    protected $blnDoNotClear = null;
    /** @var integer */
    protected $intExpandOnHover =  null;
    /** @var boolean */
    protected $blnIsTree =  null;
    /** @var string */
    protected $strListType =  null;
    /** @var integer */
    protected $intMaxLevels =  null;
    /** @var boolean */
    protected $blnProtectRoot =  null;
    /** @var integer */
    protected $intRootId = null;
    /** @var boolean */
    protected $blnRTL = null;
    /** @var boolean */
    protected $blnStartCollapsed =  null;
    /** @var integer */
    protected $intTabSize =  null;
    /** @var string */
    protected $strToleranceElement =  null;
    /** @var string */
    protected $strBranchClass = null;
    /** @var string */
    protected $strCollapsedClass = null;
    /** @var string */
    protected $strDisableNestingClass = null;
    /** @var string */
    protected $strErrorClass = null;
    /** @var string */
    protected $strExpandedClass = null;
    /** @var string */
    protected $strHoveringClass = null;
    /** @var string */
    protected $strLeafClass = null;
    /** @var string */
    protected $strDisabledClass = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function MakeJqOptions() {
        $jqOptions = null;
        if (!is_null($val = $this->DisableParentChange)) {$jqOptions['disableParentChange'] = $val;}
        if (!is_null($val = $this->DoNotClear)) {$jqOptions['doNotClear'] = $val;}
        if (!is_null($val = $this->ExpandOnHover)) {$jqOptions['expandOnHover'] = $val;}
        if (!is_null($val = $this->IsTree)) {$jqOptions['isTree'] = $val;}
        if (!is_null($val = $this->ListType)) {$jqOptions['listType'] = $val;}
        if (!is_null($val = $this->MaxLevels)) {$jqOptions['maxLevels'] = $val;}
        if (!is_null($val = $this->ProtectRoot)) {$jqOptions['protectRoot'] = $val;}
        if (!is_null($val = $this->RootId)) {$jqOptions['rootID'] = $val;}
        if (!is_null($val = $this->RTL)) {$jqOptions['rtl'] = $val;}
        if (!is_null($val = $this->StartCollapsed)) {$jqOptions['startCollapsed'] = $val;}
        if (!is_null($val = $this->TabSize)) {$jqOptions['tabSize'] = $val;}
        if (!is_null($val = $this->ToleranceElement)) {$jqOptions['toleranceElement'] = $val;}
        if (!is_null($val = $this->BranchClass)) {$jqOptions['branchClass'] = $val;}
        if (!is_null($val = $this->CollapsedClass)) {$jqOptions['collapsedClass'] = $val;}
        if (!is_null($val = $this->DisableNestingClass)) {$jqOptions['disableNestingClass'] = $val;}
        if (!is_null($val = $this->ErrorClass)) {$jqOptions['errorClass'] = $val;}
        if (!is_null($val = $this->ExpandedClass)) {$jqOptions['expandedClass'] = $val;}
        if (!is_null($val = $this->HoveringClass)) {$jqOptions['hoveringClass'] = $val;}
        if (!is_null($val = $this->LeafClass)) {$jqOptions['leafClass'] = $val;}
        if (!is_null($val = $this->DisabledClass)) {$jqOptions['disabledClass'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction()
    {
        return 'nestedSortable';
    }

    /**
     * Fires when the item is dragged to a new location.
     * This triggers for each location it is dragged into not just the ending location.
     *
     *This method does not accept any arguments.
     */
    public function change()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "change", Application::PRIORITY_LOW);
    }
    /**
     * Fires when the item is dragged.
     *
     *This method does not accept any arguments.
     */
    public function sort()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "sort", Application::PRIORITY_LOW);
    }
    /**
     * Fires once the object has moved if the new location is invalid.
     *
     *This method does not accept any arguments.
     */
    public function revert()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "revert", Application::PRIORITY_LOW);
    }
    /**
     * Only fires once when the item is done bing moved at its final location.
     *
     *This method does not accept any arguments.
     */
    public function relocate()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "relocate", Application::PRIORITY_LOW);
    }
    /**
     * Gets the value currently associated with the specified optionName.
     * 
     * Note: For options that have objects as their value, you can get the
     * value of a specific key by using dot notation. For example, "foo.bar"
     * would get the value of the bar property on the foo option.
     * 
     * 	* optionName Type: String The name of the option to get.
     * @param $optionName
     */
    public function option($optionName)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, Application::PRIORITY_LOW);
    }
    /**
     * Gets an object containing key/value pairs representing the current
     * sortable options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", Application::PRIORITY_LOW);
    }
    /**
     * Sets the value of the sortable option associated with the specified
     * optionName.
     * 
     * Note: For options that have objects as their value, you can set the
     * value of just one property by using dot notation for optionName. For
     * example, "foo.bar" would update only the bar property of the foo
     * option.
     * 
     * 	* optionName Type: String The name of the option to set.
     * 	* value Type: Object A value to set for the option.
     * @param $optionName
     * @param $value
     */
    public function option2($optionName, $value)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, Application::PRIORITY_LOW);
    }
    /**
     * Sets one or more options for the sortable.
     * 
     * 	* options Type: Object A map of option-value pairs to set.
     * @param $options
     */
    public function option3($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, Application::PRIORITY_LOW);
    }


    public function __get($strName)
    {
        switch ($strName) {
            case 'DisableParentChange': return $this->blnDisableParentChange;
            case 'DoNotClear': return $this->blnDoNotClear;
            case 'ExpandOnHover': return $this->intExpandOnHover;
            case 'IsTree': return $this->blnIsTree;
            case 'ListType': return $this->strListType;
            case 'MaxLevels': return $this->intMaxLevels;
            case 'ProtectRoot ': return $this->blnProtectRoot;
            case 'RootId': return $this->intRootId;
            case 'RTL': return $this->blnRTL;
            case 'StartCollapsed': return $this->blnStartCollapsed;
            case 'TabSize': return $this->intTabSize;
            case 'ToleranceElement': return $this->strToleranceElement;

            case 'BranchClass': return $this->strBranchClass;
            case 'CollapsedClass': return $this->strCollapsedClass;
            case 'DisableNestingClass': return $this->strDisableNestingClass;
            case 'ErrorClass': return $this->strErrorClass;
            case 'ExpandedClass': return $this->strExpandedClass;
            case 'HoveringClass': return $this->strHoveringClass;
            case 'LeafClass': return $this->strLeafClass;
            case 'DisabledClass': return $this->strDisabledClass;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'DisableParentChange':
            try {
                $this->blnDisableParentChange = Type::Cast($mixValue, Type::BOOLEAN);
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disableParentChange', $this->blnDisableParentChange);
                break;
            } catch (InvalidCast $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }

            case 'DoNotClear':
                try {
                    $this->blnDoNotClear = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'doNotClear', $this->blnDoNotClear);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ExpandOnHover':
                try {
                    $this->intExpandOnHover = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'expandOnHover', $this->intExpandOnHover);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'IsTree':
                try {
                    $this->blnIsTree = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'isTree', $this->blnIsTree);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ListType':
                try {
                    $this->strListType = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'listType', $this->strListType);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'MaxLevels':
                try {
                    $this->intMaxLevels = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'maxLevels', $this->intMaxLevels);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ProtectRoot':
                try {
                    $this->blnProtectRoot = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'protectRoot', $this->blnProtectRoot);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'RootId':
                try {
                    $this->intRootId = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'rootID', $this->intRootId);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'RTL':
                try {
                    $this->blnRTL = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'rtl', $this->blnRTL);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'StartCollapsed':
                try {
                    $this->blnStartCollapsed = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'startCollapsed', $this->blnStartCollapsed);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'TabSize':
                try {
                    $this->intTabSize = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tabSize', $this->intTabSize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ToleranceElement':
                try {
                    $this->strToleranceElement = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'toleranceElement', $this->strToleranceElement);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }


            case 'BranchClass':
                try {
                    $this->strBranchClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'branchClass', $this->strBranchClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'CollapsedClass':
                try {
                    $this->strCollapsedClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'collapsedClass', $this->strCollapsedClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'DisableNestingClass':
                try {
                    $this->strDisableNestingClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disableNestingClass', $this->strDisableNestingClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ErrorClass':
                try {
                    $this->strErrorClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'errorClass', $this->strErrorClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ExpandedClass':
                try {
                    $this->strExpandedClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'expandedClass', $this->strExpandedClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'HoveringClass':
                try {
                    $this->strHoveringClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'hoveringClass', $this->strHoveringClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'LeafClass':
                try {
                    $this->strLeafClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'leafClass', $this->strLeafClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'DisabledClass':
                try {
                    $this->strDisabledClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disabledClass', $this->strDisabledClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
    * If this control is attachable to a codegenerated control in a ModelConnector, this function will be
    * used by the ModelConnector designer dialog to display a list of options for the control.
    * @return QModelConnectorParam[]
    **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array());
    }
}
