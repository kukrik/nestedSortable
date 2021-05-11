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
use QCubed\Action\Ajax;
use QCubed\Event\Change;

class SampleForm extends Form
{
    protected $dlgToastr1;
    protected $dlgToastr2;
    protected $dlgToastr3;
    protected $dlgToastr4;
    protected $dlgToastr5;
    protected $dlgToastr6;
    protected $dlgToastr7;
    protected $dlgToastr8;
    protected $dlgToastr9;
    protected $dlgToastr10;
    protected $dlgToastr11;
    protected $dlgModal1;
    protected $dlgModal2;
    protected $dlgModal3;

    protected $lblTitle;
    protected $txtTitle;

    protected $lblNewsCategory;
    protected $lstNewsCategory;

    protected $lblTitleSlug;
    protected $txtTitleSlug;

    protected $txtContent;

    protected $lblPostDate;
    protected $calPostDate;

    protected $lblPostUpdateDate;
    protected $calPostUpdateDate;

    protected $lblNewsAuthor;
    protected $txtNewsAuthor;

    protected $lblUsersAsEditors;
    protected $txtUsersAsEditors;

    protected $lblPicture;
    protected $txtPicture;

    protected $lblPictureDescription;
    protected $txtPictureDescription;

    protected $lblAuthorSource;
    protected $txtAuthorSource;

    protected $lblStatus;
    protected $lstStatus;

    protected $lblUsePublicationDate;
    protected $chkUsePublicationDate;

    protected $lblAvailableFrom;
    protected $calAvailableFrom;

    protected $lblExpiryDate;
    protected $calExpiryDate;

    protected $lblConfirmationAsking;
    protected $chkConfirmationAsking;

    protected $btnSave;
    protected $btnSaving;
    protected $btnDelete;
    protected $btnCancel;

    protected $strSaveButtonId;
    protected $strSavingButtonId;

    protected $intId;
    protected $objMenu;
    protected $objNews;

    protected $intTemporaryId;

    protected $objNewsCategoryCondition;
    protected $objNewsCategoryClauses;

