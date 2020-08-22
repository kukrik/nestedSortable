<?php

// https://stackoverflow.com/questions/18999501/bootstrap-3-keep-selected-tab-on-page-refresh

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Control\Panel;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\QString;


class RedirectingEditPanel extends Panel
{
    public $lblMessage;

    public $lblExistingMenuText;
    public $txtExistingMenuText;

    public $lblMenuText;
    public $txtMenuText;

    public $lblContentType;
    public $lstContentTypes;

    public $lblRedirect;
    public $txtRedirect;

    public $lblTargetTypeObject;
    public $lstTargetTypeObject;

    public $lblTitleSlug;
    public $txtTitleSlug;

    public $btnSave;
    public $btnSaving;
    public $btnDelete;
    public $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $modal;

    protected $intId;
    protected $objMenuContent;

    protected $strTemplate = 'RedirectingEditPanel.tpl.php';

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

        $this->lblMessage = new Q\Plugin\Alert($this);
        $this->lblMessage->Display = false;
        $this->lblMessage->FullEffect = true;
        //$this->lblMessage->HalfEffect = true;

        $this->lblExistingMenuText = new Q\Plugin\Label($this);
        $this->lblExistingMenuText->Text = t('Existing menu text');
        $this->lblExistingMenuText->addCssClass('col-md-3');
        $this->lblExistingMenuText->setCssStyle('font-weight', 400);

        $this->txtExistingMenuText = new Bs\Label($this);
        $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
        $this->txtExistingMenuText->setCssStyle('font-weight', 400);

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
        $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems(), true);
        $this->lstContentTypes->SelectedValue = $this->objMenuContent->ContentType;
        $this->lstContentTypes->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstClassNames_Change'));

        $this->lblRedirect = new Q\Plugin\Label($this);
        $this->lblRedirect->Text = t('Redirecting url');
        $this->lblRedirect->addCssClass('col-md-3');
        $this->lblRedirect->setCssStyle('font-weight', 400);
        $this->lblRedirect->Required = true;

        $this->txtRedirect = new Bs\TextBox($this);
        $this->txtRedirect->Placeholder = 'https://';
        $this->txtRedirect->Text = $this->objMenuContent->RedirectUrl;
        $this->txtRedirect->addWrapperCssClass('center-button');
        $this->txtRedirect->MaxLength = MenuContent::RedirectUrlMaxLength;

        $this->lblTargetTypeObject = new Q\Plugin\Label($this);
        $this->lblTargetTypeObject->Text = t('Target type');
        $this->lblTargetTypeObject->addCssClass('col-md-3');
        $this->lblTargetTypeObject->setCssStyle('font-weight', 400);

        $this->lstTargetTypeObject = new Q\Plugin\Select2($this);
        $this->lstTargetTypeObject->MinimumResultsForSearch = -1;
        $this->lstTargetTypeObject->Theme = 'web-vauu';
        $this->lstTargetTypeObject->Width = '100%';
        $this->lstTargetTypeObject->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstTargetTypeObject->addItem(t('- Select target type -'), null, true);
        $this->lstTargetTypeObject->addItems($this->lstTargetTypeObject_GetItems());
        $this->lstTargetTypeObject->SelectedValue = $this->objMenuContent->TargetType;
        $this->lstTargetTypeObject->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->lstTargetTypeObject->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->lstTargetTypeObject->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->lstTargetTypeObject->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblTitleSlug = new Q\Plugin\Label($this);
        $this->lblTitleSlug->Text = t('View');
        $this->lblTitleSlug->addCssClass('col-md-3');
        $this->lblTitleSlug->setCssStyle('font-weight', 400);

        if ($this->objMenuContent->getRedirectUrl()) {
            $this->txtTitleSlug = new Bs\Label($this);
            $url = $this->objMenuContent->getRedirectUrl();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
        } else {
            $this->txtTitleSlug = new Bs\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->modal = new Bs\Modal($this);
        $this->modal->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Kas oled kindel, et soovid selle välise suunamise kustutada?</p>');
        $this->modal->Title = t('Warning');
        $this->modal->HeaderClasses = 'btn-danger';
        $this->modal->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->modal->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->modal->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'changeItem_Click'));
        
        $this->createButtons();
    }

    public function lstContentTypeObject_GetItems()
    {
        $strContentTypeArray = ContentType::nameArray();
        unset($strContentTypeArray[1]);
        return $strContentTypeArray;
    }

    public function lstTargetTypeObject_GetItems()
    {
        return TargetType::nameArray();
    }

    protected function lstClassNames_Change(ActionParams $params)
    {
        if ($this->objMenuContent->getContentType() !== $this->lstContentTypes->SelectedValue) {
            $this->modal->showDialogBox();
        } else {
            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->save();
            Application::redirect('menu_edit.php?id=' . $this->intId);
        }
    }

    public function changeItem_Click(ActionParams $params)
    {
        if ($params->ActionParameter == "pass") {
            $this->objMenuContent->setRedirectUrl(null);
            $this->objMenuContent->setIsRedirect(null);
            $this->objMenuContent->setSelectedPageId(null);
            $this->objMenuContent->setTargetType(null);
            $this->objMenuContent->setContentType(null);
            $this->objMenuContent->setIsEnabled(0);
            $this->objMenuContent->save();
        } else {
            // does nothing
        }
        Application::redirect('menu_edit.php?id=' . $this->intId);
        $this->modal->hideDialogBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnSave = new Q\Plugin\Button($this);
        if ($this->objMenuContent->getRedirectUrl()) {
            $this->btnSave->Text = t('Update');
        } else {
            $this->btnSave->Text = t('Save');
        }
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSaveButtonId = $this->btnSave->ControlId;

        $this->btnSaving = new Q\Plugin\Button($this);
        if ($this->objMenuContent->getRedirectUrl()) {
            $this->btnSaving->Text = t('Update and close');
        } else {
            $this->btnSaving->Text = t('Save and close');
        }
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSaveClose_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSavingButtonId = $this->btnSaving->ControlId;
        
        $this->btnCancel = new Q\Plugin\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function btnMenuSave_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text && $this->txtRedirect->Text) {
            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setRedirectUrl($this->txtRedirect->Text);
            $this->objMenuContent->setIsRedirect(1);
            $this->objMenuContent->setTargetType($this->lstTargetTypeObject->SelectedValue);
            $this->objMenuContent->save();

            if ($this->objMenuContent->getRedirectUrl()) {
                $this->txtTitleSlug = new Bs\Label($this);
                $url = $this->objMenuContent->getRedirectUrl();
                $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug = new Bs\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            if (!$this->objMenuContent->getMenuText() ||
                !$this->objMenuContent->getRedirectUrl()) {

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

            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->Text = t('<strong>Well done!</strong> The post has been saved or modified.');
        } else {
            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->txtMenuText->focus();
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_DANGER);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the menu title or redirecting url must exist!');
        }
    }

    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text && $this->txtRedirect->Text) {
            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setRedirectUrl($this->txtRedirect->Text);
            $this->objMenuContent->setIsRedirect(1);
            $this->objMenuContent->setTargetType($this->lstTargetTypeObject->SelectedValue);
            $this->objMenuContent->save();

            $this->redirectToListPage();
        } else {
            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->txtMenuText->focus();
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_DANGER);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the menu title or redirecting url must exist!');
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