<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Control\Panel;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Control\ListItem;

use QCubed\Control\ListItemStyle;

use QCubed\Query\QQ;

class InternalPageEditPanel extends Panel
{
    public $lblMessage;

    public $lblExistingMenuText;
    public $txtExistingMenuText;

    public $lblMenuText;
    public $txtMenuText;

    public $lblContentType;
    public $lstContentTypes;

    public $lblSelectedPage;
    public $lstSelectedPage;

    public $lblStatus;
    public $lstStatus;

    public $lblTitleSlug;
    public $txtTitleSlug;

    public $btnSave;
    public $btnSaving;
    public $btnDelete;
    public $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $modal1;
    protected $modal2;

    protected $intId;
    protected $objMenuContent;
    protected $objMenu;
    protected $strRedirectUrl;
    protected $strDoubleRoutingInfo;

    protected $objSelectedPageCondition;
    protected $objSelectedPageClauses;

    protected $strTemplate = 'InternalPageEditPanel.tpl.php';

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

        $this->objMenu = Menu::load($this->intId);

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

        $this->lblSelectedPage = new Q\Plugin\Label($this);
        $this->lblSelectedPage->Text = t('Internal page redirect');
        $this->lblSelectedPage->addCssClass('col-md-3');
        $this->lblSelectedPage->setCssStyle('font-weight', 400);
        $this->lblSelectedPage->Required = true;

