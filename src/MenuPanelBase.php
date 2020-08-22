<?php

/** This file contains the MenuPanel Class */

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Control\Panel;
use QCubed\Project\Control;
use QCubed\Project\Application;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\Js;
use QCubed\Type;

// we need a better way of reconfiguring JS and CSS assets
/*if (!defined('QCUBED_NESTEDSORTABLE_ASSETS_URL')) {
    define('QCUBED_NESTEDSORTABLE_ASSETS_URL', dirname(QCUBED_BASE_URL) . '/kukrik/nestedsortable/assets');
}*/

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
class MenuPanelBase extends Panel
{
    use Q\Control\DataBinderTrait;

    /** @var bool UseWrapper */
    protected $blnUseWrapper = false; //If it's not turned off globally, please do it here!

    protected $blnIsBlockElement = true;
    /** @var string TagName */
    protected $strSubTagName = null;
    /** @var string SectionClass */
    protected $strSectionClass = null;


    /** @var  callable */
    protected $nodeParamsCallback = null;
    /** @var  callable */
    protected $cellParamsCallback = null;

    /** @var */
    protected $mixButtons;

    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;

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

    /** @throws Caller */
    protected function registerFiles()
    {
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        $this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/style.css");
        Bs\Bootstrap::loadJS($this);
    }

    /**
     * @return bool
     */
    public function validate() {return true;}

    /**
     *
     */
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

    /** @param callable $callback */
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

    /**
     * @param $objItem
     * @return mixed
     * @throws \Exception
     */
    public function getObjectDraw($objItem)
    {
        if (!$this->cellParamsCallback) {
            throw new \Exception("Must provide an cellParamsCallback");
        }
        $this->mixButtons = call_user_func($this->cellParamsCallback, $objItem);

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
     * @param $arrObjects
     * @return string
     */
    protected function renderMenuTree($arrParams, $arrObjects)
    {
        $strHtml = '';

        for ($i = 0; $i < count($arrParams); $i++)
        {
            $this->intId = $arrParams[$i]['id'];
            $this->intParentId = $arrParams[$i]['parent_id'];
            $this->intDepth = $arrParams[$i]['depth'];
            $this->intLeft = $arrParams[$i]['left'];
            $this->intRight = $arrParams[$i]['right'];
            $this->strMenuText = $arrParams[$i]['text'];
            $this->intStatus = $arrParams[$i]['status'];

            if ($this->cellParamsCallback) {
                $this->strRenderCellHtml = $this->getRenderCellHtml($arrObjects[$i]);
            }

            if ($this->intDepth == $this->intCurrentDepth) {
                if ($this->intCounter > 0)
                    $strHtml .= '</li>';
            } elseif ($this->intDepth > $this->intCurrentDepth) {
                $strHtml .= '<' . $this->strSubTagName . '>';
                $this->intCurrentDepth = $this->intCurrentDepth + ($this->intDepth - $this->intCurrentDepth);
            } elseif ($this->intDepth < $this->intCurrentDepth) {
                $strHtml .= str_repeat('</li>' . '</' . $this->strSubTagName . '>', $this->intCurrentDepth - $this->intDepth) . '</li>';
                $this->intCurrentDepth = $this->intCurrentDepth - ($this->intCurrentDepth - $this->intDepth);
            }
            $strHtml .= _nl() . '<li id="' . $this->ControlId . '_' . $this->intId . '"';
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
TMPL;
            if ($this->cellParamsCallback) {
                $strHtml .= $this->strRenderCellHtml;
            }
            $strHtml .= <<<TMPL
    </div>
TMPL;
            ++$this->intCounter;
        }
        $strHtml .= str_repeat('</li>' . '</' . $this->strSubTagName . '>', $this->intDepth) . '</li>';
        return $strHtml;
    }

    /**
     * @param $value
     * @return null|string
     */
    protected function getRenderCellHtml($value)
    {
        if ($this->cellParamsCallback) {
            $strHtml = '';
            $attributes = [];

            if ($this->strSectionClass) {
                $attributes['class'] = $this->strSectionClass;
            }
            $strHtml .= $value;
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
        parent::getControlHtml();

        $this->dataBind();

        $strParams = [];
        $strObjects = [];

        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                $strParams[] = $this->getItemRaw($objObject);
                if ($this->cellParamsCallback) {
                    $strObjects[] = $this->getObjectDraw($objObject);
                }
            }
        }

        $strHtml = $this->welcomeMessage();


        $strHtml .= $this->renderMenuTree($strParams, $strObjects);

        //$strOut = $this->renderMenuTree($strParams, $strObjects);
        //$strHtml .= $this->renderTag($this->TagName, null, null, $strOut);


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

    /**
     * This is just a welcome message!
     * At the same time, the first menu item has been created!
     * Only the menu item title can be edited here.
     *
     * @return string
     */
    public function welcomeMessage()
    {
        if (count($this->objDataSource) == 1) {
            $strEmptyMenuText = sprintf(t('<strong>Welcome!</strong> Create the following menu items!'));
            return "<div class='alert alert-info alert-dismissible' role='alert' style='display: block;'>
                    $strEmptyMenuText
                    </div>";
        }
    }

    /**
     *
     */
    public function makeJqWidget()
    {
        Application::executeSelectorFunction(".disclose", "on", "click",
            new Js\Closure("jQuery(this).closest('li').toggleClass('mjs-nestedSortable-expanded').toggleClass('mjs-nestedSortable-collapsed')"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("[data-collapse='true']", "on", "click",
            new Js\Closure("jQuery('.sortable').find('li.mjs-nestedSortable-expanded').removeClass('mjs-nestedSortable-expanded').addClass('mjs-nestedSortable-collapsed')"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("[data-collapse='false']", "on", "click",
            new Js\Closure("jQuery('.sortable').find('li.mjs-nestedSortable-collapsed').removeClass('mjs-nestedSortable-collapsed').addClass('mjs-nestedSortable-expanded')"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("body", "on", "click", "[data-buttons='true']",
            new Js\Closure("jQuery(\"[data-status='change'], [data-edit='true'], [data-delete='true']\").prop('disabled', true);"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("body", "on", "click", "[data-buttons='false']",
        new Js\Closure("jQuery(\"[data-status='change'], [data-edit='true'], [data-delete='true']\").prop('disabled', false);"),
        Application::PRIORITY_HIGH);

        //Application::executeControlCommand($this->getJqControlId(), "nestedSortable", "refresh", Application::PRIORITY_LOW);

        /**
         * The nestedsortable functions here do not support locking the first menu item.
         * Or are they unsupported or not working well?
         * Simple locking is added here.
         * But it can be hidden or removed if you need to.
         */
        Application::executeJavaScript(sprintf("jQuery('#{$this->ControlId}_1').addClass('disabled');"));

        //Application::executeJavaScript(sprintf("jQuery('.sortable').remove()"));
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
            case "SubTagName":
                return $this->strSubTagName;
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
            case "SubTagName":
                try {
                    $this->blnModified = true;
                    $this->strSubTagName = Type::Cast($mixValue, Type::STRING);
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