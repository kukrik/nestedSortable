<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\QString;
use QCubed\Css;

class ErrorPageEditPanel extends Q\Control\Panel
{
    public $dlgModal1;
    public $dlgModal2;

    protected $dlgToastr1;
    protected $dlgToastr2;

    public $lblExistingMenuText;
    public $txtExistingMenuText;

    public $lblMenuText;
    public $txtMenuText;

    public $lblContentType;
    public $lstContentTypes;

    public $lblErrorTitle;
    public $txtErrorTitle;

    public $lblTitleSlug;
    public $txtTitleSlug;

    public $txtContent;

    public $lblPostDate;
    public $calPostDate;

    public $lblPostUpdateDate;
    public $calPostUpdateDate;

    public $lblStatus;
    public $lstStatus;

    public $btnSave;
    public $btnSaving;
    public $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    protected $objMenu;
    protected $objMenuContent;
    protected $objErrorPages;

    protected $objCategoryCondition;
    protected $objCategoryClauses;

    protected $strTemplate = 'ErrorPageEditPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $this->intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenu = Menu::load($this->intId);
        $this->objMenuContent = MenuContent::load($this->intId);
        $this->objErrorPages = ErrorPages::loadByIdFromContentId($this->intId);

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
        $this->txtMenuText->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtMenuText->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtMenuText->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtMenuText->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

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
        $this->lstContentTypes->addItems($this->lstContentTypeObject_GetItems());
        $this->lstContentTypes->SelectedValue = $this->objMenuContent->ContentType;
        $this->lstContentTypes->addAction(new Q\Event\Change(), new Q\Action\AjaxControl($this,'lstClassNames_Change'));

        $this->lblErrorTitle = new Q\Plugin\Control\Label($this);
        $this->lblErrorTitle->Text = t('Title');
        $this->lblErrorTitle->addCssClass('col-md-3');
        $this->lblErrorTitle->setCssStyle('font-weight', 400);
        $this->lblErrorTitle->Required = true;

        $this->txtErrorTitle = new Bs\TextBox($this);
        $this->txtErrorTitle->Placeholder = t('Title');
        $this->txtErrorTitle->Text = $this->objErrorPages->ErrorTitle;
        $this->txtErrorTitle->addWrapperCssClass('center-button');
        $this->txtErrorTitle->MaxLength = ErrorPages::ErrorTitleMaxLength;
        $this->txtErrorTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtErrorTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtErrorTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtErrorTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View');
        $this->lblTitleSlug->addCssClass('col-md-3');
        $this->lblTitleSlug->setCssStyle('font-weight', 400);

