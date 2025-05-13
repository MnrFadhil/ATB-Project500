<div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Monitoring</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">
                    Record Datas
                </h6>
            </div>
            <div>
                <a href="/monitoring/create" wire:navigated class="btn btn-sm btn-primary shadow-sm"><i
                        class="fas fa-add fa-sm text-white"></i>
                    <span class="d-none d-sm-inline-block">Add Record</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Jam</th>
                            <th>Shift</th>
                            <th>Note</th>
                            <th style="width: 10%">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shifts as $shift)
                            <tr>
                                <td>{{ substr($shift->start_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }}</td>
                                <td>{{ strtoupper($shift->shift) }}</td>
                                <td>{{ $shift->notes ? $shift->notes : '-' }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="/monitoring/{{ $shift->id }}" wire:navigated type="button"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-info-circle" style="color: white"></i>
                                        </a>
                                        <a href="/monitoring/{{ $shift->id }}/edit" type="button"
                                            class="btn btn-warning ml-2 btn-sm">
                                            <i class="fas fa-pencil" style="color: white"></i>
                                        </a>
                                        <button wire:click="showConfirmDelete({{ $shift }})" type="button"
                                            class="btn btn-danger ml-2 btn-sm">
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
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">
                        Test
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete <strong
                        class="text-uppercase">{{ $shiftDetail ? $shiftDetail['shift'] : '' }}</strong>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" wire:click="deleteShift()" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        window.addEventListener('show-modal-detail', () => {
            $('#detailModal').modal('show');
        });

        window.addEventListener('close-modal-detail', () => {
            $('#detailModal').modal('hide');
        });
    </script>
@endscript
