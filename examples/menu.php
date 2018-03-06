<?php
require('qcubed.inc.php');

error_reporting(E_ALL);

use QCubed as Q;
use QCubed\Control\ControlBase;
use QCubed\Control\FormBase as QForm;
use QCubed\Query\QQ;
use QCubed\Plugin\NestedSortable;
use QCubed\Control\HListItem;

use QCubed\Project\Application;


class SampleForm extends QForm {

	protected $dlgSorterTable;

	protected function formCreate() {
		parent::formCreate();

		$objMenuArrays = Menu::loadAll([\QCubed\Query\QQ::expand(QQN::menu()->Content)]);

		$data = array();
		foreach ($objMenuArrays as $objMenuArray) {
			$objParentsIds = $objMenuArray->ParentId;
			$data[] = $objParentsIds;
		}

		//var_export($data);

		// NestedSortable
		$this->dlgSorterTable = new NestedSortable($this);

		$this->dlgSorterTable->ForcePlaceholderSize = true;
		$this->dlgSorterTable->Handle = 'div';
		$this->dlgSorterTable->Helper =	'clone';
		$this->dlgSorterTable->ListType = 'ul';
		$this->dlgSorterTable->Items = 'li';
		$this->dlgSorterTable->Opacity = .6;
		$this->dlgSorterTable->Placeholder = 'placeholder';
		$this->dlgSorterTable->Revert = 250;
		$this->dlgSorterTable->TabSize = 25;
		$this->dlgSorterTable->Tolerance = 'pointer';
		$this->dlgSorterTable->ToleranceElement = '> div';
		$this->dlgSorterTable->MaxLevels = 4;
		$this->dlgSorterTable->IsTree = true;
		$this->dlgSorterTable->ExpandOnHover = 700;
		$this->dlgSorterTable->StartCollapsed = false;

		$this->dlgSorterTable->AutoRenderChildren = true;
		$this->dlgSorterTable->TagName = 'ul';
		$this->dlgSorterTable->CssClass = 'sortable ui-sortable';
		//$this->dlgSorterTable->setDataBinder("MenuArrays_Bind");

		foreach ($objMenuArrays as $objMenuArray) {
			$pnl = new \QCubed\Plugin\MenuPanel($this->dlgSorterTable);
			$pnl->Id = $objMenuArray->getId();
			$pnl->ParentId = $objMenuArray->getParentId();
			$pnl->Depth = $objMenuArray->getDepth();
			$pnl->Text = $objMenuArray->Content->getMenuText() . ' //Id: ' .$objMenuArray->getId(). ' //Parent: ' . $objMenuArray->getParentId();
			$pnl->TagName = 'ul';


			if (in_array($pnl->Id, $data)) {
				$pnl->setHtmlAttribute("class", "mjs-nestedSortable-expanded");
			} else {
				$pnl->setHtmlAttribute("class", "mjs-nestedSortable-leaf");
			}

		}

		//$pnl->ParentId = $objMenuArray->getParentId();
		//$pnl->Depth = $objMenuArray->getDepth();
		//$pnl->Left = $objMenuArray->getLeft();
		//$pnl->Right = $objMenuArray->getRight();

		$this->dlgSorterTable->addAction(new \QCubed\Jqui\Event\SortableStop(), new \QCubed\Action\Ajax('sortable_stop'));
	}

	protected function MenuArrays_Bind()
	{
		$this->dlgSorterTable->DataSource = Menu::loadAll();
	}

	public function sortable_stop($strFormId, $strControlId, $strParameter) {

		//$strItems = var_export($this->dlgSorterTable->ItemArray);

		$a = $this->dlgSorterTable->ItemArray;
		$strItems = join(",", $a);
		Application::displayAlert($strItems);



		/* $arr = $this->dlgSorterTable->ItemArray;
		   foreach ($arr as $order => $cids) {
			$cid = explode('_',  $cids);
			$id = end($cid);

			$objSorter = Sorter::load($id);
			$objSorter->setOrder($order);
			//$objSorter->Order = $order; //Same effect
			$objSorter->save();
		}*/
	}

}

SampleForm::run('SampleForm');