        if ($this->txtErrorTitle->Text) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                '/' . QString::sanitizeForUrl($this->objMenuContent->MenuText) . '/' . $this->objErrorPages->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
        } else {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->txtContent = new Q\Plugin\CKEditor($this);
        $this->txtContent->Text = $this->objErrorPages->Content;
        $this->txtContent->Rows = 10;
        $this->txtContent->Columns = 80;
        $this->txtContent->Configuration = 'ckConfig';
        $this->txtContent->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtContent->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtContent->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtContent->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Published');

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->Text = $this->objErrorPages->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->calPostDate->setCssStyle('font-weight', 'bold');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objErrorPages->PostUpdateDate ? $this->objErrorPages->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'bold');

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->Required = true;

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 0 => t('Hidden'), 2 => t('Draft')]);
        $this->lstStatus->SelectedValue = $this->objMenuContent->IsEnabled;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->AddAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'lstStatus_Click'));

        if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->lstStatus->Enabled = false;
        }

        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnSave = new Q\Plugin\Control\Button($this);
        if ($this->objErrorPages->getContent()) {
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

        $this->btnSaving = new Q\Plugin\Control\Button($this);
        if ($this->objErrorPages->getContent()) {
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
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, the menu title or content title must exist!');
        $this->dlgToastr2->ProgressBar = true;
    }

    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Kas oled kindel, et soovid seda veateadet kustutada?</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'changeItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Praegu on tegemist alammenüüdega seotud peamenüü kirjega või alammenüü kirjetega ja
                                siin ei saa selle kirje staatust muuta.</p><p style="line-height: 25px; margin-bottom: -3px;">
                                Selle kirje staatuse muutmiseks pead minema menüü haldurisse ja selle aktiveerima või deaktiveerima.</p>');
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function lstContentTypeObject_GetItems()
    {
        $strContentTypeArray = ContentType::nameArray();
        unset($strContentTypeArray[1]);
        return $strContentTypeArray;
    }

    public function lstStatus_Click(ActionParams $params)
    {
        if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->dlgModal2->showDialogBox();
        }
    }

    public function lstClassNames_Change(ActionParams $params)
    {
        if ($this->objMenuContent->getContentType() !== $this->lstContentTypes->SelectedValue) {
            $this->dlgModal1->showDialogBox();
        } else {
            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->save();
            Application::redirect('menu_edit.php?id=' . $this->intId);
        }
    }

    public function changeItem_Click(ActionParams $params)
    {
        if ($params->ActionParameter == "pass") {
            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->setRedirectUrl(null);
            $this->objMenuContent->setHomelyUrl(null);
            $this->objMenuContent->setIsRedirect(null);
            if ($this->objMenuContent->getRedirectUrl()) {
                $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            } else {
                $this->objMenuContent->setIsEnabled(0);
            }
            $this->objMenuContent->save();

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
        } else {
            // does nothing
        }
        $this->dlgModal1->hideDialogBox();
    }

    public function btnMenuSave_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text && $this->txtErrorTitle->Text) {

            $this->objErrorPages->setErrorTitle($this->txtErrorTitle->Text);
            $this->objErrorPages->setTitleSlug(QString::sanitizeForUrl($this->txtErrorTitle->Text));
            $this->objErrorPages->setContent($this->txtContent->Text);
            $this->objErrorPages->setPostUpdateDate(Q\QDateTime::Now());
            $this->objErrorPages->save();

            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->setHomelyUrl(1);
            $this->objMenuContent->setRedirectUrl('/'. QString::sanitizeForUrl($this->objMenuContent->MenuText) .
                '/' .  QString::sanitizeForUrl($this->objErrorPages->TitleSlug));
            $this->objMenuContent->save();

            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
            $this->calPostUpdateDate->Text = $this->objErrorPages->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            if ($this->txtErrorTitle->Text) {
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    '/' . QString::sanitizeForUrl($this->objMenuContent->MenuText) . '/' . $this->objErrorPages->getTitleSlug();
                $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            if ($this->objErrorPages->getContent()) {
                $strUpdate_translate = t('Update');
                $strUpdateAndClose_translate = t('Update and close');
                Application::executeJavaScript(sprintf("jQuery($this->strSaveButtonId).text('{$strUpdate_translate}');"));
                Application::executeJavaScript(sprintf("jQuery($this->strSavingButtonId).text('{$strUpdateAndClose_translate}');"));
            } else {
                $strSave_translate = t('Save');
                $strSaveAndClose_translate = t('Save and close');
                Application::executeJavaScript(sprintf("jQuery($this->strSaveButtonId).text('{$strSave_translate}');"));
                Application::executeJavaScript(sprintf("jQuery($this->strSavingButtonId).text('{$strSaveAndClose_translate}');"));
            }

            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text && $this->txtErrorTitle->Text) {
            $this->objErrorPages->setErrorTitle($this->txtErrorTitle->Text);
            $this->objErrorPages->setTitleSlug(QString::sanitizeForUrl($this->txtErrorTitle->Text));
            $this->objErrorPages->setContent($this->txtContent->Text);
            $this->objErrorPages->setPostUpdateDate(Q\QDateTime::Now());
            $this->objErrorPages->save();

            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->setHomelyUrl(1);
            $this->objMenuContent->setRedirectUrl('/'. QString::sanitizeForUrl($this->objMenuContent->MenuText) .
                '/' . $this->objErrorPages->getTitleSlug());
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