    protected function formCreate()
    {
        parent::formCreate();

        $this->intId = Application::instance()->context()->queryStringItem('id');
        if (strlen($this->intId)) {
            $this->objNews = News::load($this->intId);
        } else {
            $this->objNews = new News();
        }

        /**
         * NOTE: if the user_id is stored in session (e.g. if a User is logged in), as well, for example:
         * checking against user session etc.
         *
         * Must to save something here $this->objNews->setUserId(logged user session);
         * or something similar...
         *
         * Options to do this are left to the developer.
         **/

        if (!$this->objNews->getAssignedByUserObject()) {
            // $this->intTemporaryId = $this->objNews->setAssignedByUser($_SESSION['logged_user_id'])); // Approximately example here etc...
            // For example, John Doe is a logged user with his session
            $this->intTemporaryId = $this->objNews->setAssignedByUser(1);
        } else {
            $this->intTemporaryId = $this->objNews->getAssignedByUser();
        }

        $this->lblTitle = new Q\Plugin\Control\Label($this);
        $this->lblTitle->Text = t('Title');
        $this->lblTitle->addCssClass('col-md-3');
        $this->lblTitle->setCssStyle('font-weight', 400);
        $this->lblTitle->Required = true;

        $this->txtTitle = new Bs\TextBox($this);
        $this->txtTitle->Placeholder = t('Title');
        $this->txtTitle->Text = $this->objNews->Title ? $this->objNews->Title : null;
        $this->txtTitle->LinkedNode = QQN::News()->Title;
        $this->txtTitle->addWrapperCssClass('center-button');
        $this->txtTitle->MaxLength = News::TitleMaxLength;
        $this->txtTitle->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtTitle->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtTitle->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnCancel_Click'));
        $this->txtTitle->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblNewsCategory = new Q\Plugin\Control\Label($this);
        $this->lblNewsCategory->Text = t('Category');
        $this->lblNewsCategory->addCssClass('col-md-3');
        $this->lblNewsCategory->setCssStyle('font-weight', 400);

        $this->lstNewsCategory = new Q\Plugin\Select2($this);
        $this->lstNewsCategory->MinimumResultsForSearch = -1;
        $this->lstNewsCategory->Theme = 'web-vauu';
        $this->lstNewsCategory->Width = '100%';
        $this->lstNewsCategory->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstNewsCategory->addItem(t('- Select one category -'), null, true);
        $this->lstNewsCategory->addItems($this->lstCategory_GetItems());
        $this->lstNewsCategory->SelectedValue = $this->objNews->NewsCategoryId;
        $this->lstNewsCategory->LinkedNode = QQN::News()->NewsCategoryId;

        $this->lstNewsCategory->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->lstNewsCategory->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->lstNewsCategory->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnCancel_Click'));
        $this->lstNewsCategory->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblTitleSlug = new Q\Plugin\Control\Label($this);
        $this->lblTitleSlug->Text = t('View');
        $this->lblTitleSlug->addCssClass('col-md-3');
        $this->lblTitleSlug->setCssStyle('font-weight', 400);

        if ($this->objNews->getTitleSlug()) {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->setCssStyle('font-weight', 400);
            $this->txtTitleSlug->setCssStyle('text-align', 'left;');
            $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                $this->objNews->getTitleSlug();
            $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
            $this->txtTitleSlug->LinkedNode = QQN::News()->TitleSlug;
            $this->txtTitleSlug->HtmlEntities = false;
        } else {
            $this->txtTitleSlug = new Q\Plugin\Control\Label($this);
            $this->txtTitleSlug->Text = t('Uncompleted link...');
            $this->txtTitleSlug->setCssStyle('color', '#999;');
        }

        $this->txtContent = new Q\Plugin\CKEditor($this);
        $this->txtContent->Text = $this->objNews->Content;
        $this->txtContent->LinkedNode = QQN::News()->Content;
        $this->txtContent->Rows = 10;
        $this->txtContent->Columns = 80;
        $this->txtContent->Configuration = 'ckConfig';
        $this->txtContent->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
        $this->txtContent->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtContent->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnCancel_Click'));
        $this->txtContent->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblPostDate = new Q\Plugin\Control\Label($this);
        $this->lblPostDate->Text = t('Published');
        $this->lblPostDate->setCssStyle('font-weight', 'bold');

        $this->calPostDate = new Bs\Label($this);
        $this->calPostDate->LinkedNode = QQN::News()->PostDate;
        $this->calPostDate->Text = $this->objNews->PostDate ? $this->objNews->PostDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostDate->setCssStyle('font-weight', 'normal');

        $this->lblPostUpdateDate = new Q\Plugin\Control\Label($this);
        $this->lblPostUpdateDate->Text = t('Updated');
        $this->lblPostUpdateDate->setCssStyle('font-weight', 'bold');

        $this->calPostUpdateDate = new Bs\Label($this);
        $this->calPostUpdateDate->LinkedNode = QQN::News()->PostUpdateDate;
        $this->calPostUpdateDate->Text = $this->objNews->PostUpdateDate ? $this->objNews->PostUpdateDate->qFormat('DD.MM.YYYY hhhh:mm:ss') : null;
        $this->calPostUpdateDate->setCssStyle('font-weight', 'normal');

        $this->lblNewsAuthor = new Q\Plugin\Control\Label($this);
        $this->lblNewsAuthor->Text = t('News author');
        $this->lblNewsAuthor->setCssStyle('font-weight', 'bold');

        $this->txtNewsAuthor  = new Bs\Label($this);
        $this->txtNewsAuthor->Text = $this->objNews->Author;
        $this->txtNewsAuthor->LinkedNode = QQN::News()->Author;
        $this->txtNewsAuthor->setCssStyle('font-weight', 'normal');

        $this->lblUsersAsEditors = new Q\Plugin\Control\Label($this);
        $this->lblUsersAsEditors->Text = t('Editors');
        $this->lblUsersAsEditors->setCssStyle('font-weight', 'bold');

        $this->txtUsersAsEditors  = new Bs\Label($this);
        $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->GetUserAsEditorsArray());
        $this->txtUsersAsEditors->LinkedNode = QQN::News()->UserAsEditors;
        $this->txtUsersAsEditors->setCssStyle('font-weight', 'normal');

