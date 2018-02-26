<?php
require('qcubed.inc.php');

use QCubed\Control\FormBase as QForm;
use QCubed\Query\QQ;
//use QCubed\Plugin;
use QCubed\Project\Application;


class SampleForm extends QForm {

	protected $dlgSorterTable;

	protected function formCreate() {
		parent::formCreate();

		/*$objSorterArray = Sorter::LoadAll(QQ::Clause(QQ::OrderBy(
			QQN::Sorter()->Order)
		));*/

		$objSorterArray = Menu::LoadAll([QQ::expand(QQN::content()->MenuText)]);


		// NestedSortable
		$this->dlgSorterTable = new \QCubed\Plugin\NestedSortable($this);

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
		$this->dlgSorterTable->MaxLevels = 3;
		$this->dlgSorterTable->IsTree = true;
		$this->dlgSorterTable->ExpandOnHover = 700;
		$this->dlgSorterTable->StartCollapsed = false;

		$this->dlgSorterTable->AutoRenderChildren = true;
		$this->dlgSorterTable->TagName = $this->ListType;
		$this->dlgSorterTable->CssClass = 'sortable ui-sortable';


		foreach ($objSorterArray as $objSorter) {
			$pnl = new \QCubed\Plugin\MenuPanel($this->dlgSorterTable);
			$pnl->Id = $objSorter->getId();
			$pnl->Text = $objSorter->getMenuText();
			//$pnl->TagName = 'ul';

		}
		$this->dlgSorterTable->addAction(new \QCubed\Jqui\Event\SortableStop(), new \QCubed\Action\Ajax('sortable_stop'));
	}

	public function sortable_stop($strFormId, $strControlId, $strParameter) {

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
