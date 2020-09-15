<?php $strPageTitle = t('News edit'); ?>

<?php require('header.inc.php'); ?>
<?php $this->RenderBegin(); ?>

<div class="page-content-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <div class="content-body">
                    <div class="panel-heading">
                        <h3 class="vauu-title-3 margin-left-0"><?php _t('News edit') ?></h3>

                    </div>

                    <div class="panel-body">
                        <div class="form-horizontal" style="padding: 0 5px;">

                            <!--<div class="form-body"  style="padding: 0 -25px;">-->

                        <div class="row">
                            <div class="col-md-12">
                                <?= _r($this->lblMessage); ?>
                            </div>
                        </div>

                        <div class="row equal">
                            <div class="col-md-9 left-box padded-wrapper">






                                <div class="form-group">
                                    <?= _r($this->lblTitle); ?>
                                    <div class="col-md-7">
                                        <?= _r($this->txtTitle); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <?= _r($this->lblCategory); ?>
                                    <div class="col-md-7">
                                        <?= _r($this->lstCategory); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <?= _r($this->lblTitleSlug); ?>
                                    <div class="col-md-9">
                                        <?= _r($this->txtTitleSlug); ?>
                                    </div>
                                </div>

                                <script>
                                    ckConfig = {
                                        skin: 'moono',
                                        width: '100%',
                                        height: '350px'
                                    };
                                </script>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <?= _r($this->txtContent); ?>
                                    </div>
                                </div>

                                <div class="form-group padded-form-actions">
                                    <div class="col-md-12">
                                        <?= _r($this->btnSave); ?>
                                        <?= _r($this->btnSaving); ?>
                                        <?= _r($this->btnCancel); ?>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-3 right-box padded-wrapper">

                                <div class="form-group">
                                    <?= _r($this->lblPostDate); ?>
                                    <?= _r($this->calPostDate); ?>
                                </div>

                                <div class="form-group">
                                    <?= _r($this->lblPostUpdateDate); ?>
                                    <?= _r($this->calPostUpdateDate); ?>
                                </div>

                                <div class="form-group">
                                    <img src="../assets/images/empty-images-icon.png" class="img-responsive" alt="" style="margin-top: 15px; margin-bottom: 12px;">
                                    <!--<span id="">
                                        <label class="control-label" style="font-weight: bold; ">assets/hands_1.png</label>
                                    </span>-->

                                    <?= _r($this->lblPictureDescription); ?>
                                    <?= _r($this->txtPictureDescription); ?>

                                </div>

                                <div class="form-group">
                                    <?= _r($this->lblAuthorSource); ?>
                                    <?= _r($this->txtAuthorSource); ?>
                                </div>

                                <div class="form-group">
                                    <?= _r($this->lblStatus); ?>
                                    <?= _r($this->lstStatus); ?>
                                </div>

                                <div class="form-group">
                                    <?= _r($this->lblConfirmationAsking); ?>
                                    <?= _r($this->chkConfirmationAsking); ?>
                                </div>

                            </div>
                        </div>

                       <!--</div>-->
                            </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->RenderEnd(); ?>

<?php require('footer.inc.php'); ?>

<?php // require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>






