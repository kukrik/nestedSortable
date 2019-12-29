<?php

/**
 * This file contains the MenuPanel Class.
 */

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control;
use QCubed\Project\Application;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\Js;
use QCubed\Type;


// we need a better way of reconfiguring JS and CSS assets
if (!defined('QCUBED_NESTEDSORTABLE_ASSETS_URL')) {
    define('QCUBED_NESTEDSORTABLE_ASSETS_URL', dirname(QCUBED_BASE_URL) . '/kukrik/nestedsortable/assets');
}

/**
 * Class MenuPanelBase
 * @property integer $Id
 * @property integer $ParentId
 * @property integer $Depth
 * @property integer $Left
 * @property integer $Right
 * @property string $MenuText
 * @property integer $Status
 *
 * // Unfinished work!!!
 *
 * @package QCubed\Plugin
 */
class MenuPanelBase extends ControlBase
{
    use Q\Control\DataBinderTrait;

    /** @var bool UseWrapper */
    protected $blnUseWrapper = false; //If you do not have it turned on globally, then turn on locally.
    /** @var string TagName */
    protected $strTagName = null;
    /** @var string SectionClass */
    protected $strSectionClass = null;


    /** @var  callable */
    protected $nodeParamsCallback = null;
    /** @var  callable */
    protected $cellParamsCallback = null;

    protected $mixButtons;

    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;
    /** @var  integer Used during rendering to report which visible menu item is being drawn. */
    protected $intCurrentRowIndex;

    protected $intCurrentDepth = 0;
    protected $intCounter = 0;

    /** @var null */
    protected $strRenderCellHtml = null;

    /** @var  integer Id */
    protected $intId = null;
    /** @var  integer ParentId */
    protected $intParentId = null;
    /** @var  integer Depth */
    protected $intDepth = null;
    /** @var  integer Left */
    protected $intLeft = null;
    /** @var  integer Right */
    protected $intRight = null;
    /** @var  string MenuText */
    protected $strMenuText;
    /** @var  int Status */
    protected $intStatus;


    /**
     * MenuPanelBase constructor.
     * @param Q\Control\ControlBase|FormBase $objParentObject
     * @param null $strControlId
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller  $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
        $this->registerFiles();
    }

    /**
     * @throws Caller
     */
    protected function registerFiles()
    {
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        $this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/style.css");
        Bs\Bootstrap::loadJS($this);
    }

    public function validate() {return true;}

    public function parsePostData() {}

    /**
     * Set the node params callback. The callback should be of the form:
     * func($objItem)
     * The callback will be give the raw node from the data source, and the item's index.
     * The function should return a key/value array with the following possible items:
     * id - the id for the node tag
     * parent_id - the parent_id for the node tag
     * depth - the depth for the node tag
     * left - the left for the node tag
     * right - the right for the node tag
     * text - the text for the node tag
     * status - the status for the node tag
     *
     * The callback is a callable, so can be of the form [$objControl, "func"]
     *
     * @param callable $callback
     */
    public function createNodeParams(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
    }

    /**
     * @param callable $callback
     */
    public function createRenderButtons(callable $callback)
    {
        $this->cellParamsCallback = $callback;
    }

    /**
     * Uses HTML callback to get each loop in the original array. Relies on the NodeParamsCallback
     * to return information on how to draw each node.
     *
     * @param mixed $objItem
     * @return string
     * @throws \Exception
     */
    public function getItemRaw($objItem)
    {
        if (!$this->nodeParamsCallback) {
            throw new \Exception("Must provide an nodeParamsCallback");
        }
        $params = call_user_func($this->nodeParamsCallback, $objItem);

        $intId = '';
        if (isset($params['id'])) {
            $intId = $params['id'];
        }
        $intParentId = '';
        if (isset($params['parent_id'])) {
            $intParentId = $params['parent_id'];
        }
        $intDepth = '';
        if (isset($params['depth'])) {
            $intDepth = $params['depth'];
        }
        $intLeft = '';
        if (isset($params['left'])) {
            $intLeft = $params['left'];
        }
        $intRight = '';
        if (isset($params['right'])) {
            $intRight = $params['right'];
        }
        $strText = '';
        if (isset($params['text'])) {
            $strText = $params['text'];
        }
        $intStatus = '';
        if (isset($params['status'])) {
            $intStatus = $params['status'];
        }

        $vars = [
            'id' => $intId,
            'parent_id' => $intParentId,
            'depth' => $intDepth,
            'left' => $intLeft,
            'right' => $intRight,
            'text' => $strText,
            'status' => $intStatus
            ];

        return $vars;
    }

