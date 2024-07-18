<x-dashboard>
    <div class="row pt-3 pb-2 mb-3 border-bottom">
        <div class="col-md-12">
            <h1 class="h2">User management</h1>
        </div>
    </div>

    {!! $breadcrumb !!}

    <div class="row">
        <div class="col-md-12">
            <form action="{{route('users.store')}}" method="post">
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
                                <input type="email" name="email" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{__('Name')}}</label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="{{__('Name')}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{__('Phone')}}</label>
                                <input type="text" name="phone" class="form-control form-control-sm" placeholder="{{__('Phone')}}">
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
                                                    <option value="{{!empty($role->id) ? $role->id : 9}}">{{!empty($role->name) ? $role->name : 'n/a'}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" checked name="status" class="custom-control-input" id="statusSwitch">
                                        <label class="custom-control-label" for="statusSwitch">{{__('Active')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        @include('dashboard.image.index', ['id' => 'user-avatar'])
                        <input type="hidden" id="user-avatar" class="form-control" name="avatar" value="">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-5 text-center">
                            <button type="submit" class="btn btn-info">
                                <i class="fa-solid fa-floppy-disk"></i>
                                &nbsp;{{__('Save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('js/uploader.js')}}"></script>
    @endpush
</x-dashboard>