        if ($this->objNews->countUsersAsEditors() === 0) {
            $this->lblUsersAsEditors->Display = false;
            $this->txtUsersAsEditors->Display = false;
        }

        //$this->lblPicture;
        //$this->txtPicture;

        if (!$this->objNews->getPicture()) {
            $this->lblPictureDescription = new Q\Plugin\Control\Label($this);
            $this->lblPictureDescription->Text = t('Picture description');
            $this->lblPictureDescription->setCssStyle('font-weight', 'bold');

            $this->txtPictureDescription = new Bs\TextBox($this);
            $this->txtPictureDescription->Text = $this->objNews->PictureDescription;
            $this->txtPictureDescription->LinkedNode = QQN::News()->PictureDescription;
            $this->txtPictureDescription->MaxLength = Article::PictureMaxLength;
            $this->txtPictureDescription->TextMode = Q\Control\TextBoxBase::MULTI_LINE;
            $this->txtPictureDescription->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
            $this->txtPictureDescription->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
            $this->txtPictureDescription->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnCancel_Click'));
            $this->txtPictureDescription->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

            $this->lblAuthorSource = new Q\Plugin\Control\Label($this);
            $this->lblAuthorSource->Text = t('Author/source');
            $this->lblAuthorSource->setCssStyle('font-weight', 'bold');

            $this->txtAuthorSource = new Bs\TextBox($this);
            $this->txtAuthorSource->Text = $this->objNews->AuthorSource;
            $this->txtAuthorSource->LinkedNode = QQN::News()->AuthorSource;
            $this->txtAuthorSource->MaxLength = News::AuthorSourceMaxLength;
            $this->txtAuthorSource->AddAction(new Q\Event\EnterKey(), new Q\Action\Ajax('btnSave_Click'));
            $this->txtAuthorSource->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
            $this->txtAuthorSource->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnCancel_Click'));
            $this->txtAuthorSource->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        }

        $this->lblStatus = new Q\Plugin\Control\Label($this);
        $this->lblStatus->Text = t('Status');
        $this->lblStatus->setCssStyle('font-weight', 'bold');

        $this->lstStatus = new Q\Plugin\Control\RadioList($this);
        $this->lstStatus->addItems([1 => t('Published'), 2 => t('Hidden'), 3 => t('Draft')]);
        $this->lstStatus->SelectedValue = $this->objNews->Status;
        $this->lblStatus->LinkedNode = QQN::News()->Status;
        $this->lstStatus->ButtonGroupClass = 'radio radio-orange';
        $this->lstStatus->Enabled = true;

        if ($this->objNews->getUsePublicationDate() || $this->objNews->getConfirmationAsking()) {
            $this->lstStatus->Enabled = false;
        }

        $this->lblUsePublicationDate = new Q\Plugin\Control\Label($this);
        $this->lblUsePublicationDate->Text = t('Use publication date');
        $this->lblUsePublicationDate->setCssStyle('font-weight', 'bold');

        $this->chkUsePublicationDate = new Q\Plugin\Control\Checkbox($this);
        $this->chkUsePublicationDate->Checked = $this->objNews->UsePublicationDate;
        $this->chkUsePublicationDate->LinkedNode = QQN::News()->UsePublicationDate;
        $this->chkUsePublicationDate->WrapperClass = 'checkbox checkbox-orange';
        $this->chkUsePublicationDate->addAction(new Change(), new Ajax('setUse_PublicationDate'));

        if ($this->objNews->getConfirmationAsking()) {
            $this->chkUsePublicationDate->Enabled = false;
        }

        $this->lblAvailableFrom = new Q\Plugin\Control\Label($this);
        $this->lblAvailableFrom->Text = t('Available From');
        $this->lblAvailableFrom->setCssStyle('font-weight', 'bold');

