<?php
require('qcubed.inc.php');
require('tables/NewsTable.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Project\Application;
use QCubed\Action\ActionParams;
use QCubed\Project\Control\Paginator;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Control\ListItem;
use QCubed\Query\QQ;


class SampleForm extends Form
{
	protected $strPageTitle;
	protected $pnlNewsTypeObject;

    protected $lstItemsPerPageByAssignedUserObject;
    protected $objItemsPerPageByAssignedUserObjectCondition;
    protected $objItemsPerPageByAssignedUserObjectClauses;

    protected $lstUserChoice;
	protected $txtFilter;

	protected $dtgNews;

	protected $btnNew;
	protected $btnBack;

	protected $intId;
	protected $intState;
	protected $objMenuContent;
	protected $objNews;
    protected $objUser;

	protected function formCreate()
	{
		parent::formCreate();

        $objUserId = 9;
        $this->objUser = User::load($objUserId);

        $this->createItemsPerPage();
		$this->createFilter();
		$this->dtgNews_Create();
		$this->dtgNews->setDataBinder('BindData', $this);
		$this->createButtons();
	}

   /**
	* Creates the data grid and prepares it to be row clickable. Override for additional creation operations.
	**/
	protected function dtgNews_Create()
	{
		$this->dtgNews = new NewsTable($this);
		$this->dtgNews_CreateColumns();
		$this->createPaginators();
		$this->dtgNews_MakeEditable();
		$this->dtgNews->RowParamsCallback = [$this, "dtgNews_GetRowParams"];
        $this->dtgNews->LinkedNode = QQN::News();
		$this->dtgNews->SortColumnIndex = 4;
        $this->dtgNews->ItemsPerPage = $this->objUser->ItemsPerPageByAssignedUserObject->pushItemsPerPageNum(); //__toString();

	}

   /**
	* Calls the list connector to add the columns. Override to customize column creation.
	**/
	protected function dtgNews_CreateColumns()
	{
		$this->dtgNews->createColumns();

		if ($this->getCondition()) {
			//$this->dtgNews->renderCell = t('No matching records found');
		}
	}

    /**
     * Make the datagrid editable
     */
	protected function dtgNews_MakeEditable()
	{
		$this->dtgNews->addAction(new Q\Event\CellClick(0, null, Q\Event\CellClick::rowDataValue('value')), new Q\Action\Ajax('dtgNewsRow_Click'));
		$this->dtgNews->addCssClass('clickable-rows');
		$this->dtgNews->CssClass = 'table vauu-table table-responsive';
	}

	/**
	 * @param ActionParams $params
     */
	protected function dtgNewsRow_Click(ActionParams $params)
	{
		$intNewsId = intval($params->ActionParameter);
		Application::redirect('news_edit.php' . '?id=' . $intNewsId);
	}

    /**
     * Get row parameters for the row tag
     * 
     * @param mixed $objRowObject   A database object
     * @param int $intRowIndex      The row index
     * @return array
     */
	public function dtgNews_GetRowParams($objRowObject, $intRowIndex)
	{
		$strKey = $objRowObject->primaryKey();
		$params['data-value'] = $strKey;
		return $params;
	}

	/**
	 *
	 **/
	protected function createPaginators()
	{
		$this->dtgNews->Paginator = new Bs\Paginator($this);
		$this->dtgNews->Paginator->LabelForPrevious = t('Previous');
		$this->dtgNews->Paginator->LabelForNext = t('Next');

		$this->dtgNews->PaginatorAlternate = new Bs\Paginator($this);
		$this->dtgNews->PaginatorAlternate->LabelForPrevious = t('Previous');
		$this->dtgNews->PaginatorAlternate->LabelForNext = t('Next');

		$this->dtgNews->ItemsPerPage = 10;
		$this->dtgNews->SortColumnIndex = 4;
		$this->dtgNews->UseAjax = true;
		$this->addFilterActions();
	}

    /**
     *
     **/
    protected function createItemsPerPage()
    {
        $this->lstItemsPerPageByAssignedUserObject = new Q\Plugin\Select2($this);
        $this->lstItemsPerPageByAssignedUserObject->MinimumResultsForSearch = -1;
        $this->lstItemsPerPageByAssignedUserObject->Theme = 'web-vauu';
        $this->lstItemsPerPageByAssignedUserObject->Width = '100%';
        $this->lstItemsPerPageByAssignedUserObject->SelectionMode = Q\Control\ListBoxBase::SELECTION_MODE_SINGLE;
        $this->lstItemsPerPageByAssignedUserObject->SelectedValue = $this->objUser->ItemsPerPageByAssignedUser;
        $this->lstItemsPerPageByAssignedUserObject->LinkedNode = QQN::User()->ItemsPerPageByAssignedUserObject;
        $this->lstItemsPerPageByAssignedUserObject->addItems($this->lstItemsPerPageByAssignedUserObject_GetItems());
        $this->lstItemsPerPageByAssignedUserObject->AddAction(new Q\Event\Change(), new Q\Action\Ajax('lstItemsPerPageByAssignedUserObject_Change'));
    }

    public function lstItemsPerPageByAssignedUserObject_GetItems() {
        $a = array();
        $objCondition = $this->objItemsPerPageByAssignedUserObjectCondition;
        if (is_null($objCondition)) $objCondition = QQ::all();
        $objItemsPerPageByAssignedUserObjectCursor = ItemsPerPage::queryCursor($objCondition, $this->objItemsPerPageByAssignedUserObjectClauses);

        // Iterate through the Cursor
        while ($objItemsPerPageByAssignedUserObject = ItemsPerPage::instantiateCursor($objItemsPerPageByAssignedUserObjectCursor)) {
            $objListItem = new ListItem($objItemsPerPageByAssignedUserObject->__toString(), $objItemsPerPageByAssignedUserObject->Id);
            if (($this->objUser->ItemsPerPageByAssignedUserObject) && ($this->objUser->ItemsPerPageByAssignedUserObject->Id == $objItemsPerPageByAssignedUserObject->Id))
                $objListItem->Selected = true;
            $a[] = $objListItem;
        }
        return $a;
    }

    public function lstItemsPerPageByAssignedUserObject_Change(ActionParams $params)
    {
        $this->dtgNews->ItemsPerPage = $this->lstItemsPerPageByAssignedUserObject->SelectedName;
		$this->dtgNews->refresh();
    }

	/**
	 *
	 **/
	protected function createFilter() {
		$this->txtFilter = new Bs\TextBox($this);
		$this->txtFilter->Placeholder = t('Search...');
		$this->txtFilter->TextMode = Q\Control\TextBoxBase::SEARCH;
		$this->txtFilter->setHtmlAttribute('autocomplete', 'off');
		$this->txtFilter->addCssClass('search-box');
		$this->addFilterActions();
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

	/**
	 *	Bind Data to the list control.
	 **/
	public function bindData()
	{
		$objCondition = $this->getCondition();
		$this->dtgNews->bindData($objCondition);
	}

	/**
	 *  Get the condition for the data binder.
	 *  @return QQCondition;
	 **/
	protected function getCondition()
	{
		$strSearchValue = $this->txtFilter->Text;
		$strSearchValue = trim($strSearchValue);

		if (is_null($strSearchValue) || $strSearchValue === '') {
			 return Q\Query\QQ::all();
		} else {
			return Q\Query\QQ::orCondition(
				Q\Query\QQ::like(QQN::News()->Picture, "%" . $strSearchValue . "%"),
				Q\Query\QQ::like(QQN::News()->Title, "%" . $strSearchValue . "%"),
				Q\Query\QQ::equal(QQN::News()->Category, $strSearchValue),
				Q\Query\QQ::like(QQN::News()->Author, "%" . $strSearchValue . "%")
			);
		}
	}

	/**
	 *
	 **/
	public function createButtons()
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

	protected function btnNew_Click()
	{
		Application::redirect('news_edit.php');
	}

	public function btnBack_Click(ActionParams $params)
	{
		Application::redirect('menu_manager.php');;
	}

}
SampleForm::run('SampleForm');
