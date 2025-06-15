<div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Record</h1>
    </div>

    {{-- Form --}}

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold ">Details</h6>

            <i class="fas fa-fw fa-clone" style="cursor:pointer" wire:click="$js.copyClipboard" aria-hidden="true"></i>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 font-weight-bold mb-2 text-uppercase">{{ $shifts->shift }}</div>
                <div class="col-md-8 mb-3">
                    <div class="row">
                        <div class="col-5 col-md-4">Date</div>
                        <div class="col-7 col-md-8">: {{ $shifts->date }}</div>
                    </div>

                    <div class="row">
                        <div class="col-5 col-md-4">Operator</div>
                        <div class="col-7 col-md-8">:
                            @foreach ($shifts->shiftOperators as $operator => $index)
                                @if ($operator == 1)
                                    &
                                @endif
                                {{ $shifts->shiftOperators[$operator]->name }}
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-5 col-md-4">Time</div>
                        <div class="col-7 col-md-8">: {{ substr($shifts->start_time, 0, 5) }} -
                            {{ substr($shifts->end_time, 0, 5) }}</div>
                    </div>

                    <div class="row">
                        <div class="col-5 col-md-4">Notes</div>
                        <div class="col-7 col-md-8">: {{ $shifts->notes ? $shifts->notes : '-' }}</div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                @foreach ($shifts->waterQualities as $waterQuality)
                    <div class="col-md-6">
                        <div class="font-weight-bold mb-2 text-capitalize">{{ $waterQuality->type }}</div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-5 col-md-4">Ph</div>
                                <div class="col-7 col-md-8">: {{ $waterQuality->ph }}</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Turbidity</div>
                                <div class="col-7 col-md-8">: {{ $waterQuality->turbidity }} NTU</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Warna</div>
                                <div class="col-7 col-md-8">: {{ $waterQuality->color }} PCU</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">TDS</div>
                                <div class="col-7 col-md-8">: {{ $waterQuality->tds }}</div>
                            </div>

                            @if ($waterQuality->type == 'reservoir')
                                <div class="row">
                                    <div class="col-5 col-md-4">Free Chlor</div>
                                    <div class="col-7 col-md-8">: {{ $waterQuality->free_chlor }} mg/L</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">ORP</div>
                                    <div class="col-7 col-md-8">: {{ $waterQuality->orp }} mV</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <hr>

            <div class="row">
                @foreach ($shifts->flowMeters as $flowMeter)
                    @if (!$flowMeter->location)
                        <div class="col-md-6">
                            <div class="font-weight-bold mb-2 text-capitalize">Flowmeter Distribusi</div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-5 col-md-4">Flow</div>
                                    <div class="col-7 col-md-8">: {{ $flowMeter->flow }} L/s</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Totalizer</div>
                                    <div class="col-7 col-md-8">: {{ $flowMeter->totalizer }} m³</div>
                                </div>
                            </div>
                        </div>
                    @else
                        @if ($flowMeter->location == 'yos sudarso')
                            <div class="col-md-12">
                                <div class="font-weight-bold mb-2 text-capitalize">Flowmeter Distribusi</div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="font-weight-bold mb-2 text-capitalize">{{ $flowMeter->location }}</div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-5 col-md-4">Flow</div>
                                    <div class="col-7 col-md-8">: {{ $flowMeter->flow }} L/s</div>
                                </div>
                                <div class="row">
                                    <div class="col-5 col-md-4">Totalizer</div>
                                    <div class="col-7 col-md-8">: {{ $flowMeter->totalizer }} m³</div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">Level Reservoir</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Reservoir A</div>
                            <div class="col-7 col-md-8">: {{ $shifts->reservoirLevels->level_a }} m</div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Reservoir B</div>
                            <div class="col-7 col-md-8">: {{ $shifts->reservoirLevels->level_b }} m</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">In Comer MDP Panel</div>
                    <div>
                        <div class="row">
                            <div class="col-5 col-md-4">Total Kwh</div>
                            <div class="col-7 col-md-8">: {{ $shifts->mdpPanels->kwh_total }} kwh</div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Wbp</div>
                            <div class="col-7 col-md-8">: {{ $shifts->mdpPanels->wdp }}</div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Lwbp</div>
                            <div class="col-7 col-md-8">: {{ $shifts->mdpPanels->lwbp }}</div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Kvar</div>
                            <div class="col-7 col-md-8">: {{ $shifts->mdpPanels->kvar }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div>
                        <div class="row">
                            <div class="col-5 col-md-4">Level Air Bak Pengumpul</div>
                            <div class="col-7 col-md-8">: {{ $shifts->collection_tank }} m</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">⁠Pressure Static Mixer</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Inlet</div>
                            <div class="col-7 col-md-8">: {{ $shifts->pressureStaticMixer->inlet }} Bar</div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Outlet</div>
                            <div class="col-7 col-md-8">: {{ $shifts->pressureStaticMixer->outlet }} Bar</div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                @foreach ($shifts->pumpProccess as $pumpProccess)
                    @if ($pumpProccess->type == 'intake a')
                        <div class="col-md-12">
                            <div class="font-weight-bold mb-2 text-capitalize">Pompa Intake</div>
                        </div>
                    @elseif ($pumpProccess->type == 'distribusi a')
                        <div class="col-md-12">
                            <hr>
                            <div class="font-weight-bold mb-2 text-capitalize">Pompa Distribusi</div>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <div class="font-weight-bold mb-2">
                            <div class=" text-capitalize">
                                {{ $pumpProccess->type }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-5 col-md-4">Ampere</div>
                                <div class="col-7 col-md-8">: {{ $pumpProccess->ampere }} A</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Frekuensi</div>
                                <div class="col-7 col-md-8">: {{ $pumpProccess->frequency }} Hz</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Pressure</div>
                                <div class="col-7 col-md-8">: {{ $pumpProccess->pressure }} Bar</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Status</div>
                                <div class="col-7 col-md-8 d-flex">:
                                    @if ($pumpProccess->status == 'running')
                                        <h6> <span class="badge badge-success ml-1">Running</span></h6>
                                    @elseif ($pumpProccess->status == 'standby')
                                        <h6> <span class="badge badge-info ml-1">Standby</span></h6>
                                    @else
                                        <h6> <span class="badge badge-warning ml-1">Maintenance</span></h6>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            @foreach ($shifts->pumpChemicals as $pumpChemicals)
                <hr>

                <div class="row">
                    <div class="col-md-6">
                        @if ($pumpChemicals->type == 'pac')
                            <div class="font-weight-bold mb-2 text-capitalize">Pompa Dosing PAC</div>
                        @endif
                        @if ($pumpChemicals->type == 'chlorine/kaporit')
                            <div class="font-weight-bold mb-2 text-capitalize">⁠Pompa Dosing Clorine/Kaporit</div>
                        @endif
                        <div class="mb-3">
                            @if ($pumpChemicals->type == 'pac')
                                <div class="row">
                                    <div class="col-5 col-md-4">Frekuensi</div>
                                    <div class="col-7 col-md-8">: {{ $pumpChemicals->frequency }} Hz</div>
                                </div>
                            @endif
                            @if ($pumpChemicals->type == 'chlorine/kaporit')
                                <div class="row">
                                    <div class="col-5 col-md-4">Flow Rate</div>
                                    <div class="col-7 col-md-8">: {{ $pumpChemicals->flow_rate }} l/h</div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-5 col-md-4">Konsentrasi</div>
                                <div class="col-7 col-md-8">: {{ $pumpChemicals->concentration }} %</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Dosis</div>
                                <div class="col-7 col-md-8">: {{ $pumpChemicals->dosage }} ppm</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Pengadukan</div>
                                <div class="col-7 col-md-8">: {{ $pumpChemicals->stirring }} Kg</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">Level Tangki</div>
                                <div class="col-7 col-md-8">: {{ $pumpChemicals->tank_level }} cm</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Bar Screen</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->barscreen == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->barscreen == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @elseif ($shifts->unitOperation->barscreen == 'running')
                                    <h6> <span class="badge badge-info ml-1">Running</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Air Drayer</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->air_drayer == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->air_drayer == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @elseif ($shifts->unitOperation->air_drayer == 'running')
                                    <h6> <span class="badge badge-info ml-1">Running</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Finescreen A</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->finescreen_a == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->finescreen_a == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @elseif ($shifts->unitOperation->finescreen_a == 'running')
                                    <h6> <span class="badge badge-info ml-1">Running</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Finescreen B</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->finescreen_b == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->finescreen_b == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @elseif ($shifts->unitOperation->finescreen_b == 'running')
                                    <h6> <span class="badge badge-info ml-1">Running</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Compressor A</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->compressor_a == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->compressor_a == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @elseif ($shifts->unitOperation->compressor_a == 'running')
                                    <h6> <span class="badge badge-info ml-1">Running</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Compressor B</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->compressor_b == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->compressor_b == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @elseif ($shifts->unitOperation->compressor_b == 'running')
                                    <h6> <span class="badge badge-info ml-1">Running</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Maintenance</span></h6>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">WTP</div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">Flocullation A</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->flokulator_a == 'on')
                                    <h6> <span class="badge badge-success ml-1">On</span></h6>
                                @else
                                    <h6> <span class="badge badge-danger ml-1">Off</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Flocullation B</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->flokulator_b == 'on')
                                    <h6> <span class="badge badge-success ml-1">On</span></h6>
                                @else
                                    <h6> <span class="badge badge-danger ml-1">Off</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Clarifier A</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->clarifier_a == 'on')
                                    <h6> <span class="badge badge-success ml-1">On</span></h6>
                                @else
                                    <h6> <span class="badge badge-danger ml-1">Off</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Clarifier B</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->clarifier_b == 'on')
                                    <h6> <span class="badge badge-success ml-1">On</span></h6>
                                @else
                                    <h6> <span class="badge badge-danger ml-1">Off</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Filtrasi</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->filtration == 'on')
                                    <h6> <span class="badge badge-success ml-1">On</span></h6>
                                @else
                                    <h6> <span class="badge badge-danger ml-1">Off</span></h6>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="font-weight-bold mb-2 text-capitalize">
                        Level Grafity Filtrasi
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5 col-md-4">A</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->gravity_filter_a_status == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @else
                                    {{ $shifts->wtps->gravity_filter_a }} m
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">B</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->gravity_filter_b_status == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @else
                                    {{ $shifts->wtps->gravity_filter_b }} m
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">C</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->gravity_filter_c_status == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @else
                                    {{ $shifts->wtps->gravity_filter_c }} m
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">D</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->gravity_filter_d_status == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @else
                                    {{ $shifts->wtps->gravity_filter_d }} m
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">E</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->gravity_filter_e_status == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @else
                                    {{ $shifts->wtps->gravity_filter_e }} m
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">F</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->wtps->gravity_filter_f_status == 'standby')
                                    <h6> <span class="badge badge-primary ml-1">Standby</span></h6>
                                @else
                                    {{ $shifts->wtps->gravity_filter_f }} m
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <a class="btn btn-secondary" href="/monitoring-index" wire:navigated type="button">Back</a>
                @if (!$isAdmin)
                    <a href="/monitoring/{{ $id }}/edit" class="btn btn-warning ml-2" type="submit">
                        <i class="fas fa-pencil" style="color: white"></i>
                        Edit
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@script
    <script>
        let shiftDetail = @json($shifts);
        let airBaku = shiftDetail.water_qualities.find(data => data.type == 'air baku')
        let sedimentasi = shiftDetail.water_qualities.find(data => data.type == 'sedimentation')
        let reservoir = shiftDetail.water_qualities.find(data => data.type == 'reservoir')
        let flowAirBaku = shiftDetail.flow_meters.find(data => data.location == null)
        let flowSudarso = shiftDetail.flow_meters.find(data => data.location == 'yos sudarso')
        let flowVeteran = shiftDetail.flow_meters.find(data => data.location == 'veteran')
        let pumpProccessIntake = shiftDetail.pump_proccess.filter(data => data.type.includes('intake'))
        let pumpProccessDistribusi = shiftDetail.pump_proccess.filter(data => data.type.includes('distribusi'))
        let pumpPac = shiftDetail.pump_chemicals.find(data => data.type == 'pac')
        let pumpChlorine = shiftDetail.pump_chemicals.find(data => data.type == 'chlorine/kaporit')

        let intake = transformText(pumpProccessIntake, {
            tittle: [],
            tittle1: '',
            tittle2: '',
            tittle3: '',
            ampere: '',
            frequency: '',
            pressure: '',
        })


        let distri = transformText(pumpProccessDistribusi, {
            tittle: [],
            tittle1: '',
            tittle2: '',
            tittle3: '',
            ampere: '',
            frequency: '',
            pressure: '',
        })



        function transformText(dataArray, tempData) {
            dataArray.forEach((data, i) => {
                const idx = data.type.split(' ')[1].toUpperCase()

                if (data.status == 'running') {
                    if (tempData.tittle1 == '') {
                        tempData.tittle1 = `${idx} Running`
                    } else {
                        let test = tempData.tittle1.split(' ')
                        test[test.length - 2] += `, ${idx}`
                        tempData.tittle1 = test.join(' ')
                    }

                    if (tempData.ampere == '') {
                        tempData.ampere = `${data.ampere} A (${idx})`
                    } else {
                        tempData.ampere += `; ${data.ampere} A (${idx})`
                    }
                    if (tempData.frequency == '') {
                        tempData.frequency = `${data.frequency} Hz (${idx})`
                    } else {
                        tempData.frequency += `; ${data.frequency} Hz (${idx})`
                    }
                    if (tempData.pressure == '') {
                        tempData.pressure = `${data.pressure} Bar (${idx})`
                    } else {
                        tempData.pressure += `; ${data.pressure} Bar (${idx})`
                    }
                } else if (data.status == 'standby') {
                    if (tempData.tittle2 == '') {
                        tempData.tittle2 = `${idx} Standby`
                    } else {
                        let test = tempData.tittle2.split(' ')
                        test[test.length - 2] += `, ${idx}`
                        tempData.tittle2 = test.join(' ')
                    }
                } else {
                    if (tempData.tittle3 == '') {
                        tempData.tittle3 = `${idx} Maintenance`
                    } else {
                        let test = tempData.tittle3.split(' ')
                        test[test.length - 2] += `, ${idx}`
                        tempData.tittle3 = test.join(' ')
                    }
                }
            });

            if (tempData.tittle1 !== '') tempData.tittle.push(tempData.tittle1)
            if (tempData.tittle2 !== '') tempData.tittle.push(tempData.tittle2)
            if (tempData.tittle3 !== '') tempData.tittle.push(tempData.tittle3)

            return tempData;
        }

        /**
         * Function to copy all monitoring data values from HTML to clipboard
         * This function extracts all measurement values from the monitoring report
         * and copies them as text in a structured format with section headers
         */


        $js('copyClipboard', () => {
            copyAllMonitoringValues();
        })

        function copyAllMonitoringValues() {
            const reportText =
                `*UPDATE DAILY REPORT AND MONITORING SCADA*
${shiftDetail.date} / ${shiftDetail.shift.toUpperCase()}
Operator : ${shiftDetail.shift_operators[0].name} ${shiftDetail.shift_operators.length >1 ? '& '+shiftDetail.shift_operators?.[1]?.name:''}
Jam : ${shiftDetail.start_time.slice(0,5)} - ${shiftDetail.end_time.slice(0,5)} WIB

- Air Baku :
pH : ${airBaku.ph}
Turbidity : ${airBaku.turbidity} NTU
Warna :  ${airBaku.turbidity} PCU
TDS : ${airBaku.tds}

- Sedimentation :
pH : ${sedimentasi.ph}
Turbidity : ${sedimentasi.turbidity} NTU
Warna :  ${sedimentasi.turbidity} PCU
TDS : ${sedimentasi.tds}

- Reservoir :
pH : ${reservoir.ph}
Turbidity : ${reservoir.turbidity} NTU
Warna :  ${reservoir.turbidity} PCU
TDS : ${reservoir.tds}
Free Chlor : ${reservoir.free_chlor} mg/L
ORP : ${reservoir.orp} mV

- Flowmeter Air Baku
Flow : ${flowAirBaku.flow} l/s
Totalizer : ${flowAirBaku.totalizer} m³

- Flowmeter Distribusi :
Yos Sudarso
Flow : ${flowSudarso.flow} l/s
Totalizer : ${flowSudarso.totalizer} m³

- Veteran
Flow : ${flowVeteran.flow} l/s
Totalizer : ${flowVeteran.totalizer} m³

- Level Reservoir :
Level A : ${shiftDetail.reservoir_levels.level_a} m
Level B : ${shiftDetail.reservoir_levels.level_b} m

- In Comer MDP Panel :
Kwh total : ${shiftDetail.mdp_panels.kwh_total}
Wbp : ${shiftDetail.mdp_panels.wdp}
Lwbp : ${shiftDetail.mdp_panels.lwbp}
Kvar : ${shiftDetail.mdp_panels.kvar}

- Level air bak pengumpul: ${shiftDetail.collection_tank} m

- ⁠Pressure Static Mixer
Inlet : ${shiftDetail.pressure_static_mixer.inlet} bar
Outlet :  ${shiftDetail.pressure_static_mixer.outlet} bar

- Pompa Intake : ${intake.tittle.join(', ')}
Ampere : ${intake.ampere == ''?'-':intake.ampere}
Frekuensi : ${intake.frequency == ''?'-':intake.frequency}
Pressure : ${intake.pressure== ''?'-':intake.pressure}

- Pompa Distribusi : ${distri.tittle.join(', ')}
Ampere : ${distri.ampere == ''?'-':distri.ampere}
Frekuensi : ${distri.frequency == ''?'-':distri.frequency}
Pressure : ${distri.pressure== ''?'-':distri.pressure}

- Pompa Dosing PAC :
Frekuensi : ${pumpPac.frequency} Hz
Dosis : ${pumpPac.dosage} ppm
Konsentrasi :  ${pumpPac.concentration} %
Pengadukan : ${pumpPac.stirring} Kg
Level Tangki : ${pumpPac.tank_level} cm

- Pompa Dosing Clorine/Kaporit :
Frekuensi : ${pumpPac.flow_rate} l/h
Dosis : ${pumpPac.dosage} ppm
Konsentrasi :  ${pumpPac.concentration} %
Pengadukan : ${pumpPac.stirring} Kg
Level Tangki : ${pumpPac.tank_level} cm

- Barscreen : ${shiftDetail.unit_operation.barscreen}
- Fine Screen A : ${shiftDetail.unit_operation.finescreen_a}
- Fine Screen B : ${shiftDetail.unit_operation.finescreen_b}
- Compressor A : ${shiftDetail.unit_operation.compressor_a}
- Compressor B : ${shiftDetail.unit_operation.compressor_b}
- Air dryer : ${shiftDetail.unit_operation.air_drayer}

WTP
- Flocullation A: ${shiftDetail.wtps.flokulator_a}
- Flocullation B: ${shiftDetail.wtps.flokulator_b}
- Clarifier A : ${shiftDetail.wtps.clarifier_a}
- Clarifier B : ${shiftDetail.wtps.clarifier_b}
- Filtrasi : ${shiftDetail.wtps.filtration}
- Level Grafity Filtrasi :
A: ${shiftDetail.wtps.gravity_filter_a_status == 'standby' ? `Standby`:`${shiftDetail.wtps.gravity_filter_a} m menuju overflow` }
B: ${shiftDetail.wtps.gravity_filter_b_status == 'standby' ? `Standby`:`${shiftDetail.wtps.gravity_filter_b} m menuju overflow` }
C: ${shiftDetail.wtps.gravity_filter_c_status == 'standby' ? `Standby`:`${shiftDetail.wtps.gravity_filter_c} m menuju overflow` }
D: ${shiftDetail.wtps.gravity_filter_d_status == 'standby' ? `Standby`:`${shiftDetail.wtps.gravity_filter_d} m menuju overflow` }
E: ${shiftDetail.wtps.gravity_filter_e_status == 'standby' ? `Standby`:`${shiftDetail.wtps.gravity_filter_e} m menuju overflow` }
F: ${shiftDetail.wtps.gravity_filter_f_status == 'standby' ? `Standby`:`${shiftDetail.wtps.gravity_filter_f} m menuju overflow` }

Catatan : ${shiftDetail?.notes ?shiftDetail?.notes : '-'}`;


            const textarea = document.createElement('textarea');
            textarea.value = reportText;
            textarea.style.position = 'fixed'; // Prevent scrolling
            document.body.appendChild(textarea);

            textarea.select();
            try {
                const success = document.execCommand('copy');
                if (success) {
                    alert('Report copied to clipboard!');
                } else {
                    throw new Error('Copy failed');
                }
            } catch (err) {
                alert('Failed to copy. Please manually select and copy (Ctrl+C).');
                console.error('Copy error:', err);
            }
            document.body.removeChild(textarea);


            // navigator.clipboard.writeText(reportText)
            //     .then(() => alert('Daily report copied to clipboard!'))
            //     .catch(err => {
            //         console.error('Failed to copy text: ', err);
            //         alert('Please click the button again to copy');
            //     });
        }
    </script>
@endscript
