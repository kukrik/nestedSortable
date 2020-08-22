<?php

// https://stackoverflow.com/questions/18999501/bootstrap-3-keep-selected-tab-on-page-refresh

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Control\Panel;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;

class PageEditPanel extends Panel
{
    public $lblMessage;

    public $lblTitle;
    public $lblMenuTitle;

    public $lblContentType;
    public $lstContentTypes;

    public $lblMenuText;
    public $txtMenuText;

    public $btnSave;
    public $btnSaving;
    public $btnCancel;

    protected $intId;
    protected $objMenuContent;
    protected $strMethodCallBack;

    protected $strTemplate = 'PageEditPanel.tpl.php';

    public function __construct($objParentObject, /*$strMethodCallBack,*/ $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenuContent = MenuContent::load($this->intId);

        $this->lblMessage = new Q\Plugin\Alert($this);
        $this->lblMessage->Display = false;
        $this->lblMessage->FullEffect = true;
        //$this->lblMessage->HalfEffect = true;

        $this->lblTitle = new Q\Plugin\Label($this);
        $this->lblTitle->Text = t('Existing menu text');
        $this->lblTitle->addCssClass('col-md-3');
        $this->lblTitle->setCssStyle('font-weight', 400);

        $this->lblMenuTitle = new Bs\Label($this);
        $this->lblMenuTitle->Text = $this->objMenuContent->getMenuText();
        $this->lblMenuTitle->setCssStyle('font-weight', 400);

        $this->lblContentType = new Q\Plugin\Label($this);
        $this->lblContentType->Text = t('Content type');
        $this->lblContentType->addCssClass('col-md-3');
        $this->lblContentType->setCssStyle('font-weight', 400);
        $this->lblContentType->Required = true;

        $this->lstContentTypes = new Q\Plugin\Select2($this);
        $this->lstContentTypes->MinimumResultsForSearch = -1;
        $this->lstContentTypes->Theme = 'web-vauu';
        $this->lstContentTypes->Width = '100%';
        $this->lstContentTypes->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;


        if (!$this->objMenuContent->getRedirectUrl() /*&& $this->objMenuContent->getContentType()*/) {
            $this->lstContentTypes->addItem(t('- Select one type -'), null, true);
        }
        $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems());
        $this->lstContentTypes->SelectedValue = $this->objMenuContent->ContentType;
        $this->lstContentTypes->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstClassNames_Change'));

        $this->lblMenuText = new Q\Plugin\Label($this);
        $this->lblMenuText->Text = t('Menu text');
        $this->lblMenuText->addCssClass('col-md-3');
        $this->lblMenuText->setCssStyle('font-weight', 400);
        $this->lblMenuText->Required = true;

        $this->txtMenuText = new Bs\TextBox($this);
        $this->txtMenuText->Placeholder = t('Menu text');
        $this->txtMenuText->Text = $this->objMenuContent->MenuText;
        $this->txtMenuText->addWrapperCssClass('center-button');
        $this->txtMenuText->MaxLength = MenuContent::MenuTextMaxLength;

        if ($this->objMenuContent->ContentType == null) {
            $this->txtMenuText->Enabled = false;
        }

        $this->createButtons();
    }

    public function lstContentTypeObject_GetItems()
    {
        $strContentTypeArray = ContentType::nameArray();
        unset($strContentTypeArray[1]);
        return $strContentTypeArray;
    }

    protected function lstClassNames_Change(ActionParams $params)
    {
        $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
        $this->objMenuContent->save();

        if ($this->objMenuContent->ContentType !== 5 && $this->objMenuContent->ContentType !== 6 && $this->objMenuContent->ContentType !== 7) {
            $objArticle = new Article();
            $objArticle->setMenuContentId($this->objMenuContent->Id);
            $objArticle->setPostDate(Q\QDateTime::Now());
            $objArticle->save();

            $objMetadata = new Metadata();
            $objMetadata->setMenuContentId($this->objMenuContent->Id);
            $objMetadata->save();
        }

        Application::redirect('menu_edit.php?id=' . $this->intId);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnCancel = new Q\Plugin\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////


    public function btnMenuCancel_Click(ActionParams $params)
    {
        $this->redirectToListPage();
    }

    protected function redirectToListPage()
    {
        Application::redirect('menu_manager.php');
    }
}