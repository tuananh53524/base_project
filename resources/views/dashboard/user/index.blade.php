<x-dashboard>
    <div class="row pt-3 pb-2 mb-3 border-bottom">
        <div class="col-md-3">
            <h1 class="h2">User management</h1>
        </div>
        <div class="col-md-7">

        </div>

        <div class="col-md-2 text-end">
            <div class="btn-group me-2">
                <a href="{{ route('users.create') }}" class="btn btn-sm btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-plus-square" viewBox="0 0 16 16">
                        <path
                            d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                        <path
                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                    </svg>
                    Tạo mới</a>
            </div>
        </div>
    </div>

    {!! $breadcrumb !!}

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (!$records->isEmpty())
                    @foreach ($records as $record)
                        <tr>
                            <td class="align-middle">{{ $record->id }}</td>
                            <td class="align-middle">
                                @if (!empty($record->username))
                                    <span class="badge bg-primary"><a
                                            href="{{ route('users.edit', $record->id) }}"><strong>{{ $record->username }}</strong></a></span>
                                @endif
                                <div class="">{{ $record->email }}</div>

                            </td>
                            <td class="align-middle text-center">
                                <span class="badge badge-pill bg-info">
                                    {{ $record->role_name }}
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                @if (!empty($record->status))
                                    <i class="fa-regular fa-square-check text-success"></i>
                                @else
                                    <i class="fa-solid fa-square-xmark text-danger"></i>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <a class="btn bg-info text-light btn-sm" href="{{ route('users.edit', $record->id) }}">
                                    <i class="fa-regular fa-pen-to-square"></i> Edit
                                </a>

                                <form id="delete-form-{{ $record->id }}" class="d-inline"
                                    action="{{ route('users.destroy', $record->id) }}" method="POST">
                                    <input type="hidden" name="_method" value="DELETE">
                                    @csrf
                                    <button type="button"
                                        onclick="confirm('Are you sure you want to delete this item?') ? document.getElementById('delete-form-{{ $record->id }}').submit() : false"
                                        class="btn bg-danger text-light btn-sm">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            <h5 class="text-center text-danger m-2">{{ __('Không tìm thấy bản ghi nào!') }}</h5>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

</x-dashboard>