        $this->lstSelectedPage = new Q\Plugin\Select2($this);
        $this->lstSelectedPage->MinimumResultsForSearch = -1;
        $this->lstSelectedPage->Theme = 'web-vauu';
        $this->lstSelectedPage->Width = '100%';
        $this->lstSelectedPage->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        if (!$this->objMenuContent->SelectedPageId) {
            $this->lstSelectedPage->addItem(t('- Select one internal page -'), null, true);
        }
        $this->lstSelectedPage->addItems($this->lstSelectedPage_GetItems());
        $this->lstSelectedPage->SelectedValue = $this->objMenuContent->SelectedPageId;
        $this->lstSelectedPage->AddAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'lstSelectedPage_Click'));

        $this->lblStatus = new Q\Plugin\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->addCssClass('col-md-3');
        $this->lblStatus->Required = true;

        $this->lstStatus = new Q\Plugin\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 0 => t('Hidden'), 2 => t('Draft')]);
        $this->lstStatus->SelectedValue = $this->objMenuContent->IsEnabled;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange radio-inline';
        $this->lstStatus->AddAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'lstStatus_Click'));

        if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->lstStatus->Enabled = false;
        }

        $this->lblTitleSlug = new Q\Plugin\Label($this);
        $this->lblTitleSlug->Text = t('View');
        $this->lblTitleSlug->addCssClass('col-md-3');
        $this->lblTitleSlug->setCssStyle('font-weight', 400);

        if ($this->objMenuContent->getRedirectUrl()) {
            $this->txtTitleSlug = new Q\Plugin\Label($this);

            if ($this->objMenuContent->getIsRedirect() == null) {
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objMenuContent->getRedirectUrl();
            } else {
                $url = $this->objMenuContent->getRedirectUrl();
            }
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
        } else {
            $this->txtTitleSlug = new Bs\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->strDoubleRoutingInfo = new Panel($this);
        $this->strDoubleRoutingInfo->Text = t('Warning, double routing!');
        $this->strDoubleRoutingInfo->setCssClass('label label-sm label-danger');
        $this->strDoubleRoutingInfo->TagName = 'span';
        $this->strDoubleRoutingInfo->Display = false;

        if ($this->objMenuContent->getContentType() == 7 && $this->objMenuContent->getIsRedirect() == 1) {
            $this->strDoubleRoutingInfo->Display = true;
        } else {
            $this->strDoubleRoutingInfo->Display = false;
        }

        $this->modal1 = new Bs\Modal($this);
        $this->modal1->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Kas oled kindel, et soovid selle sisemise lehe suunamise kustutada?</p>');
        $this->modal1->Title = t('Warning');
        $this->modal1->HeaderClasses = 'btn-danger';
        $this->modal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->modal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->modal1->addAction(new Q\Event\DialogButton(), new Q\Action\AjaxControl($this, 'changeItem_Click'));

        $this->modal2 = new Bs\Modal($this);
        $this->modal2->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Praegu on tegemist alammenüüdega seotud peamenüü kirjega või alammenüü kirjetega ja
                                siin ei saa selle kirje staatust muuta.</p><p style="line-height: 25px; margin-bottom: -3px;">
                                Selle kirje staatuse muutmiseks pead minema menüü haldurisse ja selle aktiveerima või deaktiveerima.</p>');
        $this->modal2->Title = t("Tip");
        $this->modal2->HeaderClasses = 'btn-darkblue';
        $this->modal2->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->createButtons();
    }

    public function lstContentTypeObject_GetItems()
    {
        $strContentTypeArray = ContentType::nameArray();
        unset($strContentTypeArray[1]);
        return $strContentTypeArray;
    }

    public function lstSelectedPage_GetItems()
    {
        $a = array();
        $objCondition = $this->objSelectedPageCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objSelectedPageCursor = MenuContent::queryCursor($objCondition, $this->objSelectedPageClauses);

        // Iterate through the Cursor
        while ($objSelectedPage = MenuContent::instantiateCursor($objSelectedPageCursor)) {
            $objListItem = new ListItem($objSelectedPage->printHierarchy(), $objSelectedPage->Id);
            if (($this->objMenuContent->SelectedPage) && ($this->objMenuContent->SelectedPage->Id == $objSelectedPage->Id)) {
                $objListItem->Selected = true;
                $objListItem->Disabled = true;
            }
            //if ($objSelectedPage->ContentType === null /*||$objSelectedPage->ContentType == 6 */|| $objSelectedPage->IsEnabled === 0 || $objSelectedPage->IsEnabled === 2) {
                //$objListItem->WrapperClass('red');

                //$objListItem = new Q\TagStyler();
                //$objListItem->ItemStyle('class', 'red');

                //$objListItem->ItemStyle->setAttributes('class', 'red');

                //* @property string $strWrapperClass with, an item can be highlighted or displayed differently from other items if needed.

           // }
            $a[] = $objListItem;
        }
        return $a;
    }

    /*public function WrapperClass($strClass)
    {
        return $this->setCssClass($strClass);
    }*/

    public function lstSelectedPage_Click(ActionParams $params) {}

    public function lstClassNames_Change(ActionParams $params)
    {
        if ($this->objMenuContent->getContentType() !== $this->lstContentTypes->SelectedValue ||
            $this->objMenuContent->getSelectedPageId() !== $this->lstSelectedPage->SelectedValue) {
            $this->modal1->showDialogBox();
        } else {
            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->setSelectedPageId($this->lstSelectedPage->SelectedValue);
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
        $this->modal1->hideDialogBox();
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

    public function lstStatus_Click(ActionParams $params)
    {
        if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->modal2->showDialogBox();
        }
    }

    public function btnMenuSave_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text && $this->lstSelectedPage->SelectedValue) {
            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setSelectedPageId($this->lstSelectedPage->SelectedValue);
            $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            $this->objMenuContent->setIsRedirect($this->objMenuContent->getIsRedirect());

            if ($this->objMenuContent->getSelectedPageId()) {
                $objRedirectUrl = MenuContent::load($this->objMenuContent->SelectedPageId);
                $this->objMenuContent->setRedirectUrl($objRedirectUrl->getRedirectUrl());
                $this->objMenuContent->setIsRedirect($objRedirectUrl->getIsRedirect());
            }
            $this->objMenuContent->save();

            if ($this->objMenuContent->getRedirectUrl()) {
                $this->txtTitleSlug = new Bs\Label($this);

                if ($this->objMenuContent->getIsRedirect() == null) {
                    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                        $this->objMenuContent->getRedirectUrl();
                } else {
                    $url = $this->objMenuContent->getRedirectUrl();
                }

                $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug = new Bs\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            if ($this->objMenuContent->getContentType() == 7 && $this->objMenuContent->getIsRedirect() == 1) {
                $this->strDoubleRoutingInfo->Display = true;
            } else {
                $this->strDoubleRoutingInfo->Display = false;
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
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the menu title or internal page redirect must exist!');
        }
    }

    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text && $this->lstSelectedPage->SelectedValue) {
            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setSelectedPageId($this->lstSelectedPage->SelectedValue);
            $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            $this->objMenuContent->setIsRedirect($this->objMenuContent->getIsRedirect());

            if ($this->objMenuContent->getSelectedPageId()) {
                $objRedirectUrl = MenuContent::load($this->objMenuContent->SelectedPageId);
                $this->objMenuContent->setRedirectUrl($objRedirectUrl->getRedirectUrl());
                $this->objMenuContent->setIsRedirect($objRedirectUrl->getIsRedirect());
            }
            $this->objMenuContent->save();
            $this->redirectToListPage();
        } else {
            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->txtMenuText->focus();
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_DANGER);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the menu title or internal page redirect must exist!');
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