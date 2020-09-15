<?php
require('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Query\QQ;
use QCubed\Project\Control\Paginator;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $strPageTitle;
    protected $pnlNewsTypeObject;

    protected $lstItemsPerPage;
    protected $txtFilter;

    protected $dtgNews;

    protected $btnNew;
    protected $btnBack;

    protected $intId;
    protected $intState;
    protected $objMenuContent;
    protected $objNews;

    protected function formCreate()
    {
        parent::formCreate();

        //$this->intId = Application::instance()->context()->queryStringItem('id');
        //$this->objMenuContent = MenuContent::load($this->intId);
        //$this->objNews = News::load($this->intId);

        $this->pnlNewsTypeObject = new Q\Plugin\Control\Label($this);
        $this->pnlNewsTypeObject->Text = t('News list');
        $this->pnlNewsTypeObject->TagName = 'h3';
        $this->pnlNewsTypeObject->CssClass = 'vauu-title-3 margin-left-0';

        $this->lstItemsPerPage = new Q\Plugin\Select2($this);
        $this->lstItemsPerPage->MinimumResultsForSearch = -1;
        $this->lstItemsPerPage->Theme = 'web-vauu';
        $this->lstItemsPerPage->Width = '100%';
        $this->lstItemsPerPage->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstItemsPerPage->addItems($this->lstItemsPerPage_GetItems());
        //$this->lstItemsPerPage->SelectedValue = ////////////////;
        $this->lstItemsPerPage->AddAction(new Q\Event\Change(), new Q\Action\Ajax('lstItemsPerPage_Change'));

        $this->txtFilter = new Bs\TextBox($this);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
        $this->txtFilter->setHtmlAttribute('autocomplete', 'off');
        $this->addFilterActions();

        //$this->dtgNews = new Q\Plugin\Control\VauuTable($this);
        //$this->dtgNews->CssClass = 'table vauu-table table-responsive';
        //$this->dtgNews->addCssClass('clickable-rows');

        /*$col = $this->dtgNews->createNodeColumn(t('Picture'), QQN::News()->Picture);
        $col->CellStyler->Width = '8%';
        $col = $this->dtgNews->createNodeColumn(t('Title'), QQN::News()->Title);
        $col->CellStyler->Width = '32%';
        $col = $this->dtgNews->createNodeColumn(t('Category'), QQN::News()->NewsCategory);
        $col->CellStyler->Width = '10%';
        $col = $this->dtgNews->createCallableColumn(t('Status'), [$this, 'renderStatus']);
        $col->CellStyler->Width = '10%';
        $col->HtmlEntities = false;
        $col = $this->dtgNews->createNodeColumn(t('Created'), QQN::News()->PostDate);
        $col->OrderByClause = QQ::orderBy(QQN::news()->PostDate, false);
        $col->ReverseOrderByClause = QQ::orderBy(QQN::News()->PostDate, true);
        $col->CellStyler->Width = '15%';
        $col->Format = 'DD.MM.YYYY hhhh:mm:ss';
        $col = $this->dtgNews->createNodeColumn(t('Available from'), QQN::News()->AvailableFrom);
        $col->CellStyler->Width = '15%';
        $col->Format = 'DD.MM.YYYY hhhh:mm:ss';
        $col = $this->dtgNews->createNodeColumn(t('Author'), QQN::News()->Author);
        $col->CellStyler->Width = '10%';*/


        /*$this->dtgNews->Paginator = new Bs\Paginator($this);
        $this->dtgNews->Paginator->LabelForPrevious = t('Previous');
        $this->dtgNews->Paginator->LabelForNext = t('Next');

        $this->dtgNews->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgNews->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgNews->PaginatorAlternate->LabelForNext = t('Next');*/

        /*$this->dtgNews->ItemsPerPage = 3;
        $this->dtgNews->SortColumnIndex = 4;
        $this->dtgNews->UseAjax = true;*/

        $this->createColumns();

        //$this->createPaginator();

        $this->createButtons();

        $this->dtgNews->setDataBinder('dtgNews_Bind');
        $this->dtgNews->RowParamsCallback = [$this, 'dtgNews_GetRowParams'];
        $this->dtgNews->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\Ajax('dtgNewsRow_Click'));

    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createColumns()
    {
        $this->dtgNews = new Q\Plugin\Control\VauuTable($this);
        $col = $this->dtgNews->createNodeColumn(t('Picture'), QQN::News()->Picture);
        $col->CellStyler->Width = '8%';
        $col = $this->dtgNews->createNodeColumn(t('Title'), QQN::News()->Title);
        $col->CellStyler->Width = '32%';
        $col = $this->dtgNews->createNodeColumn(t('Category'), QQN::News()->NewsCategory);
        $col->CellStyler->Width = '10%';
        $col = $this->dtgNews->createCallableColumn(t('Status'), [$this, 'renderStatus']);
        $col->CellStyler->Width = '10%';
        $col->HtmlEntities = false;
        $col = $this->dtgNews->createNodeColumn(t('Created'), QQN::News()->PostDate);
        $col->OrderByClause = QQ::orderBy(QQN::news()->PostDate, false);
        $col->ReverseOrderByClause = QQ::orderBy(QQN::News()->PostDate, true);
        $col->CellStyler->Width = '15%';
        $col->Format = 'DD.MM.YYYY hhhh:mm:ss';
        $col = $this->dtgNews->createNodeColumn(t('Available from'), QQN::News()->AvailableFrom);
        $col->CellStyler->Width = '15%';
        $col->Format = 'DD.MM.YYYY hhhh:mm:ss';
        $col = $this->dtgNews->createNodeColumn(t('Author'), QQN::News()->Author);
        $col->CellStyler->Width = '10%';


        $this->dtgNews->CssClass = 'table vauu-table table-responsive';
        $this->dtgNews->addCssClass('clickable-rows');

        $this->createPaginator();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function createPaginator()
    {
        //$this->dtgNews = new Q\Plugin\Control\VauuTable($this);
        $this->dtgNews->Paginator = new Bs\Paginator($this);
        $this->dtgNews->Paginator->LabelForPrevious = t('Previous');
        $this->dtgNews->Paginator->LabelForNext = t('Next');

        $this->dtgNews->PaginatorAlternate = new Bs\Paginator($this);
        $this->dtgNews->PaginatorAlternate->LabelForPrevious = t('Previous');
        $this->dtgNews->PaginatorAlternate->LabelForNext = t('Next');

        $this->dtgNews->ItemsPerPage = 3;
        $this->dtgNews->SortColumnIndex = 4;
        $this->dtgNews->UseAjax = true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnNew = new Q\Plugin\Control\Button($this);
        $this->btnNew->Text = t(' Create a new post');
        $this->btnNew->Glyph = 'fa fa-plus';
        $this->btnNew->CssClass = 'btn btn-orange';
        $this->btnNew->addWrapperCssClass('center-button');
        $this->btnNew->CausesValidation = false;
        $this->btnNew->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnNew_Click'));

        $this->btnBack = new Q\Plugin\Control\Button($this);
        $this->btnBack->Text = t(' Back');
        //$this->btnBack->Glyph = 'fa fa-reply'; // fa-angle-double-left
        $this->btnBack->CssClass = 'btn btn-default';
        $this->btnBack->addWrapperCssClass('center-button');
        $this->btnBack->CausesValidation = false;
        $this->btnBack->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnBack_Click'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function lstItemsPerPage_GetItems()
    {
        return [3, 5, 10, 25, 50, 100];
    }

    public function lstItemsPerPage_Change(ActionParams $params)
    {
        $this->dtgNews->ItemsPerPage = $this->lstItemsPerPage->SelectedName;
        $this->dtgNews->refresh();
    }

    protected function addFilterActions()
    {
        $this->txtFilter->addAction(new Q\Event\Input(300), new Q\Action\Ajax('filterChanged'));
        $this->txtFilter->addActionArray(new Q\Event\EnterKey(),
            [
                new Q\Action\Ajax('FilterChanged'),
                new Q\Action\Terminate()
            ]
        );
    }

    protected function filterChanged()
    {
        $this->dtgNews->refresh();
    }

    protected function dtgNews_Bind()
    {
        $strSearchValue = $this->txtFilter->Text;
        $strSearchValue = trim($strSearchValue);

        if (is_null($strSearchValue) || $strSearchValue === '') {
            $objCondition = Q\Query\QQ::All();
        } else {
            $objCondition =  Q\Query\QQ::orCondition(
                Q\Query\QQ::like(QQN::News()->Title, $strSearchValue),
                Q\Query\QQ::like(QQN::News()->NewsCategoryId, $strSearchValue),
                Q\Query\QQ::like(QQN::News()->PostDate, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::News()->AvailableFrom, "%" . $strSearchValue . "%"),
                Q\Query\QQ::like(QQN::News()->Author, "%" . $strSearchValue . "%")
            );
        }
        $this->dtgNews->TotalItemCount = News::QueryCount($objCondition);
        $objClauses[] = $this->dtgNews->OrderByClause;
        $objClauses[] = $this->dtgNews->LimitClause;
        $this->dtgNews->DataSource = News::QueryArray($objCondition, $objClauses);
    }

    public function dtgNews_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    protected function dtgNewsRow_Click(ActionParams $params)
    {
        $intNewsId = intval($params->ActionParameter);
        Application::redirect('news_edit.php' . '?id=' . $intNewsId);
        $this->lstItemsPerPage->SelectedValue = 0;
        $this->lstItemsPerPage->refresh();
    }

    public function renderStatus(News $objNews)
    {
        if ($objNews->IsEnabled == 1) {
            return  '<i class="fa fa-circle fa-lg" aria-hidden="true" style="color: #449d44; line-height: .1;"></i>' . ' ' . t('Published');
        } elseif ($objNews->IsEnabled == 2) {
            return '<i class="fa fa-circle-o fa-lg" aria-hidden="true" style="color: #000000; line-height: .1;"></i>' . ' ' . t('Draft');
        } elseif ($objNews->IsEnabled == 3) {
            return '<i class="fa fa-circle fa-lg" aria-hidden="true" style="color: #ff0000; line-height: .1;"></i>' . ' ' . t('Hidden');
        } elseif ($objNews->IsEnabled == 4) {
            return '<i class="fa fa-circle fa-lg" aria-hidden="true" style="color: #ffb00c; line-height: .1;"></i>' . ' ' . t('Waiting');
        }
    }

/*} elseif (date("Y-m-d H:i:s") < $objNews->ExpiryDateStart->qFormat('"YYYY-MM-DD hhhh:mm:ss')) {
}*/

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function btnNew_Click(ActionParams $params)
    {
        $this->editItem();
        /*$objNews = new News();
        $objNews->setPostDate(Q\QDateTime::Now());
        $objNews->setIsEnabled(3);
        $objNews->save();
        Application::redirect('news_edit.php');*/
    }

    protected function editItem($strKey = null) {
        $strQuery = '';
        if ($strKey) {
            $strQuery =  '?intId=' . $strKey;
        }
        //$strEditPageUrl = QCUBED_FORMS_URL . '/address_edit.php' . $strQuery;
        Application::redirect('news_edit.php' . $strQuery);
    }

    public function btnBack_Click(ActionParams $params)
    {
        $this->redirectToListPage();

    }

    protected function redirectToListPage()
    {
        //if ($this->intId) {
            Application::redirect('menu_manager.php');
            //$this->intState->SaveState = false;
        //} else {
            //Application::redirect('menu_manager.php');
        //}
    }


}

SampleForm::run('SampleForm');