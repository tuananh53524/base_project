@extends('dashboard.raw')
@section('title', "Quản lý media")
@section('content')

    <div class="row">
        <div class="col-md-12 alert alert-info">
            <form method="POST" action="{{route('media.upload')}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input form-control form-control-sm" id="customFile" name="files[]" multiple>
                            <label class="custom-file-label" for="customFile">{{__('Select files: ')}} (image, video, audio)</label>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <input type="text" value="{{!empty($filter['folder']) ? $filter['folder'] : ''}}" class="form-control form-control-sm" placeholder="{{__('Enter folder')}}" name="folder">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-info btn-sm" type="submit"><i class="fas fa-upload"></i> {{__('Upload file')}}</button>
                        <a href="{{route('dashboard')}}" class="btn btn-secondary btn-sm"><i class="fas fa-home"></i> Home</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr/>

    <form method="GET" action="{{route('media.index')}}">
        @csrf
        <div class="row">
            <div class="col-md-5">
                <input type="text" value="{{!empty($filter['keyword']) ? $filter['keyword'] : ''}}" class="form-control form-control-sm" placeholder="{{__('keyword')}}" name="keyword">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="type">
                    <option value="9">-- {{__('Type of media')}} --</option>
                    <option {{(isset($filter['type']) && $filter['type'] == 0) ? 'selected' : '' }} value="0">Image</option>
                    <option {{(isset($filter['type']) && $filter['type'] == 1) ? 'selected' : '' }} value="1">Video</option>
                    <option {{(isset($filter['type']) && $filter['type'] == 2) ? 'selected' : '' }} value="1">Audio</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-info btn-sm">
                    <i class="fas fa-search"></i> {{__('Search')}}
                </button>

                <a href="/admin/media/index" class="btn btn-primary btn-sm"><i class="fas fa-list-ul"></i> {{__('All')}}</a>
                <a href="/admin/media/index?view=tree" class="btn btn-primary btn-sm"><i class="fas fa-folder"></i> {{__('Folder')}}</a>
            </div>
        </div>
    </form>

    <div class="row mt-3">
        <div class="col">
            {{ $records->links() }}
        </div>
    </div>

    <hr/>

    @if(!empty($filter['type']))
        <div class="row">
            <div class="col-md-12">
                <small class="text-danger font-italic">{{__('Click filename for choosing')}}</small>
            </div>
        </div>
    @endif

    <div class="row bg-light">
        @if (!empty($records))
            @if($view == 'tree')
                <div class="col-lg-3 col-md-3 col-sm-4 mb-4">
                    <div class="form-group text-center">
                        <a href="/admin/media/index?folder={{!empty($record->folder) ? $record->folder : ''}}"><i class="fas fa-4x fa-folder-open"></i></a>
                        <p><span class="text-muted font-weight-bold">{{!empty($record->folder) ? $record->folder : ''}}</span></p>
                    </div>
                </div>
            @else
                @foreach ($records as $record)
                    <div class="col-lg-3 col-md-3 col-sm-4 mb-4">
                        <div class="form-group text-center">
                            <figure class="figure">
                                @if(empty($record->type))
                                    <img class="img-thumbnail img-responsive" title="{{$record->name}}"
                                         src="{{$record->thumb}}"
                                         onclick="admin.selectFiles('{{$record->thumb}}', '{{$record->name}}', '{{$record->id}}', '{{$record->type}}');">
                                @elseif ($record->type == 1)
                                    <div class="bg-info">
                                        <video class="mw-100" controls>
                                            <source src="/stream/watch/{{$record->id}}" type="video/mp4">
                                        </video>
                                    </div>
                                @elseif ($record->type == 2)
                                    <div class="bg-info">
                                        <audio class="mw-100" controls>
                                            <source src="/stream/watch/{{$record->id}}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @endif

                                <figcaption class="figure-caption"
                                            onclick="admin.selectFiles('{{$record->path}}', '{{$record->name}}', '{{$record->id}}', '{{$record->type}}');">
                                    @if(strlen($record->name) > 40)
                                        <span class="badge badge-info"
                                              style="cursor: pointer">{{__('Select me')}}</span>
                                    @else
                                        <small class="text-info font-weight-bold"
                                               style="cursor: pointer">{{$record->name}}</small>
                                    @endif
                                </figcaption>
                            </figure>
                        </div>
                    </div>
                @endforeach
            @endif
        @else
            <div class="col text-center">
                <h3>{{__('No results for')}}: <span class="badge badge-info">{{$keyword}}</span></h3>
            </div>
        @endif
    </div>

    <hr/>

    <div class="row">
        <div class="col">
            {{ $records->links() }}
        </div>
    </div>

@endsection
