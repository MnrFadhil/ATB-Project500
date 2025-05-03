<div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User List</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <div>
                <h6 class="m-0 font-weight-bold">
                    All Users Data
                </h6>
            </div>
            <div>
                <button wire:click="createUser()" class=" btn btn-sm btn-primary shadow-sm"><i
                        class="fas fa-add fa-sm text-white"></i>
                    <span class="d-none d-sm-inline-block">Add User</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="input-group input-group-sm mb-4 float-right" style="width: 220px">
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
                            <th style="width: 10%">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->name }}</td>
                                <td class="text-capitalize">{{ $user->role }}</td>
                                <td>{{ $user->address }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button wire:click="detailUser({{ $user }})" type="button"
                                            class="btn btn-info btn-sm" data-placement="left" title="Edit User">
                                            <i class="fas fa-info-circle" style="color: white"></i>
                                        </button>
                                        <button wire:click="updateUser({{ $user }})" type="button"
                                            class="btn btn-warning ml-2 btn-sm" data-placement="left" title="Edit User">
                                            <i class="fas fa-pencil" style="color: white"></i>
                                        </button>
                                        <button wire:click="deleteUser({{ $user }})" type="button"
                                            class="btn btn-danger ml-2 btn-sm" data-toggle="tooltip"
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

    {{-- Form Modal --}}
    <div wire:ignore.self class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formLabel">{{ $mode == 'edit' ? 'Edit' : 'Create' }} User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form wire:submit.prevent="submit" class="modal-body">
                    <div class="form-group">
                        <label for="name-id">Name</label>
                        <input wire:model="name" type="text" class="form-control" id="name-id">
                        @error('name')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="username-id">Username</label>
                        <input wire:model="username" type="text" class="form-control" id="username-id">
                        @error('username')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email-id">Email</label>
                        <input wire:model="email" type="email" class="form-control" id="email-id">
                        @error('email')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="address-id">Address</label>
                        <input wire:model="address" type="text" class="form-control" id="address-id">
                    </div>
                    <div class="form-group">
                        <label for="role-id">Role</label>
                        <select wire:model="role" class="form-control" value="{{ $role }}" id="role-id">
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        @error('role')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($mode == 'edit')
                        <button type="button" wire:click="showChangePassword"
                            class=" btn btn-sm btn-primary shadow-sm w-100"> Change Password </button>
                    @endif

                    @if ($isChangePassword || $mode == 'create')
                        <div class="form-group mt-2">
                            <label for="role-id">Password</label>
                            <input wire:model="password" type="password" class="form-control" id="name-id">
                            @error('password')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                    <div class="float-right mt-3">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail User
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-3 font-weight-bold">Name</div>
                        <div class="font-weight-bold col-1">:</div>
                        <div>{{ $name }}</div>
                    </div>
                    <div class="row">
                        <div class="col-3 font-weight-bold">Username</div>
                        <div class="font-weight-bold col-1">:</div>
                        <div>{{ $username }}</div>
                    </div>
                    <div class="row">
                        <div class="col-3 font-weight-bold">Email</div>
                        <div class="font-weight-bold col-1">:</div>
                        <div>{{ $email }}</div>
                    </div>
                    <div class="row">
                        <div class="col-3 font-weight-bold">Address</div>
                        <div class="font-weight-bold col-1">:</div>
                        <div>{{ $address }}</div>
                    </div>
                    <div class="row">
                        <div class="col-3 font-weight-bold">Role</div>
                        <div class="font-weight-bold col-1">:</div>
                        <div class="text-capitalize">{{ $role }}</div>
                    </div>


                    @if ($mode == 'delete')
                        <div class="alert alert-danger mt-3 " role="alert">
                            Are you sure to delete this user data permanently
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    @if ($mode == 'delete')
                        <button type="button" wire:click="submit()" class="btn btn-danger">Delete</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        // tootip
        $(function() {
            $('[data-toggle="modal"]').tooltip();
        });


        window.addEventListener('show-modal-detail', () => {
            $('#detailModal').modal('show');
        });

        window.addEventListener('close-modal-detail', () => {
            $('#detailModal').modal('hide');
        });

        window.addEventListener('show-modal-form', () => {
            $('#formModal').modal('show');
        });

        window.addEventListener('close-modal-form', () => {
            $('#formModal').modal('hide');
        });
    </script>
@endscript
