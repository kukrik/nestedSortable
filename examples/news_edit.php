<?php
require('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

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

class SampleForm extends Form
{
    protected $lblMessage;
    protected $modal1;
    protected $modal2;

    protected $lblTitle;
    protected $txtTitle;

    protected $lblCategory;
    protected $lstCategory;

    protected $lblTitleSlug;
    protected $txtTitleSlug;

    protected $txtContent;

    protected $lblPostDate;
    protected $calPostDate;

    protected $lblPostUpdateDate;
    protected $calPostUpdateDate;

    protected $lblPicture;
    protected $txtPicture;

    protected $lblPictureDescription;
    protected $txtPictureDescription;

    protected $lblAuthorSource;
    protected $txtAuthorSource;

    protected $lblStatus;
    protected $lstStatus;

    protected $lblConfirmationAsking;
    protected $chkConfirmationAsking;

    protected $btnSave;
    protected $btnSaving;
    protected $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    //protected $intNewsType;
    protected $objMenu;
    //protected $objMenuContent;
    protected $objNews;

    protected $objCategoryCondition;
    protected $objCategoryClauses;

    protected $intState;

    protected function formCreate()
    {
        //parent::formCreate();

        $this->intId = Application::instance()->context()->queryStringItem('id');
        //$this->objMenuContent = MenuContent::load($this->intId);
        $this->objNews = News::load($this->intId);

        $this->lblMessage = new Q\Plugin\Control\Alert($this);
        $this->lblMessage->Display = false;
        $this->lblMessage->FullEffect = true;
        //$this->lblMessage->HalfEffect = true;

        $this->lblTitle = new Q\Plugin\Control\Label($this);
        $this->lblTitle->Text = t('Title');
        $this->lblTitle->addCssClass('col-md-3');
        $this->lblTitle->setCssStyle('font-weight', 400);
        $this->lblTitle->Required = true;

        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Title');
        $this->txtTitle->Text = $this->objNews->Title;
        $this->txtTitle->addWrapperCssClass('center-button');
        $this->txtTitle->MaxLength = Article::TitleMaxLength;
        $this->txtTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnMenuSave_Click'));
        $this->txtTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnMenuCancel_Click'));
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
        //if (!$this->objNews->getNewsCategoryId()) {
            $this->lstCategory->addItem(t('- Select one category -'), null, true);
        //}
        $this->lstCategory->addItems($this->lstCategory_GetItems());
        $this->lstCategory->SelectedValue = $this->objNews->NewsCategoryId;
        $this->lstCategory->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnMenuSave_Click'));
        $this->lstCategory->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->lstCategory->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnMenuCancel_Click'));
        $this->lstCategory->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View');
        $this->lblTitleSlug->addCssClass('col-md-3');
        $this->lblTitleSlug->setCssStyle('font-weight', 400);

        if ($this->objNews->getTitleSlug()) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objNews->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
            $this->txtTitleSlug->HtmlEntities = false;
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
        } else {
            $this->txtTitleSlug = new Bs\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->txtContent = new Q\Plugin\CKEditor($this);
        $this->txtContent->Text = $this->objNews->Content;
        $this->txtContent->Rows = 10;
        $this->txtContent->Columns = 80;
        $this->txtContent->Configuration = 'ckConfig';
        $this->txtContent->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnMenuSave_Click'));
        $this->txtContent->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtContent->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnMenuCancel_Click'));
        $this->txtContent->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Published');

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->Text = $this->objNews->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss');
        $this->calPostDate->setCssStyle('font-weight', 'bold');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->Text = $this->objNews->PostUpdateDate ? $this->objNews->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'bold');

        //$this->lblPicture;
        //$this->txtPicture;

        if (!$this->objNews->Picture) {
            $this->lblPictureDescription = new Q\Plugin\Control\Label($this);
            $this->lblPictureDescription->Text = t('Picture description');

            $this->txtPictureDescription = new Bs\TextBox($this);
            $this->txtPictureDescription->Text = $this->objNews->PictureDescription;
            $this->txtPictureDescription->MaxLength = Article::PictureMaxLength;
            $this->txtPictureDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtPictureDescription->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnMenuSave_Click'));
            $this->txtPictureDescription->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
            $this->txtPictureDescription->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnMenuCancel_Click'));
            $this->txtPictureDescription->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

            $this->lblAuthorSource = new Q\Plugin\Control\Label($this);
            $this->lblAuthorSource->Text = t('Author/source');

            $this->txtAuthorSource = new Bs\TextBox($this);
            $this->txtAuthorSource->Text = $this->objNews->AuthorSource;
            $this->txtAuthorSource->MaxLength = News::AuthorSourceMaxLength;
            $this->txtAuthorSource->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnMenuSave_Click'));
            $this->txtAuthorSource->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
            $this->txtAuthorSource->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnMenuCancel_Click'));
            $this->txtAuthorSource->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        }

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 0 => t('Hidden'), 2 => t('Draft')]);
        $this->lstStatus->SelectedValue = $this->objNews->IsEnabled;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->AddAction(new Q\Event\Click(), new Q\Action\Ajax('lstStatus_Click'));

        /*if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->lstStatus->Enabled = false;
        }*/

        $this->lblConfirmationAsking = new Q\Plugin\Control\Label($this);
        $this->lblConfirmationAsking->Text = t('Confirmation of publication');

        $this->chkConfirmationAsking = new Q\Plugin\Control\Checkbox($this);
        $this->chkConfirmationAsking->Checked = $this->objNews->ConfirmationAsking;
        $this->chkConfirmationAsking->LinkedNode = QQN::News()->ConfirmationAsking;
        $this->chkConfirmationAsking->WrapperClass = 'checkbox checkbox-orange';

        $this->createButtons();

        $this->modal1 = new Bs\Modal($this);
        $this->modal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete this news?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->modal1->Title = t('Warning');
        $this->modal1->HeaderClasses = 'btn-danger';
        $this->modal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->modal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        //$this->modal1->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('changeItem_Click'));

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
        if ($this->objNews->getContent()) {
            $this->btnSave->Text = t('Update');
        } else {
            $this->btnSave->Text = t('Save');
        }
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuSave_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSaveButtonId = $this->btnSave->ControlId;

        $this->btnSaving = new Q\Plugin\Control\Button($this);
        if ($this->objNews->getContent()) {
            $this->btnSaving->Text = t('Update and close');
        } else {
            $this->btnSaving->Text = t('Save and close');
        }
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuSaveClose_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSavingButtonId = $this->btnSaving->ControlId;

        $this->btnCancel = new Q\Plugin\Control\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuCancel_Click'));

        //print_r( $this->objMenuContent->NewsType);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////


    public function lstCategory_GetItems()
    {
        $a = array();
        $objCondition = $this->objCategoryCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objCategoryCursor = CategoryOfNews::queryCursor($objCondition, $this->objCategoryClauses);

        // Iterate through the Cursor
        while ($objNewsCategory = CategoryOfNews::instantiateCursor($objCategoryCursor)) {
            $objListItem = new ListItem($objNewsCategory->__toString(), $objNewsCategory->Id);
            //if (($this->objNews->NewsCategory) && ($this->objNews->Category->Id == $objCategory->Id))
                if (($this->objNews->NewsCategory) && ($this->objNews->NewsCategory->Id == $objNewsCategory->Id))
                $objListItem->Selected = true;
            $a[] = $objListItem;
        }
        return $a;
    }

    public function lstStatus_Click(ActionParams $params)
    {
        /*if ($this->objMenu->ParentId || $this->objMenu->Right !== $this->objMenu->Left + 1) {
            $this->modal2->showDialogBox();
        }*/
    }

    protected function btnMenuSave_Click(ActionParams $params)
    {
        if ($this->txtTitle->Text) {

            $this->objNews->setTitle($this->txtTitle->Text);
            $this->objNews->setNewsCategoryId($this->lstCategory->SelectedValue);

            $this->objNews->setTitleSlug('/'. QString::sanitizeForUrl(t('News')) .
                '/' .  QString::sanitizeForUrl($this->txtTitle->Text));

            $this->objNews->setContent($this->txtContent->Text);
            $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
            $this->objNews->setIsEnabled($this->lstStatus->SelectedValue);
            if ($this->chkConfirmationAsking->Checked) {
                $this->objNews->setConfirmationAsking(1);
            } else {
                $this->objNews->setConfirmationAsking(0);
            }
            $this->objNews->save();

            $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

            if ($this->txtTitle->Text) {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objNews->getTitleSlug();
                $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
                $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
                $this->txtTitleSlug->Text = t('Uncompleted link...');
                $this->txtTitleSlug->setCssStyle('color', '#999;');
            }

            if ($this->objNews->getContent()) {
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
            $this->txtTitle->focus();
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_DANGER);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the news title must exist!');
        }
    }

    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if ($this->txtTitle->Text) {
            $this->objNews->setTitle($this->txtTitle->Text);
            $this->objNews->setNewsCategoryid($this->lstCategory->SelectedValue);
            $this->objNews->setTitleSlug(QString::sanitizeForUrl($this->txtTitle->Text));
            $this->objNews->setContent($this->txtContent->Text);
            $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
            $this->objNews->setIsEnabled($this->lstStatus->SelectedValue);
            //$this->objNews->setRedirectUrl('/'. QString::sanitizeForUrl(NewsType::toTabsText($this->objMenuContent->NewsType)) .
            //    '/' .  QString::sanitizeForUrl($this->objNews->Title));
            if ($this->chkConfirmationAsking->Checked) {
                $this->objNews->setConfirmationAsking(1);
            } else {
                $this->objNews->setConfirmationAsking(0);
            }
            $this->objNews->save();

            $this->redirectToListPage();
        } else {
            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->txtTitle->focus();
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_DANGER);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the news title must exist!');
        }
    }

    public function btnMenuCancel_Click(ActionParams $params)
    {
        $this->redirectToListPage();
    }

    protected function redirectToListPage()
    {
        Application::redirect('news_list.php');
    }

}
SampleForm::run('SampleForm');