        $this->calAvailableFrom = new Q\Plugin\DateTimePicker($this);
        $this->calAvailableFrom->Language = 'ee';
        $this->calAvailableFrom->TodayHighlight = true;
        $this->calAvailableFrom->ClearBtn = true;

        $today = date('Y-m-d H:i:s');
        $this->calAvailableFrom->StartDate = $today;

        $this->calAvailableFrom->AutoClose = true;
        $this->calAvailableFrom->StartView = 2;
        $this->calAvailableFrom->ForceParse = false;
        $this->calAvailableFrom->Format = 'dd.mm.yyyy hh:ii';
        $this->calAvailableFrom->DateTimePickerType = Q\Plugin\DateTimePickerBase::DEFAULT_OUTPUT_DATETIME;
        $this->calAvailableFrom->Text = $this->objNews->AvailableFrom ? $this->objNews->AvailableFrom->qFormat('DD.MM.YYYY hhhh:mm') : null;
        $this->calAvailableFrom->LinkedNode = QQN::News()->AvailableFrom;
        $this->calAvailableFrom->addCssClass('calendar-trigger');
        $this->calAvailableFrom->ActionParameter = $this->calAvailableFrom->ControlId;
        $this->calAvailableFrom->addAction(new Change(), new Ajax('setDate_AvailableFrom'));

        $this->lblExpiryDate = new Q\Plugin\Control\Label($this);
        $this->lblExpiryDate->Text = t('Expiry Date');
        $this->lblExpiryDate->setCssStyle('font-weight', 'bold');

        $this->calExpiryDate = new Q\Plugin\DateTimePicker($this);
        $this->calExpiryDate->Language = 'ee';
        $this->calExpiryDate->ClearBtn = true;

        $tomorrow = date('Y-m-d H:i:s', strtotime('+1 day'));
        $this->calExpiryDate->StartDate = $tomorrow;

        $this->calExpiryDate->AutoClose = true;
        $this->calExpiryDate->StartView = 2;
        $this->calExpiryDate->ForceParse = false;
        $this->calExpiryDate->Format = 'dd.mm.yyyy hh:ii';
        $this->calExpiryDate->DateTimePickerType = Q\Plugin\DateTimePickerBase::DEFAULT_OUTPUT_DATETIME;
        $this->calExpiryDate->Text = $this->objNews->ExpiryDate ? $this->objNews->ExpiryDate->qFormat('DD.MM.YYYY hhhh:mm') : null;
        $this->calExpiryDate->LinkedNode = QQN::News()->ExpiryDate;
        $this->calExpiryDate->addCssClass('calendar-trigger');
        $this->calExpiryDate->ActionParameter = $this->calExpiryDate->ControlId;
        $this->calExpiryDate->addAction(new Change(), new Ajax('setDate_ExpiryDate'));

        if (!$this->objNews->getUsePublicationDate()) {
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
        }

        $this->lblConfirmationAsking = new Q\Plugin\Control\Label($this);
        $this->lblConfirmationAsking->Text = t('Confirmation of publication');
        $this->lblConfirmationAsking->setCssStyle('font-weight', 'bold');

        $this->chkConfirmationAsking = new Q\Plugin\Control\Checkbox($this);
        $this->chkConfirmationAsking->Checked = $this->objNews->ConfirmationAsking;
        $this->chkConfirmationAsking->LinkedNode = QQN::News()->ConfirmationAsking;
        $this->chkConfirmationAsking->WrapperClass = 'checkbox checkbox-orange';
        $this->chkConfirmationAsking->addAction(new Change(), new Ajax('gettingConfirmation_Click'));

        $this->createButtons();
        $this->createToastr();
        $this->createModals();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function createButtons()
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
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSave_Click'));
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
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSaveClose_Click'));
        // The variable below is being prepared for fast transmission
        $this->strSavingButtonId = $this->btnSaving->ControlId;

