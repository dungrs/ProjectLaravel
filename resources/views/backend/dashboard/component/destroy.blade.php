
@csrf
@method('DELETE')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-5">
            <div class="panel-head">
                <div class="panel-title">{{ __('messages.generalTitle') }}</div>
                <div class="panel-description">
                    <p>{{ __('messages.generalDescription') }}<span class="text-danger">{{ $model->name }}</span></p>
                    <p>{{ __('messages.generalAlert') }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin chung</h5>
                </div>
                <div class="ibox-content">
                    <div class="row mb15">
                        <div class="col-lg-12">
                            <div class="form-row">
                                <label for="" class="control-label text-left">{{ __('messages.tableName') }}
                                    <span class="text-danger">(*)</span>
                                </label>
                                <input type="text" name="name" value="{{ old('email', ($model->name ?? '')) }}" class="form-control" placeholder="" autocomplete="off" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="text-right mb15">
        <button class="btn btn-danger" type="submit" name="send" value="send">{{ __('messages.deleteButton') }}</button>
    </div>
</div>