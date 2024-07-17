<x-dashboard>
    <div class="row pt-3 pb-2 mb-3 border-bottom">
        <div class="col-md-12">
            <h1 class="h2">Dashboard</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="warning-msg" style="display: none">
                <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </symbol>
                </svg>

                <div class="alert alert-success d-flex align-items-center alert-dismissible" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                    <div>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>

            @if(Auth::check() && Auth::user()->role_id == config('app.roles.root'))
                <h3>Refresh tool</h3>
                <span class="btn btn-secondary btn-sm m-1" onclick="admin.execCommand()">Exec command</span>
            @endif
        </div>
    </div>
    <hr/>
    <form action="{{route('download.log')}}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <input name="file_name" class="form-control form-control-sm mb-3" value="laravel-{{date('Y-m-d')}}.log" placeholder="laravel-{{date('Y-m-d')}}.log">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-sm">Download Log</button>
            </div>
        </div>
    </form>
</x-dashboard>
