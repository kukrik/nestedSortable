<style>
    select option[disabled] { color: #ff0000; font-weight: bold }
    .select2-container--web-vauu .select2-results__option {
        padding: 4px 12px; }
</style>

<div class="form-horizontal">
    <div class="form-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?= _r($this->lblExistingMenuText); ?>
                    <div class="col-md-5">
                        <?= _r($this->txtExistingMenuText); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblMenuText); ?>
                    <div class="col-md-5">
                        <?= _r($this->txtMenuText); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblContentType); ?>
                    <div class="col-md-5">
                        <?= _r($this->lstContentTypes); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblSelectedPage); ?>
                    <div class="col-md-5">
                        <?= _r($this->lstSelectedPage); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblStatus); ?>
                    <div class="col-md-9">
                        <?= _r($this->lstStatus); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblTitleSlug); ?>
                    <div class="col-md-9">
                        <?= _r($this->txtTitleSlug); ?>
                        <?= _r($this->strDoubleRoutingInfo); ?>
                    </div>
                </div>

            </div>
        </div>
        <div class="form-actions fluid">
            <div class="col-md-offset-3 col-md-12">
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnSaving); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>