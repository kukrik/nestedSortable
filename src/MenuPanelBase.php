<?php

/**
 * This file contains the MenuPanel Class.
 */

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Control\BlockControl;
use QCubed\Type;
use QCubed\QString;
use QCubed\Project\Application;
use QCubed\Project\Control;
use QCubed\Html;
use QCubed\Bootstrap as Bs;
//use QCubed\Plugin\NestedSortable;

use DataBinderTrait;

// we need a better way of reconfiguring JS and CSS assets
if (!defined('QCUBED_NESTEDSORTABLE_ASSETS_URL')) {
    define('QCUBED_NESTEDSORTABLE_ASSETS_URL', dirname(QCUBED_BASE_URL) . '/kukrik/nestedsortable/assets');
}

/**
 * Class MenuPanel: ..................
 *
 * @property integer $Id
 * @property integer $ParentId
 * @property integer $Depth
 * @property integer $Left
 * @property Integer $Right
 *
 * @package QCubed\Plugin
 */

class MenuPanelBase extends BlockControl
{


    protected $blnUseWrapper = false; //If you do not have it turned on globally, then turn on locally.
    //protected $strTagName = 'ul';
    //protected $strIndicateClass = 'mjs-nestedSortable-leaf';

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

    //protected $objMenuArrays;


    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->registerFiles();
    }

    protected function registerFiles()
    {
        //$this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        //$this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
        //$this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/style.css");
        Bs\Bootstrap::loadJS($this);
    }

    public static function createFaHtml($strIcon, $strDescription = null)
    {
        $strToReturn = sprintf('<i class="fa %s"></i>', $strIcon);
        if ($strDescription) {
            $strToReturn .= sprintf(' %s', $strDescription);
        }
        return $strToReturn;
    }



    /**
     * Returns the HTML formatted string for the control
     * @return string HTML string
     */
    protected function getControlHtml()
    {

        /*$strHtml = '';
        if ($this->hasDataBinder()) {
            $this->callDataBinder();
        }*/

        $strText = Q\QString::htmlEntities($this->Text);

        $strInnerHtml = <<<TMPL

<div class="menu-row enabled">
    <span class="reorder"><i class="fa fa-reorder"></i></span>
    <span class="disclose"><span></span></span>
    <section class="menu-body">{$strText}</section>
    <section class="menu-btn-body center-button">
        <button title="Disable" class="btn btn-white btn-xs" data-toggle="tooltip" data-value="103" >Disable</button>
        <button title="Edit" class="btn btn-primary btn-xs" data-toggle="tooltip" value="103" >
            <i class="fa fa-pencil"></i>
        </button>
        <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" data-value="103">
            <i class="fa fa-trash-o"></i>
        </button>
    </section>
</div>

TMPL;
        if (!is_null($this->ParentId)) {
            $strHtml =  $this->renderTag('li', ['id' => $this->strControlId . '_' . $this->intId], null, $strInnerHtml);
            $strHtml = Html::renderTag($this->strTagName, null, $strHtml);
            return $strHtml;
        } else /*if (is_null($this->ParentId))*/ {
            $strHtml = $this->renderTag('li', ['id' => $this->strControlId . '_' . $this->intId], null, $strInnerHtml);
            //$strHtml = $this->renderTag('ul', null, $strHtml);
            return $strHtml;
        /*} else {
            $strHtml = $this->renderTag('li', ['id' => $this->strControlId . '_' . $this->intId], null, $strInnerHtml);
            //$strHtml .= Html::renderTag('ul', null, $strHtml);
            return $strHtml;*/

        }

        //return $strHtml;

}


    public function getControlJavaScript()
    {

        $strJS = sprintf('$j( document ).on( "click", function( event ) {
  $j( event.target ).closest( "li" ).css("background-color", "red");
});',
            //$this->getJqControlId(),
            //$this->intId,
            $this->getJqControlId(),
            $this->intId);


        /*$strJS = sprintf('$j("#%s_%s.disclose").on("click", function() {
            $j(this).closest("li").toggleClass("mjs-nestedSortable-expanded").toggleClass("mjs-nestedSortable-collapsed");
        })',
            $this->getJqControlId(),
            $this->intId,
            $this->getJqControlId(),
            $this->intId);*/
        return $strJS;
    }

    /*public function getEndScript()
    {
        return  $this->getControlJavaScript() . '; ' . parent::getEndScript();
    }*/



        /*if(empty($this->ParentId)) {
            if (in_array($this->Id, $data)) {
                return $this->renderTag('li', ['id'=>$this->strControlId . '_' . $this->intId, 'class' => 'mjs-nestedSortable-expanded'], null, $strInnerHtml);
            } else
                return $this->renderTag('li', ['id'=>$this->strControlId . '_' . $this->intId, 'class' => 'mjs-nestedSortable-leaf'], null, $strInnerHtml);
        } elseif ($this->ParentId) {
            if (in_array($this->Id, $data)) {
                $strInnerHtml = $this->renderTag('li', ['id' => $this->strControlId . '_' . $this->intId, 'class' => 'mjs-nestedSortable-expanded'], null, $strInnerHtml);
                return Html::renderTag($this->TagName, null, null, $strInnerHtml);
            } else {
                $strInnerHtml = $this->renderTag('li', ['id' => $this->strControlId . '_' . $this->intId, 'class' => 'mjs-nestedSortable-leaf'], null, $strInnerHtml);
                return Html::renderTag($this->TagName, null, null, $strInnerHtml);
            }
        }*/

    //} $j("#%s").



    /*public function getEndScript()
    {
        $strJS = parent::getEndScript();

        $strCtrlJs = <<<FUNC
			;\$j('(".disclose").on("click", function() {
            $j(this).closest("li").toggleClass("mjs-nestedSortable-expanded").toggleClass("mjs-nestedSortable-collapsed");
        })
FUNC;
        Application::executeJavaScript($strCtrlJs, Application::PRIORITY_HIGH);

        return $strJS;
    }*/


    //public function renderScript(ControlBase $objControl)
    //{
        /*if ($this->blnDisplay === true) {
            $strShowOrHide = 'show';
        } else {
            if ($this->blnDisplay === false) {
                $strShowOrHide = 'hide';
            } else {
                $strShowOrHide = '';
            }
        }*/

        //return sprintf('$j("#%s").(".disclose").on("click", function() {
            //$j(this).closest("li").toggleClass("mjs-nestedSortable-expanded").toggleClass("mjs-nestedSortable-collapsed");
        //})',
            //$objControl->ControlId);
    //}



    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    public function __get($strName) {
        switch ($strName) {
            case "Id": return $this->intId;
            case "ParentId": return $this->intParentId;
            case "Depth": return $this->intDepth;
            case "Left": return $this->intLeft;
            case "Right": return $this->intRight;

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
	public function __set($strName, $mixValue) {
		switch ($strName) {

            case "Id":
                try {
                    //$this->blnModified = true;
                    $this->intId = Type::Cast($mixValue, Type::INTEGER);
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
