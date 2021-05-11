<div class="form-horizontal">
    <div class="form-body">
        <div class="form-group">
            <?= _r($this->lblExistingMenuText); ?>
            <div class="col-md-5">
                <?= _r($this->txtExistingMenuText); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblMenuText); ?>
            <div class="col-md-4">
                <?= _r($this->txtMenuText); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblTitleSlug); ?>
            <div class="col-md-9">
                <?= _r($this->txtTitleSlug); ?>
            </div>
        </div>
        <div class="form-actions fluid">
            <div class="col-md-offset-3 col-md-9">
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnSaving); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>