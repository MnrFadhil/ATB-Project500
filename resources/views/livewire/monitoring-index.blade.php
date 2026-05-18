<div>
    <!-- Page Heading -->
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">Monitoring</h1>

        <div class="d-flex align-items-center mb-2">
            <div class="d-sm-flex d-none align-items-center justify-content-end mr-2">
                <div class="mb-1 mt-md-0">
                    <input placeholder="Select Date" wire:model.live="date" type="date" class="form-control"
                        id="date" required>
                </div>
            </div>
            <div>
                <button wire:click="$js.modalDownloadShow" class=" btn btn-sm btn-info shadow-sm"><i
                        class="fas fa-download fa-sm text-white"></i>
                    <span class="d-none d-sm-inline-block ml-2">Download Report</span>
                </button>
            </div>
        </div>
    </div>

    <div class="d-sm-none mb-2 align-items-center justify-content-end">
        <div class="mb-1 mt-md-0">
            <input placeholder="Select Date" wire:model.live="date" type="date" class="form-control" id="date"
                required>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <div>
                <h6 class="m-0 font-weight-bold" style="color: #00664A">
                    Record Datas
                </h6>
            </div>
            <div>
                @if (!$isAdmin)
                    <a href="/monitoring-create" wire:navigated class="btn btn-sm btn-primary shadow-sm"><i
                            class="fas fa-add fa-sm text-white"></i>
                        <span class="d-none d-sm-inline-block">Add Record</span>
                    </a>
                @endif
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
                                <td>
                                    <div style="width: 100px">
                                        {{ substr($shift->start_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }}</td>
                                    </div>
                                <td>
                                    <div style="width: 100px">
                                        {{ strtoupper($shift->shift) }}
                                    </div>
                                </td>
                                <td>
                                    <div  style="white-space: pre-line;">{{ $shift->notes ? $shift->notes : '-' }}</td></div>
                                <td>
                                    <div class="d-flex">
                                        <a href="/monitoring/{{ $shift->id }}" wire:navigated type="button"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-info-circle" style="color: white"></i>
                                        </a>
                                        @if (!$isAdmin)
                                            <a href="/monitoring/{{ $shift->id }}/edit" type="button"
                                                class="btn btn-warning ml-2 btn-sm">
                                                <i class="fas fa-pencil" style="color: white"></i>
                                            </a>

                                            <button wire:click="showConfirmDelete({{ $shift }})" type="button"
                                                class="btn btn-danger ml-2 btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
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
                        Delete
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


    <div wire:ignore.self class="modal fade" id="modalDownload" tabindex="-1" aria-labelledby="modalDownloadLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form wire:submit.prevent="downloadReport" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDownloadLabel">
                        Download Report
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div>
                        <label>Date</label>
                        <input wire:model="downloadDate" type="date" class="form-control" required>
                        @error('downloadDate')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <label>Type</label>
                        <select wire:model="downloadType" class="form-control">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">Download</button>
                </div>
            </form>
        </div>
    </div>
</div>

@script
    <script>
        $js('modalDownloadShow', () => {
            $('#modalDownload').modal('show');
        })

        window.addEventListener('modalDownloadHide', () => {
            $('#modalDownload').modal('hide')
        });


        window.addEventListener('show-modal-detail', () => {
            $('#detailModal').modal('show');
        });

        window.addEventListener('close-modal-detail', () => {
            $('#detailModal').modal('hide');
        });
    </script>
@endscript
