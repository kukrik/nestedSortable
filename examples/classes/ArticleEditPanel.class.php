<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;
use QCubed\QString;
use QCubed\Css;

class ArticleEditPanel extends Q\Control\Panel
{
    protected $lblMessage;
    protected $modal1;
    protected $modal2;

    public $lblExistingMenuText;
    public $txtExistingMenuText;

    public $lblMenuText;
    public $txtMenuText;

    public $lblContentType;
    public $lstContentTypes;

    public $lblTitle;
    public $txtTitle;

    public $lblCategory;
    public $lstCategory;

    public $lblTitleSlug;
    public $txtTitleSlug;

    public $txtContent;

    public $lblPostDate;
    public $calPostDate;

    public $lblPostUpdateDate;
    public $calPostUpdateDate;

    public $lblPicture;
    public $txtPicture;

    public $lblPictureDescription;
    public $txtPictureDescription;

    public $lblAuthorSource;
    public $txtAuthorSource;

    public $lblStatus;
    public $lstStatus;

    public $lblConfirmationAsking;
    public $chkConfirmationAsking;

    public $btnSave;
    public $btnSaving;
    public $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    protected $objMenu;
    protected $objMenuContent;
    protected $objArticle;
    protected $objMetadata;

    protected $objCategoryCondition;
    protected $objCategoryClauses;

    protected $strTemplate = 'ArticleEditPanel.tpl.php';

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
        $this->objArticle = Article::loadByIdFromContentId($this->intId);
        $this->objMetadata = Metadata::loadByIdFromContentId($this->intId);

        $this->lblMessage = new Q\Plugin\Control\Alert($this);
        $this->lblMessage->Display = false;
        $this->lblMessage->FullEffect = true;
        //$this->lblMessage->HalfEffect = true;

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


        $this->lblTitle = new Q\Plugin\Control\Label($this);
        $this->lblTitle->Text = t('Title');
        $this->lblTitle->addCssClass('col-md-3');
        $this->lblTitle->setCssStyle('font-weight', 400);
        $this->lblTitle->Required = true;

        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Title');
        $this->txtTitle->Text = $this->objArticle->Title;
        $this->txtTitle->addWrapperCssClass('center-button');
        $this->txtTitle->MaxLength = Article::TitleMaxLength;
        $this->txtTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblCategory = new Q\Plugin\Control\Label($this);
        $this->lblCategory->Text = t('Category');
        $this->lblCategory->addCssClass('col-md-3');
        $this->lblCategory->setCssStyle('font-weight', 400);

        $this->lstCategory = new Q\Plugin\Select2($this);
        $this->lstCategory->MinimumResultsForSearch = -1;
        $this->lstCategory->Theme = 'web-vauu';
        $this->lstCategory->Width = '100%';
        $this->lstCategory->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstCategory->addItem(t('- Select one category -'), null, true);
        $this->lstCategory->addItems($this->lstCategory_GetItems());
        $this->lstCategory->SelectedValue = $this->objArticle->CategoryId;
        $this->lstCategory->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->lstCategory->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->lstCategory->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->lstCategory->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View');
        $this->lblTitleSlug->addCssClass('col-md-3');
        $this->lblTitleSlug->setCssStyle('font-weight', 400);