    public function getObjectDraw($objItem)
    {
        if (!$this->cellParamsCallback) {
            throw new \Exception("Must provide an cellParamsCallback");
        }
        $this->mixButtons = call_user_func($this->cellParamsCallback, $objItem);
        //print '<pre>';
        //print_r($this->mixButtons . "***");
        //print '</pre>';
        return $this->mixButtons;
    }

    /**
     * Fix up possible embedded reference to the form.
     */
    public function sleep()
    {
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->nodeParamsCallback);
        $this->cellParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->cellParamsCallback);
        parent::sleep();
    }

    /**
     * The object has been unserialized, so fix up pointers to embedded objects.
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
        $this->cellParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->cellParamsCallback);
    }

    /**
     * @param $arrParams
     * @return string
     */
    protected function renderMenuTree($arrParams)
    {
        $strHtml = '';
        foreach ($arrParams as $arrParam) {

            if ($this->nodeParamsCallback) {
                $this->intId = $arrParam['id'];
                $this->intParentId = $arrParam['parent_id'];
                $this->intDepth = $arrParam['depth'];
                $this->intLeft = $arrParam['left'];
                $this->intRight = $arrParam['right'];
                $this->strMenuText = $arrParam['text'];
                $this->intStatus = $arrParam['status'];
            }
            $strRenderCellHtml = $this->getRenderCellHtml($arrParam);

            if ($this->intDepth == $this->intCurrentDepth) {
                if ($this->intCounter > 0)
                    $strHtml .= '</li>';
            } elseif ($this->intDepth > $this->intCurrentDepth) {
                $strHtml .= '<' . $this->strTagName . '>';
                $this->intCurrentDepth = $this->intCurrentDepth + ($this->intDepth - $this->intCurrentDepth);
            } elseif ($this->intDepth < $this->intCurrentDepth) {
                $strHtml .= str_repeat('</li>' . '</' . $this->strTagName . '>', $this->intCurrentDepth - $this->intDepth) . '</li>';
                $this->intCurrentDepth = $this->intCurrentDepth - ($this->intCurrentDepth - $this->intDepth);
            }
            $strHtml .= _nl() . '<li id="' . $this->strControlId . '_' . $this->intId . '"';
            if ($this->intLeft + 1 == $this->intRight) {
                $strHtml .= ' class="mjs-nestedSortable-leaf"';
            } else {
                $strHtml .= ' class="mjs-nestedSortable-expanded"';
            }
            $strHtml .= '>';

            $strCheckStatus = $this->intStatus == 1 ? 'enabled' : 'disabled';
            $strHtml .= <<<TMPL

    <div class="menu-row $strCheckStatus">
        <span class="reorder"><i class="fa fa-bars"></i></span>
        <span class="disclose"><span></span></span>
        <section class="menu-body">{$this->strMenuText}</section>
        $strRenderCellHtml
    </div>

TMPL;
            ++$this->intCounter;
        }
        $strHtml .= str_repeat('</li>' . '</' . $this->strTagName . '>', $this->intDepth) . '</li>';

        return $strHtml;
    }

    protected function getRenderCellHtml($values)
    {
        if ($this->cellParamsCallback) {
        $strHtml = '';
        $attributes = [];

        if ($this->strSectionClass) {
            $attributes['class'] = $this->strSectionClass;
        }
            $strHtml .= $values;
            $strHtml = Html::renderTag('section', $attributes, $strHtml);
            return $strHtml;
        } else {
            return null;
        }
    }

    /**
     * Returns the HTML for the control.
     * @return string
     */
    protected function getControlHtml()
    {
        $this->dataBind();

        if (empty($this->objDataSource)) {
            $this->objDataSource = null;
            $strEmptyMenuText = sprintf(t('<strong>Empty menu!</strong> Create the first menu item!'));
            return "<li><div class='alert alert-info alert-dismissible' role='alert' style='display: block;'>
    $strEmptyMenuText
</div></li>";
        }

        $strRows = [];
        $this->intCurrentRowIndex = 0;
        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                if ($this->nodeParamsCallback) {
                    $strRows[] = $this->getItemRaw($objObject);
                }
                if ($this->cellParamsCallback) {
                    $strRows[] = $this->getObjectDraw($objObject);
                }
                $this->intCurrentRowIndex++;
            }
        }
        $strHtml = $this->renderMenuTree($strRows);
        $this->objDataSource = null;
        return $strHtml;
    }

    /**
     * @throws Caller
     */
    public function dataBind()
    {
        // Run the DataBinder (if applicable)
        if (($this->objDataSource === null) && ($this->hasDataBinder()) && (!$this->blnRendered)) {
            try {
                $this->callDataBinder();
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }
    }

    public function makeJqWidget()
    {
        Application::executeSelectorFunction(".disclose" , "on", "click",
            new Js\Closure("jQuery(this).closest(\"li\").toggleClass(\"mjs-nestedSortable-expanded\").toggleClass(\"mjs-nestedSortable-collapsed\")"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction(".js-collapse-all" , "on", "click",
            new Js\Closure("jQuery(\"ul.sortable\").find(\"li.mjs-nestedSortable-expanded\").removeClass(\"mjs-nestedSortable-expanded\").addClass(\"mjs-nestedSortable-collapsed\")"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction(".js-expand-all" , "on", "click",
            new Js\Closure("jQuery(\"ul.sortable\").find(\"li.mjs-nestedSortable-collapsed\").removeClass(\"mjs-nestedSortable-collapsed\").addClass(\"mjs-nestedSortable-expanded\")"),
            Application::PRIORITY_HIGH);
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////

    public function __get($strName)
    {
        switch ($strName) {
            case "Id":
                return $this->intId;
            case "ParentId":
                return $this->intParentId;
            case "Depth":
                return $this->intDepth;
            case "Left":
                return $this->intLeft;
            case "Right":
                return $this->intRight;
            case "MenuText":
                return $this->strMenuText;
            case "Status":
                return $this->intStatus;
            case "TagName":
                return $this->strTagName;
            case "SectionClass":
                return $this->strSectionClass;
            case "DataSource":
                return $this->objDataSource;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

    /////////////////////////
    // Public Properties: SET
    /////////////////////////

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "Id":
                try {
                    $this->blnModified = true;
                    $this->intId = Type::Cast($mixValue, Type::INTEGER);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "ParentId":
                try {
                    $this->blnModified = true;
                    $this->intParentId = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Depth":
                try {
                    $this->blnModified = true;
                    $this->intDepth = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Left":
                try {
                    $this->blnModified = true;
                    $this->intLeft = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Right":
                try {
                    $this->blnModified = true;
                    $this->intRight = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "MenuText":
                try {
                    $this->blnModified = true;
                    $this->strMenuText = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Status":
                try {
                    $this->blnModified = true;
                    $this->intStatus = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "TagName":
                try {
                    $this->blnModified = true;
                    $this->strTagName = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "SectionClass":
                try {
                    $this->blnModified = true;
                    $this->strSectionClass = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "DataSource":
                $this->objDataSource = $mixValue;
                $this->blnModified = true;
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

}