        $this->btnDelete = new Q\Plugin\Control\Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->CssClass = 'btn btn-danger';
        $this->btnDelete->addWrapperCssClass('center-button');
        $this->btnDelete->CausesValidation = false;
        $this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));

        $this->btnCancel = new Q\Plugin\Control\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCancel_Click'));
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
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, the news title must exist!');
        $this->dlgToastr2->ProgressBar = true;

        $this->dlgToastr3 = new Q\Plugin\Toastr($this);
        $this->dlgToastr3->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr3->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr3->Message = t('<strong>Well done!</strong> The publication date for this post has been saved or changed.');
        $this->dlgToastr3->ProgressBar = true;

        $this->dlgToastr4 = new Q\Plugin\Toastr($this);
        $this->dlgToastr4->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr4->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr4->Message = t('<strong>Well done!</strong> The expiration date for this post has been saved or changed.');
        $this->dlgToastr4->ProgressBar = true;

        $this->dlgToastr5 = new Q\Plugin\Toastr($this);
        $this->dlgToastr5->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr5->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr5->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this date \"Available from\" does not exist.</p>Please enter at least the date of publication!');
        $this->dlgToastr5->ProgressBar = true;
        $this->dlgToastr5->TimeOut = 10000;
        $this->dlgToastr5->EscapeHtml = false;

        $this->dlgToastr6 = new Q\Plugin\Toastr($this);
        $this->dlgToastr6->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr6->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr6->Message = t('<p style=\"margin-bottom: 2px;\">Start date must be smaller then end date!</p><strong>Try to do it right again!</strong>');
        $this->dlgToastr6->ProgressBar = true;
        $this->dlgToastr6->TimeOut = 10000;
        $this->dlgToastr6->EscapeHtml = false;

        $this->dlgToastr7 = new Q\Plugin\Toastr($this);
        $this->dlgToastr7->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr7->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr7->Message = t('Publication date have been canceled.');
        $this->dlgToastr7->ProgressBar = true;

        $this->dlgToastr8 = new Q\Plugin\Toastr($this);
        $this->dlgToastr8->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr8->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr8->Message = t('Expiration date have been canceled.');
        $this->dlgToastr8->ProgressBar = true;

        $this->dlgToastr9 = new Q\Plugin\Toastr($this);
        $this->dlgToastr9->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr9->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr9->Message = t('<p style=\"margin-bottom: 2px;\"><strong>Sorry</strong>, this date \"Available from\" does not exist.</p><strong>Re-enter publication date and expiration date!</strong>');
        $this->dlgToastr9->ProgressBar = true;
        $this->dlgToastr9->TimeOut = 10000;
        $this->dlgToastr9->EscapeHtml = false;

        $this->dlgToastr10 = new Q\Plugin\Toastr($this);
        $this->dlgToastr10->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr10->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr10->Message = t('<strong>Well done!</strong> The message has been sent to the editor-in-chief of the site for review, correction or approval!');
        $this->dlgToastr10->ProgressBar = true;
        $this->dlgToastr10->TimeOut = 10000;

        $this->dlgToastr11 = new Q\Plugin\Toastr($this);
        $this->dlgToastr11->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr11->PositionClass = Q\Plugin\Toastr::POSITION_TOP_CENTER;
        $this->dlgToastr11->Message = t('<strong>Well done!</strong> A message has been sent to the editor-in-chief of the site to cancel the confirmation!');
        $this->dlgToastr11->ProgressBar = true;
        $this->dlgToastr11->TimeOut = 10000;
    }

    protected function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently
                                delete this news?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal1->Title = t('Warning');
        $this->dlgModal1->HeaderClasses = 'btn-danger';
        $this->dlgModal1->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal1->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('deleteItem_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">There is nothing to delete here!
                                Please use the "Cancel" button to access the news list!</p>');
        $this->dlgModal2->Title = t("Tip");
        $this->dlgModal2->HeaderClasses = 'btn-darkblue';
        $this->dlgModal2->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Are you sure you want to cancel
                                the publication date for this post?</p>');
        $this->dlgModal3->Title = t('Warning');
        $this->dlgModal3->HeaderClasses = 'btn-danger';
        $this->dlgModal3->addButton(t("I accept"), "pass", false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal3->addButton(t("I'll cancel"), "no-pass", false, false, null,
            ['class' => 'btn btn-default']);
        $this->dlgModal3->addAction(new Q\Event\DialogButton(), new Q\Action\Ajax('cancelItem_Click'));
    }
    ///////////////////////////////////////////////////////////////////////////////////////////

    public function lstCategory_GetItems()
    {
        $a = array();
        $objCondition = $this->objNewsCategoryCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objNewsCategoryCursor = CategoryOfNews::queryCursor($objCondition, $this->objNewsCategoryClauses);

        // Iterate through the Cursor
        while ($objNewsCategory = CategoryOfNews::instantiateCursor($objNewsCategoryCursor)) {
            $objListItem = new ListItem($objNewsCategory->__toString(), $objNewsCategory->Id);
                if (($this->objNews->NewsCategory) && ($this->objNews->NewsCategory->Id == $objNewsCategory->Id))
                    $objListItem->Selected = true;
            $a[] = $objListItem;
        }
        return $a;
    }

    protected function deleteItem_Click(ActionParams $params)
    {
        if ($params->ActionParameter == "pass") {
            $this->objNews->unassociateAllUsersAsEditors();
            $this->objNews->delete();
            $this->redirectToListPage();
        }
        $this->dlgModal1->hideDialogBox();
    }

    protected function cancelItem_Click(ActionParams $params)
    {
        if ($params->ActionParameter == "pass") {

            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
            $this->lstStatus->Enabled = true;

            $this->lstStatus->SelectedValue = 2;
            $this->calAvailableFrom->Text = null;

            $this->objNews->setUsePublicationDate(0);
            $this->objNews->setStatus(2);

            $this->objNews->save();

            $this->redirectToListPage();
        } else {
            $this->dlgModal3->hideDialogBox();
            $this->calAvailableFrom->focus();
        }
    }

    public function setUse_PublicationDate(ActionParams $params)
    {
        if ($this->chkUsePublicationDate->Checked) {
            $this->lblAvailableFrom->Display = true;
            $this->calAvailableFrom->Display = true;
            $this->lblExpiryDate->Display = true;
            $this->calExpiryDate->Display = true;

            $this->lstStatus->Enabled = false;
            $this->lstStatus->SelectedValue = null;

            $this->objNews->setUsePublicationDate(1);
            $this->objNews->setStatus(4);
            $this->calAvailableFrom->focus();
        } else {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
            $this->lstStatus->Enabled = true;
            $this->lstStatus->SelectedValue = 2;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->objNews->setUsePublicationDate(0);
            $this->objNews->setStatus(2);
            $this->objNews->setAvailableFrom(null);
            $this->objNews->setExpiryDate(null);

            $this->dlgToastr7->notify();
        }

        $this->renderActionsWithOrWithoutId();

        $this->objNews->save();
    }

    protected function setDate_AvailableFrom(ActionParams $params)
    {
        if ($this->calAvailableFrom->Text) {
            $this->objNews->setAvailableFrom($this->calAvailableFrom->DateTime);

            $this->dlgToastr3->notify();
        } else {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->lstStatus->Enabled = true;
            $this->lstStatus->SelectedValue = 2;

            $this->objNews->setUsePublicationDate(0);
            $this->objNews->setStatus(2);
            $this->objNews->setAvailableFrom(null);
            $this->objNews->setExpiryDate(null);

            $this->dlgToastr7->notify();
        }
        $this->renderActionsWithOrWithoutId();

        $this->objNews->save();
    }

    protected function setDate_ExpiryDate(ActionParams $params)
    {
        if ($this->calAvailableFrom->Text && $this->calExpiryDate->Text) {
            if (new DateTime($this->calAvailableFrom->Text) > new DateTime($this->calExpiryDate->Text)) {
                $this->calExpiryDate->Text = null;
                $this->objNews->setExpiryDate(null);

                $this->dlgToastr6->notify();
            } else if ($this->calExpiryDate->Text) {
                $this->objNews->setExpiryDate($this->calExpiryDate->DateTime);

                $this->dlgToastr4->notify();
            } else {
                $this->calExpiryDate->Text = null;
                $this->objNews->setExpiryDate(null);

                $this->dlgToastr8->notify();
            }
        } else if ($this->calAvailableFrom->Text && !$this->calExpiryDate->Text) {
            $this->calExpiryDate->Text = null;
            $this->objNews->setExpiryDate(null);

            $this->dlgToastr8->notify();
        } else {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
            $this->lstStatus->Enabled = true;
            $this->lstStatus->SelectedValue = 2;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->objNews->setUsePublicationDate(0);
            $this->objNews->setStatus(2);
            $this->objNews->setAvailableFrom(null);
            $this->objNews->setExpiryDate(null);

            $this->dlgToastr9->notify();
        }

        $this->renderActionsWithOrWithoutId();

        $this->objNews->save();
    }

    protected function gettingConfirmation_Click(ActionParams $params)
    {
        // Add the code to send the message here.
        // Options to do this are left to the developer.
        //
        // Note that a proper solution must be considered here.
        // If the editor-in-chief needs to be reviewed, he should not receive messages...

        if ($this->chkConfirmationAsking->Checked) {
            $this->chkUsePublicationDate->Checked = false;
            $this->lblAvailableFrom->Display = false;
            $this->calAvailableFrom->Display = false;
            $this->lblExpiryDate->Display = false;
            $this->calExpiryDate->Display = false;
            $this->lstStatus->Enabled = false;
            $this->chkUsePublicationDate->Enabled = false;
            $this->lstStatus->SelectedValue = 2;

            $this->calAvailableFrom->Text = null;
            $this->calExpiryDate->Text = null;

            $this->objNews->setUsePublicationDate(0);
            $this->objNews->setStatus(2);
            $this->objNews->setAvailableFrom(null);
            $this->objNews->setExpiryDate(null);
            $this->objNews->setConfirmationAsking(1);

            $this->dlgToastr10->notify();
        } else {
            $this->objNews->setConfirmationAsking(0);
            $this->lstStatus->Enabled = true;
            $this->chkUsePublicationDate->Enabled = true;

            $this->dlgToastr11->notify();
        }

        $this->objNews->save();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function btnSave_Click(ActionParams $params)
    {
        $this->renderActionsWithOrWithoutId();

        if ($this->txtTitle->Text !== null) {
            $this->objNews->setTitle($this->txtTitle->Text);
            $this->objNews->setNewsCategoryId($this->lstNewsCategory->SelectedValue);
            $this->objNews->setTitleSlug('/' . QString::sanitizeForUrl(t('News')) .
                '/' . QString::sanitizeForUrl($this->txtTitle->Text));
            $this->objNews->setContent($this->txtContent->Text);
            $this->objNews->setUserNameById($this->intTemporaryId);

            if ($this->chkUsePublicationDate->Checked == false) {
                $this->objNews->setStatus($this->lstStatus->SelectedValue);
            }

            if ($this->lstNewsCategory->SelectedValue) {
                $this->objNews->setNewsCategoryById($this->objNews->getNewsCategoryId());
            }

            if ($this->chkUsePublicationDate->Checked == true && $this->calAvailableFrom->Text == null) {
                $this->chkUsePublicationDate->Checked = false;
                $this->lblAvailableFrom->Display = false;
                $this->calAvailableFrom->Display = false;
                $this->lblExpiryDate->Display = false;
                $this->calExpiryDate->Display = false;
                $this->lstStatus->Enabled = true;

                $this->lstStatus->SelectedValue = 2;
                $this->calAvailableFrom->Text = null;

                $this->objNews->setUsePublicationDate(0);
                $this->objNews->setStatus(2);

                $this->dlgToastr5->notify();
            }
            $this->objNews->save();

            $this->txtNewsAuthor->Text = $this->objNews->getAuthor();

            if ($this->objNews->getTitle()) {
                $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX .
                    $this->objNews->getTitleSlug();
                $this->txtTitleSlug->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
                $this->txtTitleSlug->HtmlEntities = false;
                $this->txtTitleSlug->setCssStyle('font-weight', 400);
            } else {
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

            $this->dlgToastr1->notify();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    public function btnSaveClose_Click(ActionParams $params)
    {
        $this->renderActionsWithOrWithoutId();

        if ($this->txtTitle->Text !== null && $this->txtTitle->Text !== '') {
            $this->objNews->setTitle($this->txtTitle->Text);
            $this->objNews->setNewsCategoryId($this->lstNewsCategory->SelectedValue);
            $this->objNews->setTitleSlug('/' . QString::sanitizeForUrl(t('News')) .
                '/' . QString::sanitizeForUrl($this->txtTitle->Text));
            $this->objNews->setContent($this->txtContent->Text);
            $this->objNews->setUserNameById($this->intTemporaryId);

            if ($this->chkUsePublicationDate->Checked == false) {
                $this->objNews->setStatus($this->lstStatus->SelectedValue);
            }

            if ($this->lstNewsCategory->SelectedValue) {
                $this->objNews->setNewsCategoryById($this->objNews->getNewsCategoryId());
            }

            if ($this->chkUsePublicationDate->Checked == true && $this->calAvailableFrom->Text == null) {
                $this->dlgModal3->showDialogBox();
            } else {
                $this->redirectToListPage();
            }

            $this->objNews->save();
        } else {
            $this->dlgToastr2->notify();
        }
    }

    public function renderActionsWithOrWithoutId()
    {
        if (strlen($this->intId)) {
            if ($this->txtTitle->Text !== $this->objNews->getTitle() ||
                $this->lstNewsCategory->SelectedValue !== $this->objNews->getNewsCategoryId() ||
                $this->txtContent->Text !== $this->objNews->getContent() ||
                // $this->txtPicture->text !== $this->objNews->getPicture() ||
                $this->txtPictureDescription->Text !== $this->objNews->getPictureDescription() ||
                $this->txtAuthorSource->Text !== $this->objNews->getAuthorSource() ||
                $this->lstStatus->SelectedValue !== $this->objNews->getStatus() ||
                $this->chkUsePublicationDate->Checked !== $this->objNews->getUsePublicationDate() ||
                $this->calAvailableFrom->Text !== $this->objNews->getAvailableFrom() ||
                $this->calExpiryDate->Text !== $this->objNews->getExpiryDate() ||
                $this->chkConfirmationAsking->Checked !== $this->objNews->getConfirmationAsking()
            ) {
                // $this->objNews->getAssignedEditorsNameById($_SESSION['logged_user_id'])); // Approximately example here etc...
                // For example, John Doe is a logged user with his session
                $this->objNews->getAssignedEditorsNameById(1);

                $this->txtUsersAsEditors->Text = implode(', ', $this->objNews->getUserAsEditorsArray());
                $this->objNews->setPostUpdateDate(Q\QDateTime::Now());
                $this->calPostUpdateDate->Text = $this->objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');

                $this->lblUsersAsEditors->Display = true;
                $this->txtUsersAsEditors->Display = true;
            }
        } else {
            $this->objNews->setStatus(3);
            $this->objNews->setUserNameById($this->intTemporaryId);
            $this->objNews->setPostDate(Q\QDateTime::Now());
            $this->objNews->setPostUpdateDate(null);
            $this->calPostDate->Text = $this->objNews->getPostDate()->qFormat('DD.MM.YYYY hhhh:mm:ss');
        }
    }
    
    public function btnCancel_Click(ActionParams $params)
    {
            $this->redirectToListPage();
    }

    public function btnDelete_Click(ActionParams $params)
    {
        if ($this->objNews->getTitle()) {
            $this->dlgModal1->showDialogBox();
        } else {
            $this->dlgModal2->showDialogBox();
        }
    }

    protected function redirectToListPage()
    {
        Application::redirect('news_list.php');
    }

}
SampleForm::run('SampleForm');