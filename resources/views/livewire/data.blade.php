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
                <a href="/data/create" wire:navigated class=" btn btn-sm btn-primary shadow-sm"><i
                        class="fas fa-add fa-sm text-white"></i>
                    <span class="d-none d-sm-inline-block">Add Record</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- <div class="input-group input-group-sm mb-4 float-right" style="width: 220px">
                <div class="input-group-prepend">
                    <span class="input-group-text">Search</span>
                </div>
                <input wire:model.live="search" type="text" class="form-control" aria-label="Small"
                    aria-describedby="inputGroup-sizing-sm">
            </div> --}}
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
                                        <button type="button" class="btn btn-info btn-sm">
                                            <i class="fas fa-info-circle" style="color: white"></i>
                                        </button>
                                        <button type="button" class="btn btn-warning ml-2 btn-sm">
                                            <i class="fas fa-pencil" style="color: white"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger ml-2 btn-sm">
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


</div>

@script
    <script></script>
@endscript
