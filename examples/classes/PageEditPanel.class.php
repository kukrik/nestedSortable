<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;

class PageEditPanel extends Q\Control\Panel
{
    protected $dlgToastr1;
    protected $dlgToastr2;

    public $lblExistingMenuText;
    public $txtExistingMenuText;

    public $lblContentType;
    public $lstContentTypes;

    public $lblMenuText;
    public $txtMenuText;

    public $btnSave;
    public $btnSaving;
    public $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    protected $objMenuContent;
    protected $strMethodCallBack;

    protected $strTemplate = 'PageEditPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenuContent = MenuContent::load($this->intId);

        $this->lblExistingMenuText = new Q\Plugin\Control\Label($this);
        $this->lblExistingMenuText->Text = t('Existing menu text');
        $this->lblExistingMenuText->addCssClass('col-md-3');
        $this->lblExistingMenuText->setCssStyle('font-weight', 400);

        $this->txtExistingMenuText = new Q\Plugin\Control\Label($this);
        $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
        $this->txtExistingMenuText->setCssStyle('font-weight', 400);

        $this->lblMenuText = new Q\Plugin\Control\Label($this);
        $this->lblMenuText->Text = t('Menu text');
        $this->lblMenuText->addCssClass('col-md-3');
        $this->lblMenuText->setCssStyle('font-weight', 400);
        $this->lblMenuText->Required = true;

        $this->txtMenuText = new Bs\TextBox($this);
        $this->txtMenuText->Placeholder = t('Menu text');
        $this->txtMenuText->Text = $this->objMenuContent->MenuText;
        $this->txtMenuText->addWrapperCssClass('center-button');
        $this->txtMenuText->MaxLength = MenuContent::MenuTextMaxLength;

        $this->lblContentType = new Q\Plugin\Control\Label($this);
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

        $this->createButtons();
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnSave = new Q\Plugin\Control\Button($this);
        if (is_null($this->objMenuContent->getContentType())) {
            $this->btnSave->Text = t('Save');
        } else {
            $this->btnSave->Text = t('Update');
        }
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSaveButtonId = $this->btnSave->ControlId;

        $this->btnSaving = new Q\Plugin\Control\Button($this);
        if (is_null($this->objMenuContent->getContentType())) {
            $this->btnSaving->Text = t('Save and close');
        } else {
            $this->btnSaving->Text = t('Update and close');
        }
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSaveClose_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSavingButtonId = $this->btnSaving->ControlId;

        $this->btnCancel = new Q\Plugin\Control\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
    }

    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> The post has been saved or modified.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, the menu title must exist!');
        $this->dlgToastr2->ProgressBar = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

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

        if ($this->objMenuContent->ContentType == 2) {
            $objArticle = new Article();
            $objArticle->setMenuContentId($this->objMenuContent->Id);
            $objArticle->setPostDate(Q\QDateTime::Now());
            $objArticle->save();

            $objMetadata = new Metadata();
            $objMetadata->setMenuContentId($this->objMenuContent->Id);
            $objMetadata->save();
        }

        if ($this->objMenuContent->ContentType == 3) {
            $objMetadata = new Metadata();
            $objMetadata->setMenuContentId($this->objMenuContent->Id);
            $objMetadata->save();
        }

        if ($this->objMenuContent->ContentType == 10) {
            $objErrorPages = new ErrorPages();
            $objErrorPages->setMenuContentId($this->objMenuContent->Id);
            $objErrorPages->setPostDate(Q\QDateTime::Now());
            $objErrorPages->save();
        }
        Application::redirect('menu_edit.php?id=' . $this->intId);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function btnMenuSave_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text) {
            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setIsEnabled(0);
            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->save();

            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();

            if (is_null($this->objMenuContent->getContentType())) {
                $strSave_translate = t('Save');
                $strSaveAndClose_translate = t('Save and close');
                Application::executeJavaScript(sprintf("jQuery($this->strSaveButtonId).text('{$strSave_translate}');"));
                Application::executeJavaScript(sprintf("jQuery($this->strSavingButtonId).text('{$strSaveAndClose_translate}');"));
            } else {
                $strUpdate_translate = t('Update');
                $strUpdateAndClose_translate = t('Update and close');
                Application::executeJavaScript(sprintf("jQuery($this->strSaveButtonId).text('{$strUpdate_translate}');"));
                Application::executeJavaScript(sprintf("jQuery($this->strSavingButtonId).text('{$strUpdateAndClose_translate}');"));
            }

            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text) {
            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setIsEnabled(0);
            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->save();

            $this->redirectToListPage();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    public function btnMenuCancel_Click(ActionParams $params)
    {
        $this->redirectToListPage();
    }

    protected function redirectToListPage()
    {
        Application::redirect('menu_manager.php');
    }
}