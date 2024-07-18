<div class="col-md-12 text-center bg-light p-2 uploader" id="cms-uploader">
    <p class="text-center"><img title="Click here to upload image" onclick="uploader.trigger('{{$id}}', false, '{{!empty($preview) ? $preview : "image-preview"}}')" class="img-responsive mw-100 img-thumbnail" src="{{!empty($image) ? $image : '/images/default.webp'}}" id="{{!empty($preview) ? $preview : 'image-preview'}}"></p>
    <div class="row">
        <div class="col-md-5 text-end">
            <div class="form-group">
                <div class="form-block">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="status" class="custom-control-input use-original" id="{{!empty($useOriginalId) ? $useOriginalId : 'use-original'}}">
                        <label class="custom-control-label" for="{{!empty($useOriginalId) ? $useOriginalId : 'use-original'}}">{{__('Dùng ảnh gốc')}}</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 text-center">
            <input type="hidden" class="crop-ratio" value="{{isset($ratio) ? $ratio : '191x100'}}">
            <div class="dropdown">
                <a class="badge bg-info dropdown-toggle" href="javascript:void(0)" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                    - Ratio: 191x100 -
                </a>

                <div class="dropdown-menu crop-ratio-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" onclick="uploader.setCropData(this)" data-type="1x1" href="javascript:void(0)">Ratio: 1:1</a>
                    <a class="dropdown-item" onclick="uploader.setCropData(this)" data-type="4x3" href="javascript:void(0)">Ratio: 4:3</a>
                    <a class="dropdown-item" onclick="uploader.setCropData(this)" data-type="3x2" href="javascript:void(0)">Ratio: 3:2</a>
                    <a class="dropdown-item" onclick="uploader.setCropData(this)" data-type="16x9" href="javascript:void(0)">Ratio: 16:9</a>
                    <a class="dropdown-item" onclick="uploader.setCropData(this)" data-type="191x100" href="javascript:void(0)">Ratio: 191x100</a>
                    <a class="dropdown-item" onclick="uploader.setCropData(this)" data-type="2x3" href="javascript:void(0)">Ratio: 2:3</a>
                    <a class="dropdown-item" onclick="uploader.setCropData(this)" data-type="8x5" href="javascript:void(0)">Ratio: 8:5</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 text-start">
            @if(App\Helpers\Common::isBackEnd())
            <a href="javascript:void(0)" onclick="admin.mediaIndex('{{!empty($preview) ? $preview : "image-preview"}}', '{{$id}}')" class="badge bg-info"><i class="fas fa-image"></i> {{__('Album')}}</a>
            @endif
            <a href="javascript:void(0)" onclick="uploader.previewID = '{{!empty($preview) ? $preview : "image-preview"}}'; admin.showCropPopup('', '{{$id}}')" class="badge bg-danger"><i class="fas fa-cut"></i> {{__('Cắt ảnh')}}</a>
        </div>
    </div>
</div>