        if ($this->txtTitle->Text) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                '/' . QString::sanitizeForUrl($this->objMenuContent->MenuText) . '/' . $this->objArticle->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
        } else {
            $this->txtTitleSlug = new Bs\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->txtContent = new Q\Plugin\CKEditor($this);
        $this->txtContent->Text = $this->objArticle->Content;
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
        $this->calPostDate->Text = $this->objArticle->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->calPostDate->setCssStyle('font-weight', 'bold');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objArticle->PostUpdateDate ? $this->objArticle->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'bold');

        //$this->lblPicture;
        //$this->txtPicture;

        if (!$this->objArticle->Picture) {
            $this->lblPictureDescription = new Q\Plugin\Control\Label($this);
            $this->lblPictureDescription->Text = t('Picture description');

            $this->txtPictureDescription = new Bs\TextBox($this);
            $this->txtPictureDescription->Text = $this->objArticle->PictureDescription;
            $this->txtPictureDescription->MaxLength = Article::PictureMaxLength;
            $this->txtPictureDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtPictureDescription->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
            $this->txtPictureDescription->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
            $this->txtPictureDescription->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtPictureDescription->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

            $this->lblAuthorSource = new Q\Plugin\Control\Label($this);
            $this->lblAuthorSource->Text = t('Author/source');

            $this->txtAuthorSource = new Bs\TextBox($this);
            $this->txtAuthorSource->Text = $this->objArticle->AuthorSource;
            $this->txtAuthorSource->MaxLength = Article::AuthorSourceMaxLength;
            $this->txtAuthorSource->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
            $this->txtAuthorSource->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
            $this->txtAuthorSource->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
            $this->txtAuthorSource->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        }

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 0 => t('Hidden'), 2 => t('Draft')]);
        $this->lstStatus->SelectedValue = $this->objMenuContent->IsEnabled;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->AddAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'lstStatus_Click'));

        if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->lstStatus->Enabled = false;
        }

        $this->lblConfirmationAsking = new Q\Plugin\Control\Label($this);
        $this->lblConfirmationAsking->Text = t('Confirmation of publication');

        $this->chkConfirmationAsking = new Q\Plugin\Control\Checkbox($this);
        $this->chkConfirmationAsking->Checked = $this->objArticle->ConfirmationAsking;
        $this->chkConfirmationAsking->LinkedNode = QQN::Article()->ConfirmationAsking;
        $this->chkConfirmationAsking->WrapperClass = 'checkbox checkbox-orange';

        $this->createButtons();

        $this->modal1 = new Bs\Modal($this);
        $this->modal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete this menu content with specific metadata of this page?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
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
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnSave = new Q\Plugin\Control\Button($this);
        if ($this->objArticle->getContent()) {
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
        if ($this->objArticle->getContent()) {
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

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function lstContentTypeObject_GetItems()
    {
        $strContentTypeArray = ContentType::nameArray();
        unset($strContentTypeArray[1]);
        return $strContentTypeArray;
    }

    public function lstCategory_GetItems()
    {
        $a = array();
        $objCondition = $this->objCategoryCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objCategoryCursor = CategoryOfArticle::queryCursor($objCondition, $this->objCategoryClauses);

        // Iterate through the Cursor
        while ($objCategory = CategoryOfArticle::instantiateCursor($objCategoryCursor)) {
            $objListItem = new ListItem($objCategory->__toString(), $objCategory->Id);
            if (($this->objArticle->Category) && ($this->objArticle->Category->Id == $objCategory->Id))
                $objListItem->Selected = true;
            $a[] = $objListItem;
        }
        return $a;
    }

    public function lstStatus_Click(ActionParams $params)
    {
        if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->modal2->showDialogBox();
        }
    }

    public function lstClassNames_Change(ActionParams $params)
    {
        if ($this->objMenuContent->getContentType() !== $this->lstContentTypes->SelectedValue) {
            $this->modal1->showDialogBox();
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
            $this->objMenuContent->setIsRedirect(null);
            if ($this->objMenuContent->getRedirectUrl()) {
                $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            } else {
                $this->objMenuContent->setIsEnabled(0);
            }
            $this->objMenuContent->save();
            $this->objArticle->delete();
            $this->objMetadata->delete();

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
        $this->modal1->hideDialogBox();
    }

    public function btnMenuSave_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text && $this->txtTitle->Text) {

            $this->objArticle->setTitle($this->txtTitle->Text);
            $this->objArticle->setCategoryId($this->lstCategory->SelectedValue);
            $this->objArticle->setTitleSlug(QString::sanitizeForUrl($this->txtTitle->Text));
            $this->objArticle->setContent($this->txtContent->Text);
            $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
            if ($this->chkConfirmationAsking->Checked) {
                $this->objArticle->setConfirmationAsking(1);
            } else {
                $this->objArticle->setConfirmationAsking(0);
            }
            $this->objArticle->save();

            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->setRedirectUrl('/'. QString::sanitizeForUrl($this->objMenuContent->MenuText) .
                '/' .  QString::sanitizeForUrl($this->objArticle->TitleSlug));
            $this->objMenuContent->save();

            $this->txtExistingMenuText->Text = $this->objMenuContent->getMenuText();
            $this->txtExistingMenuText->refresh();

            $this->calPostUpdateDate->Text = $this->objArticle->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            if ($this->txtTitle->Text) {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    '/' . QString::sanitizeForUrl($this->objMenuContent->MenuText) . '/' . $this->objArticle->getTitleSlug();
                $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            if ($this->objArticle->getContent()) {
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
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the menu title and content title must exist!');
        }
    }

    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text && $this->txtTitle->Text) {
            $this->objArticle->setTitle($this->txtTitle->Text);
            $this->objArticle->setCategoryId($this->lstCategory->SelectedValue);
            $this->objArticle->setTitleSlug(QString::sanitizeForUrl($this->txtTitle->Text));
            $this->objArticle->setContent($this->txtContent->Text);
            $this->objArticle->setPostUpdateDate(Q\QDateTime::Now());
            if ($this->chkConfirmationAsking->Checked) {
                $this->objArticle->setConfirmationAsking(1);
            } else {
                $this->objArticle->setConfirmationAsking(0);
            }
            $this->objArticle->save();

            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            if (is_null($this->objArticle->getTitle())) {
                $this->objMenuContent->setIsEnabled(0);
            } else {
                $this->objMenuContent->setIsEnabled($this->lstStatus->SelectedValue);
            }
            $this->objMenuContent->setContentType($this->lstContentTypes->SelectedValue);
            $this->objMenuContent->setRedirectUrl('/'. QString::sanitizeForUrl($this->objMenuContent->MenuText) .
                '/' . $this->objArticle->getTitleSlug());
            $this->objMenuContent->save();

            $this->redirectToListPage();
        } else {
            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->txtMenuText->focus();
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_DANGER);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the menu title and content title must exist!');
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