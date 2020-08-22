<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Control\FormBase;
use QCubed\Control\ControlBase;
use QCubed\Project\Application;
use QCubed\Type;
use QCubed\Js;
use QCubed\Html;


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
 * @property integer $Id
 * @property integer $ParentId
 * @property integer $Depth
 * @property integer $Left
 * @property integer $Right
 * @property string $MenuText
 * @property string $ContentType
 * @property integer $Status
 *
 * @property string $SectionClass
 *
 * @property-read array $ItemArray    List of ControlIds in sort orders.
 *
 * @link https://github.com/ilikenwf/nestedSortable
 * @package QCubed\Plugin
 */
class NestedSortableBase extends NestedSortableGen
{
    use Q\Control\DataBinderTrait;

    protected $blnIsBlockElement = false;

    protected $aryItemArray = null;

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
    /** @var  string ContentTypeObject */
    protected $strContentTypeObject;
    /** @var  int ContentType */
    protected $intContentType;
    /** @var  string RedirectUrl */
    protected $strRedirectUrl;
    /** @var  int IsRedirect */
    protected $intIsRedirect;
    /** @var  string SelectedPage */
    protected $strSelectedPage;
    /** @var  int Status */
    protected $intStatus;

    protected $blnMenuItemAppend = false;

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

    protected function registerFiles()
    {
        $this->addJavascriptFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/js/jquery.mjs.nestedSortable.js"); // updated https://github.com/tfddev/nestedSortable
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
    public function parsePostData(){}

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
     * menu_text - the menu_text for the node tag
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
        $strMenuText = '';
        if (isset($params['menu_text'])) {
            $strMenuText = $params['menu_text'];
        }
        $strContentTypeObject = '';
        if (isset($params['content_type_object'])) {
            $strContentTypeObject = $params['content_type_object'];
        }
        $intContentType = '';
        if (isset($params['content_type'])) {
            $intContentType = $params['content_type'];
        }
        $strRedirectUrl = '';
        if (isset($params['redirect_url'])) {
            $strRedirectUrl = $params['redirect_url'];
        }
        $intIsRedirect = '';
        if (isset($params['is_redirect'])) {
            $intIsRedirect = $params['is_redirect'];
        }
        $strSelectedPage = '';
        if (isset($params['selected_page'])) {
            $strSelectedPage = $params['selected_page'];
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
            'menu_text' => $strMenuText,
            'content_type_object' => $strContentTypeObject,
            'content_type' => $intContentType,
            'redirect_url' => $strRedirectUrl,
            'is_redirect' => $intIsRedirect,
            'selected_page' => $strSelectedPage,
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
     * Returns the HTML for the control.
     * @return string
     */
    protected function getControlHtml()
    {
        //$this->blnUseWrapper = false;
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

        $strHtml = '';
        $strHtml .= $this->welcomeMessage();

        //$strHtml .= _nl() . '<' . $this->TagName .  ' id="' . $this->ControlId . '"' . ' class="' . $this->CssClass .'"' . '>';

        //$strHtml .= $this->renderMenuTree($strParams, $strObjects);
        $strOut = $this->renderMenuTree($strParams, $strObjects);
        $strHtml .= $this->renderTag($this->TagName, null, null, $strOut);
        //$strHtml .= Html::renderTag($this->TagName, ['id' => $this->ControlId, 'class' => $this->CssClass], $strOut);
        $this->objDataSource = null;

        //$strHtml .= _nl() . '</' . $this->TagName . '>';

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
     * @param $arrParams
     * @param $arrObjects
     * @return string
     */
    protected function renderMenuTree($arrParams, $arrObjects)
    {
        $strHtml = '';
        //$strHtml .= _nl() . '<' . $this->TagName .  ' id="' . $this->ControlId . '"' . ' class="' . $this->CssClass .'"' . '>';

        for ($i = 0; $i < count($arrParams); $i++)
        {
            $this->intId = $arrParams[$i]['id'];
            $this->intParentId = $arrParams[$i]['parent_id'];
            $this->intDepth = $arrParams[$i]['depth'];
            $this->intLeft = $arrParams[$i]['left'];
            $this->intRight = $arrParams[$i]['right'];
            $this->strMenuText = $arrParams[$i]['menu_text'];
            $this->strContentTypeObject = $arrParams[$i]['content_type_object'];
            $this->intContentType = $arrParams[$i]['content_type'];
            $this->strRedirectUrl = $arrParams[$i]['redirect_url'];
            $this->intIsRedirect = $arrParams[$i]['is_redirect'];
            $this->strSelectedPage = $arrParams[$i]['selected_page'];
            $this->intStatus = $arrParams[$i]['status'];

            if ($this->cellParamsCallback) {
                $this->strRenderCellHtml = $this->getRenderCellHtml($arrObjects[$i]);
            }

            if ($this->intDepth == $this->intCurrentDepth) {
                if ($this->intCounter > 0)
                    $strHtml .= '</li>';
            } elseif ($this->intDepth > $this->intCurrentDepth) {
                $strHtml .= _nl() . '<' . $this->TagName . '>';
                $this->intCurrentDepth = $this->intCurrentDepth + ($this->intDepth - $this->intCurrentDepth);
            } elseif ($this->intDepth < $this->intCurrentDepth) {
                $strHtml .= str_repeat('</li>' . '</' . $this->TagName . '>', $this->intCurrentDepth - $this->intDepth) . '</li>';
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
            $strDisplayedType = $this->strContentTypeObject ? ' Type: ' . $this->strContentTypeObject:' Type: ' . 'NULL';
            $strRoutingInfo = $this->intContentType == 8 ? ' - ' . ' <span style="color: #2980b9;">' . $this->strRedirectUrl . '</span>'  : '';
            if ($this->intContentType == 7 && $this->intIsRedirect == 1) {
                $strDoubleRoutingInfo = ' - ' . '<span style="color: #ff0000;">' . '(' .  '<strong>' . $this->strSelectedPage . '</strong>' . ' | ' . t('Warning, double redirection: ') . '<span style="color:#2980b9">' . $this->strRedirectUrl . '</span>' . ')' . '</span>';

            } elseif ($this->intContentType == 5) {
                $strDoubleRoutingInfo = ' - ' . $this->strSelectedPage;
            } else {
                $strDoubleRoutingInfo = $this->strSelectedPage;
            }

            $strHtml .= <<<TMPL

    <div class="menu-row {$strCheckStatus}">
        <span class="reorder"><i class="fa fa-bars"></i></span>
        <span class="disclose"><span></span></span>
        <section class="menu-body">{$this->strMenuText}<span class="separator">&nbsp;</span>{$strDisplayedType}{$strRoutingInfo} {$strDoubleRoutingInfo}</section>
TMPL;
            if ($this->cellParamsCallback) {
                $strHtml .= $this->strRenderCellHtml;
            }
            $strHtml .= <<<TMPL
    </div>
TMPL;
            ++$this->intCounter;
        }
        $strHtml .= str_repeat('</li>' . '</' . $this->TagName . '>', $this->intDepth) . '</li>';

        if ($this->blnMenuItemAppend == true)
        {
            $this->getMenuItemAppend();
        }
        //$strHtml .= _nl() . '</' . $this->TagName . '>';
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
            $strHtml = _nl() . Html::renderTag('section', $attributes, $strHtml);
            return $strHtml;
        } else {
            return null;
        }
    }

    protected function getMenuItemAppend()
    {
        $strHtml = _nl() . '<li id="' . $this->ControlId . '_' . $this->intId . '"';
        $strHtml .= ' class="mjs-nestedSortable-leaf"' . '>';
        $strHtml .= <<<TMPL

    <div class="menu-row disabled">
        <span class="reorder"><i class="fa fa-bars"></i></span>
        <span class="disclose"><span></span></span>
        <section class="menu-body">{$this->strMenuText}</section>
TMPL;
        if ($this->cellParamsCallback) {
            $strHtml .= $this->strRenderCellHtml;
        }
        $strHtml .= '</li>';
        return $strHtml;
    }

    public function refresh()
    {
        parent::refresh();
        ControlBase::refresh();
    }

    public function getEndScript()
    {
        Application::executeSelectorFunction(".disclose", "on", "click",
            new Js\Closure("jQuery(this).closest('li').toggleClass('mjs-nestedSortable-expanded').toggleClass('mjs-nestedSortable-collapsed')"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("[data-collapse='true']", "on", "click",
            new Js\Closure("jQuery('#{$this->ControlId}').find('li.mjs-nestedSortable-expanded').removeClass('mjs-nestedSortable-expanded').addClass('mjs-nestedSortable-collapsed')"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("[data-collapse='false']", "on", "click",
            new Js\Closure("jQuery('#{$this->ControlId}').find('li.mjs-nestedSortable-collapsed').removeClass('mjs-nestedSortable-collapsed').addClass('mjs-nestedSortable-expanded')"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("body", "on", "click", "[data-buttons='true']",
            new Js\Closure("jQuery(\"[data-status='change'], [data-edit='true'], [data-delete='true']\").prop('disabled', true);"),
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction("body", "on", "click", "[data-buttons='false']",
            new Js\Closure("jQuery(\"[data-status='change'], [data-edit='true'], [data-delete='true']\").prop('disabled', false);"),
            Application::PRIORITY_HIGH);

        /**
         * The nestedsortable functions here do not support locking the first menu item.
         * Or are they unsupported or not working well?
         * Simple locking is added here.
         * But it can be hidden or removed if you need to.
         */
        Application::executeJavaScript(sprintf("jQuery('#{$this->ControlId}_1').addClass('disabled')"));

        //Application::executeJavaScript(sprintf("jQuery('#btnDelete9').closest('li').css('border', '1px solid red')"));

        $strJS = parent::getEndScript();

        $strCtrlJs = <<<FUNC
jQuery('#{$this->ControlId}').on("sortstop", function (event, ui) {
    var arr = jQuery(this).nestedSortable("toArray", {startDepthCount: 0});
    var str = JSON.stringify(arr);
    console.log(str);
    qcubed.recordControlModification("$this->ControlId", "_ItemArray", str);
})
FUNC;
        Application::executeJavaScript($strCtrlJs, Application::PRIORITY_HIGH);

        return $strJS;
    }

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
            case "ContentTypeObject":
                try {
                    $this->blnModified = true;
                    $this->strContentTypeObject = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "ContentType":
                try {
                    $this->blnModified = true;
                    $this->intContentType = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "RedirectUrl":
                try {
                    $this->blnModified = true;
                    $this->strRedirectUrl = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "IsRedirect":
                try {
                    $this->blnModified = true;
                    $this->intIsRedirect = Type::Cast($mixValue, Type::INTEGER);
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
                $this->blnModified = true;
                $this->objDataSource = $mixValue;
                break;
            case "MenuItemAppend":
                try {
                    $this->blnModified = true;
                    $this->blnMenuItemAppend = Type::Cast($mixValue, Type::BOOLEAN);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;

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
            case 'ItemArray': return $this->aryItemArray;
            case "Id": return $this->intId;
            case "ParentId": return $this->intParentId;
            case "Depth": return $this->intDepth;
            case "Left": return $this->intLeft;
            case "Right": return $this->intRight;
            case "MenuText": return $this->strMenuText;
            case "ContentTypeObject": return $this->strContentTypeObject;
            case "ContentType": return $this->intContentType;
            case "RedirectUrl": return $this->strRedirectUrl;
            case "IsRedirect": return $this->intIsRedirect;
            case "Status": return $this->intStatus;
            case "SectionClass": return $this->strSectionClass;
            case "DataSource": return $this->objDataSource;
            case "MenuItemAppend": return $this->blnMenuItemAppend;

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