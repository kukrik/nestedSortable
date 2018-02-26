<?php require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>

<?php $this->RenderBegin(); ?>

	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">

			<!-- BEGIN PAGE CONTENT-->

			<div class="row">
				<div class="col-md-12">

					<div class="content-body">
						<div class="panel-heading">
							<h3 class="panel-title">Menu Management</h3>

							<div class="row">

								<div class="panel-addmenu">
									<div class="form-group col-md-2 center-button">
										<a href="#" class="btn btn-orange create" title="" role="button">
											<i class="fa fa-plus"></i> Add Menu Item</a>
									</div>
								</div>

								<div class="panel-textarea">
									<div class="form-group col-md-5">
										<input class="form-control" type="text" placeholder="Menu Text" />
									</div>

									<div class="form-group col-md-7 center-button">
										<a href="#" class="btn btn-orange center-button" title="" role="button"> Save</a>
										<a href="#" class="btn btn-default center-button back" title="" role="button"> Cancel</a>
									</div>
								</div>

							</div>

							<div class="row">
								<div class="form-group col-md-6 center-button">
									<a href="#" id="collapse-all" class="btn btn-default center-button" title="" role="button"><i class="fa fa-minus"></i> Collapse All</a>
									<a href="#" id="expand-all" class="btn btn-default center-button" title="" role="button"><i class="fa fa-plus"></i> Expand All</a>
								</div>
							</div>

						</div>

						<!-- MENU CONTAINER BEGIN -->

						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-success alert-dismissible" role="alert" style="display: block;">
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span></button>
										<strong>Well done!</strong> Order have been changed and saved.
									</div>
								</div>
							</div>

							<!-- MENU BEGIN -->

             <!-- SIIA TULEB MENUU  -->

							<?php $this->dlgSorterTable->render(); ?>



							<!-- MENU END -->
						</div>

						<!-- MENU CONTAINER BEGIN -->
					</div>
				</div>
			</div>


			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- BEGIN CONTENT -->



<?php $this->RenderEnd(); ?>
<?php require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>