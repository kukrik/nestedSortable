<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Type;
use QCubed\Js;

// we need a better way of reconfiguring JS assets
if (!defined('QCUBED_NESTEDSORTABLE_ASSETS_URL')) {
    define('QCUBED_NESTEDSORTABLE_ASSETS_URL', dirname(QCUBED_BASE_URL) . '/kukrik/nestedsortable/assets');
}

/**
 * Class NestedSortableBase
 *
 * If want to will be overwritten when you update QCubed. To override, make your changes
 * to the NestedSortable.class.php file instead.
 *
 * NestedSortable is a group of panels that can be dragged to reorder them. You will need to put
 * some care into the css styling of the objects so that the css allows them to be moved. It
 * will use the top level html objects inside the panel to decide what to sort. Make sure
 * they have ids so it can return the ids of the items in sort order.
 *
 * @property-read array $ItemArray    List of ControlIds in sort orders.
 *
 * @link https://github.com/ilikenwf/nestedSortable
 * @package QCubed\Plugin
 */
class NestedSortableBase extends NestedSortableGen
{
    protected $aryItemArray = null;

    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->registerFiles();
    }

    protected function registerFiles()
    {
        $this->addJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/jquery.mjs.nestedSortable.js");
    }

    // Find out what the sort order is at the beginning so that aryItemArray is up to date
    public function makeJqOptions()
    {
        $jqOptions = parent::makeJqOptions();

        // Put this in the qcubed.js file, or something like it.
        $jqOptions['create'] = new Q\Js\Closure('
                        var arr = jQuery(this).nestedSortable("toArray", {startDepthCount: 0});
                        var str = JSON.stringify(arr);
                        console.log(str);
                        qcubed.recordControlModification("$this->ControlId", "_ItemArray", str);
         ');
        return $jqOptions;
    }

    public function getEndScript()
    {
        $strJS = Q\Control\BlockControl::getEndScript();

        $strCtrlJs = <<<FUNC
                    ;\$j('#{$this->ControlId}').on("sortstop", function (event, ui) {
                        var arr = jQuery(this).nestedSortable("toArray", {startDepthCount: 0});
                        var str = JSON.stringify(arr);
                        console.log(str);
                        qcubed.recordControlModification("$this->ControlId", "_ItemArray", str);
        })
FUNC;
        Application::executeJavaScript($strCtrlJs, Application::PRIORITY_HIGH);

        return $strJS;
    }

    /**
     * Generated method overrides the built-in Control method, causing it to not redraw completely. We restore
     * its functionality here.
     */

    /*public function refresh()
    {
        parent::refresh();
        ControlBase::refresh();
    }*/

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case '_ItemArray': // Internal only. Do not use. Used by JS above to track selections.
                try {
                    $data = Type::cast($mixValue, Type::STRING);
                    $this->aryItemArray = $data;
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

    public function __get($strName)
    {
        switch ($strName) {
            case 'ItemArray':
                return $this->aryItemArray;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
}
