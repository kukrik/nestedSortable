<div class="form-horizontal">
    <div class="form-body">
        <div class="row equal">
            <div class="col-md-9 left-box padded-wrapper">
                <div class="form-group">
                    <?= _r($this->lblExistingMenuText); ?>
                    <div class="col-md-7">
                        <?= _r($this->txtExistingMenuText); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblMenuText); ?>
                    <div class="col-md-7">
                        <?= _r($this->txtMenuText); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblContentType); ?>
                    <div class="col-md-7">
                        <?= _r($this->lstContentTypes); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblErrorTitle); ?>
                    <div class="col-md-7">
                        <?= _r($this->txtErrorTitle); ?>
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
                    <?= _r($this->lblStatus); ?>
                    <?= _r($this->lstStatus); ?>
                </div>
            </div>
        </div>
    </div>
</div>






