<x-dashboard>
    <div class="row pt-3 pb-2 mb-3 border-bottom">
        <div class="col-md-12">
            <h1 class="h2">User management</h1>
        </div>
    </div>

    {!! $breadcrumb !!}

    <div class="row">
        <div class="col-md-12">
            <form action="{{route('users.update', $user->id)}}" method="post">
                <input name="_method" type="hidden" value="PUT">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email address</label>
                                <input type="email" value="{{!empty($user->email) ? $user->email : ''}}" name="email" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{__('Username')}}</label>
                                <input type="text" value="{{!empty($user->username) ? $user->username : ''}}" name="username" class="form-control form-control-sm" placeholder="{{__('Username')}}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{__('Full name')}}</label>
                                <input type="text" value="{{!empty($user->full_name) ? $user->full_name : ''}}" name="full_name" class="form-control form-control-sm" placeholder="{{__('Full name')}}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{__('Occupation')}}</label>
                                <input type="text" value="{{!empty($user->occupation) ? $user->occupation : ''}}" name="occupation" class="form-control form-control-sm" placeholder="{{__('Full name')}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{__('Facebook')}}</label>
                                <input type="text" value="{{!empty($user->facebook) ? $user->facebook : ''}}" name="facebook" class="form-control form-control-sm" placeholder="{{__('facebook')}}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{__('Zalo')}}</label>
                                <input type="text" value="{{!empty($user->zalo) ? $user->zalo : ''}}" name="zalo" class="form-control form-control-sm" placeholder="{{__('zalo')}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{__('Linkedin')}}</label>
                                <input type="text" value="{{!empty($user->linkedin) ? $user->linkedin : ''}}" name="linkedin" class="form-control form-control-sm" placeholder="{{__('linkedin')}}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{__('Pinterest')}}</label>
                                <input type="text" value="{{!empty($user->pinterest) ? $user->pinterest : ''}}" name="pinterest" class="form-control form-control-sm" placeholder="{{__('pinterest')}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{__('Instagram')}}</label>
                                <input type="text" value="{{!empty($user->instagram) ? $user->instagram : ''}}" name="instagram" class="form-control form-control-sm" placeholder="{{__('instagram')}}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <select class="form-select form-select-sm" name="role_id">
                                        <option value="" selected>-- Role --</option>
                                        @if (!empty($roles))
                                            @foreach($roles as $role)
                                                @if($role->name == config('app.roles.root') && Auth::user()->role_id > 1)
                                                    @continue
                                                @else
                                                    <option  {{$user->role_id == $role->id ? 'selected' : ''}} value="{{!empty($role->id) ? $role->id : 9}}">{{!empty($role->name) ? $role->name : 'n/a'}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox"  {{($user->status == 1) ? 'checked' : ''}} name="status" class="custom-control-input" id="statusSwitch">
                                        <label class="custom-control-label" for="statusSwitch">{{__('Active')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        @include('dashboard.image.index', [
                        'id' => 'user-avatar',
                        'image' => !empty($user->avatar) && Storage::disk('public')->exists($user->avatar) ? Storage::disk('public')->url($user->avatar) : ''
                        ])
                        <input type="hidden" id="user-avatar" class="form-control"  value="{{!empty($user->avatar) ? $user->avatar : ''}}" name="avatar">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="my-3">
                            <label class="control-label font-weight-bold" for="user-brief">Giới thiệu về tác giả</label>
                            <textarea id="user-brief" class="form-control" name="brief" rows="6">{{$user->brief}}</textarea>
                        </div>

                        <div class="my-3">
                            <label class="control-label font-weight-bold" for="user-content">Chi tiết</label>
                            <textarea id="user-content" class="form-control tinymce-selector" name="detail" rows="6">{{$user->detail}}</textarea>
                        </div>

                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-info">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                                    <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                                </svg>
                                &nbsp;{{__('Save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
            admin.initTinyMCE('textarea.tinymce-selector');
        });
    </script>

    @push('scripts')
        <script src="{{asset('js/uploader.js')}}"></script>
        <script src="{{asset('libs/tinymce/tinymce.min.js')}}"></script>
    @endpush
</x-dashboard>
