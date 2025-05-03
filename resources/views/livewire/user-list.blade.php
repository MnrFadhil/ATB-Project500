<div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User List</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex">
            <h6 class="m-0 font-weight-bold">
                All Users Data
            </h6>
        </div>
        <div class="card-body">
            <div class="input-group input-group-sm mb-4 float-right" style="width: 250px">
                <div class="input-group-prepend">
                    <span class="input-group-text">Search</span>
                </div>
                <input wire:model.live="search" type="text" class="form-control" aria-label="Small"
                    aria-describedby="inputGroup-sizing-sm">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Address</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->role }}</td>
                                <td>{{ $user->address }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button class="btn btn-warning btn-sm" data-toggle="tooltip"
                                            data-placement="left" title="Edit User">
                                            <i class="fas fa-pencil" style="color: white"></i>
                                        </button>
                                        <button class="btn btn-danger ml-2 btn-sm" data-toggle="tooltip"
                                            data-placement="left" title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No Data Available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
