<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;

class PageMetadataPanel extends Q\Control\Panel
{
    public $dlgModal;
    protected $dlgToastr;
    
    protected $lblInfo;

    public $lblKeywords;
    public $txtKeywords;

    public $lblDescription;
    public $txtDescription;

    public $lblAuthor;
    public $txtAuthor;

    public $btnSave;
    public $btnSaving;
    public $btnDelete;
    public $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $objMetadata;

    protected $strTemplate = 'PageMetaDataPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $intId = Application::instance()->context()->queryStringItem('id');
        $this->objMetadata = Metadata::loadByIdFromContentId($intId);

        $this->lblInfo = new Q\Plugin\Control\Alert($this);
        $this->lblInfo->Display = true;
        $this->lblInfo->Dismissable = true;
        $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
        $this->lblInfo->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
        $this->lblInfo->addCssClass('alert alert-info alert-dismissible');
        $this->lblInfo->Text = t('These fields can be left blank. If there is a special need to highlight the special
                                features of one page or another, which search engines might find and offer to people.
                                By default, the global metadata of the website is sufficient.');

        $this->lblKeywords = new Q\Plugin\Control\Label($this);
        $this->lblKeywords->Text = t('Keywords of the metadata');
        $this->lblKeywords->addCssClass('col-md-3');
        $this->lblKeywords->setCssStyle('font-weight', 400);

        $this->txtKeywords = new Bs\TextBox($this);
        $this->txtKeywords->Text = $this->objMetadata->Keywords;
        $this->txtKeywords->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtKeywords->Rows = 3;
        $this->txtKeywords->addWrapperCssClass('center-button');
        $this->txtKeywords->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtKeywords->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtKeywords->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtKeywords->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblDescription = new Q\Plugin\Control\Label($this);
        $this->lblDescription->Text = t('Description of the metadata');
        $this->lblDescription->addCssClass('col-md-3');
        $this->lblDescription->setCssStyle('font-weight', 400);

        $this->txtDescription = new Bs\TextBox($this);
        $this->txtDescription->Text = $this->objMetadata->Description;
        $this->txtDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
        $this->txtDescription->Rows = 3;
        $this->txtDescription->addWrapperCssClass('center-button');
        $this->txtDescription->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtDescription->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtDescription->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtDescription->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblAuthor = new Q\Plugin\Control\Label($this);
        $this->lblAuthor->Text = t('Author');
        $this->lblAuthor->addCssClass('col-md-3');
        $this->lblAuthor->setCssStyle('font-weight', 400);

        $this->txtAuthor = new Bs\TextBox($this);
        $this->txtAuthor->Text = $this->objMetadata->Author;
        $this->txtAuthor->addWrapperCssClass('center-button');
        $this->txtAuthor->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtAuthor->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtAuthor->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtAuthor->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        
        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnSave = new Q\Plugin\Control\Button($this);
        if ($this->objMetadata->getKeywords() ||
            $this->objMetadata->getDescription() ||
            $this->objMetadata->getAuthor()) {
            $this->btnSave->Text = t('Update');
        } else {
            $this->btnSave->Text = t('Save');
        }
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnMenuSave_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSaveButtonId = $this->btnSave->ControlId;

        $this->btnSaving = new Q\Plugin\Control\Button($this);
        if ($this->objMetadata->getKeywords() ||
            $this->objMetadata->getDescription() ||
            $this->objMetadata->getAuthor()) {
            $this->btnSaving->Text = t('Update and close');
        } else {
            $this->btnSaving->Text = t('Save and close');
        }
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnMenuSaveClose_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSavingButtonId = $this->btnSaving->ControlId;

        $this->btnDelete = new Q\Plugin\Control\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->addWrapperCssClass('center-button');
        $this->btnDelete->CausesValidation = false;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'btnMenuDelete_Click'));

        $this->btnCancel = new Q\Plugin\Control\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
    }

    protected function createToastr()
    {
        $this->dlgToastr = new Q\Plugin\Toastr($this);
        $this->dlgToastr->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr->Message = t('<strong>Well done!</strong> The post has been saved or modified.');
        $this->dlgToastr->ProgressBar = true;
    }
    
    public function createModals()
    {
        $this->dlgModal = new Bs\Modal($this);
        $this->dlgModal->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to delete the specific metadata of this page?</p>
                            <p style="line-height: 25px; margin-bottom: -3px;">If desired, you can later re-write!</p>');
        $this->dlgModal->Title = t('Warning');
        $this->dlgModal->HeaderClasses = 'btn-danger';
        $this->dlgModal->addButton(t("I accept"), t('This menu metadata has been permanently deleted.'), false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal->addCloseButton(t("I'll cancel"));
        $this->dlgModal->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'deletedItem_Click'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function btnMenuSave_Click(ActionParams $params)
    {
        $this->objMetadata->setKeywords($this->txtKeywords->Text);
        $this->objMetadata->setDescription($this->txtDescription->Text);
        $this->objMetadata->setAuthor($this->txtAuthor->Text);
        $this->objMetadata->save();

        if (!$this->objMetadata->getKeywords() ||
            !$this->objMetadata->getDescription() ||
            !$this->objMetadata->getAuthor()) {

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

        $this->dlgToastr->notify();
    }

    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        $this->objMetadata->setKeywords($this->txtKeywords->Text);
        $this->objMetadata->setDescription($this->txtDescription->Text);
        $this->objMetadata->setAuthor($this->txtAuthor->Text);
        $this->objMetadata->save();

        $this->redirectToListPage();
    }

    public function btnMenuDelete_Click(ActionParams $params)
    {
        if ($this->objMetadata->getKeywords() || $this->objMetadata->getDescription() || $this->objMetadata->getAuthor()) {
            $this->dlgModal->showDialogBox();
        }
    }

    public function deletedItem_Click(ActionParams $params)
    {
        $this->objMetadata->setKeywords(null);
        $this->objMetadata->setDescription(null);
        $this->objMetadata->setAuthor(null);
        $this->objMetadata->save();

        $this->txtKeywords->Text = '';
        $this->txtDescription->Text = '';
        $this->txtAuthor->Text = '';

        $this->dlgModal->hideDialogBox();
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