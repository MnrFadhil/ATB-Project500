<div>
    <!-- Page Heading -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $id ? 'Edit' : 'Create' }} Record</h1>
        <div class="btn-group">
            <a class="btn btn-secondary" href="/monitoring-index" wire:navigated type="button">Back</a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">{{ $id ? 'Edit Monitoring Data' : 'Create Monitoring Data' }}</h6>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="submit" @keydown.enter.prevent>
                
                <!-- ============ SHIFT INFORMATION ============ -->
                <div class="card mb-3 border-left-primary">
                    <div class="card-header bg-light cursor-pointer" wire:click="toggleSection('shiftInfo')" style="cursor: pointer;">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas {{ $showShiftInfo ? 'fa-chevron-down' : 'fa-chevron-right' }}"></i>
                            📅 Shift Information
                        </h6>
                    </div>
                    @if($showShiftInfo)
                        <div class="card-body">
                            <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="date">Date <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                    <input
                                        wire:model.live="form.shift.date"
                                        type="date"
                                        class="form-control flex-grow-1 @error('form.shift.date') is-invalid @enderror"
                                        style="min-width:0"
                                        id="date"
                                        required>
                                    @if(empty($form->shift['date'] ?? ''))
                                        <i class="fas fa-exclamation-triangle text-warning ml-2" title="Wajib diisi"></i>
                                    @endif
                                </div>
                                @error('form.shift.date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                                <div class="col-md-6 mb-3">
                                    <label for="shift">Shift <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center">
                                        <select wire:model.live="form.shift.shift" class="custom-select flex-grow-1 @error('form.shift.shift') is-invalid @enderror" style="min-width:0" id="shift" required>
                                            <option value="">Select Shift</option>
                                            <option value="shift i">Shift I</option>
                                            <option value="shift ii">Shift II</option>
                                            <option value="shift iii">Shift III</option>
                                        </select>
                                        @if(empty($form->shift['shift'] ?? ''))
                                            <i class="fas fa-exclamation-triangle text-warning ml-2" title="Wajib diisi"></i>
                                        @endif
                                    </div>
                                    @error('form.shift.shift') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="startTime">Start Shift Time <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center">
                                    <select
                                        wire:model.live="form.shift.start_time"
                                        wire:change="loadPreviousShiftData(form.shift.date, form.shift.start_time)"
                                        class="custom-select flex-grow-1 @error('form.shift.start_time') is-invalid @enderror"
                                        style="min-width:0"
                                        id="startTime"
                                        required>
                                        <option value="">Select Start Time</option>
                                        @php
                                            $shiftTimes = [
                                                'shift i'   => ['07:00','09:00','11:00','13:00','15:00'],
                                                'shift ii'  => ['15:00','17:00','19:00','21:00','23:00'],
                                                'shift iii' => ['23:00','01:00','03:00','05:00','07:00'],
                                            ];
                                            $selectedShift = strtolower($form->shift['shift'] ?? '');
                                            $startOptions = $shiftTimes[$selectedShift] ?? [];
                                        @endphp
                                        @forelse($startOptions as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @empty
                                            @for($i = 1; $i <= 24; $i += 2)
                                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00">
                                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00
                                                </option>
                                            @endfor
                                        @endforelse
                                    </select>
                                    @if(in_array($form->shift['start_time'] ?? '', ['', '00:00']))
                                        <i class="fas fa-exclamation-triangle text-warning ml-2" title="Wajib diisi"></i>
                                    @endif
                                    </div>
                                    @error('form.shift.start_time') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="endTime">End Shift Time <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center">
                                    <select wire:model.live="form.shift.end_time" class="custom-select flex-grow-1 @error('form.shift.end_time') is-invalid @enderror" style="min-width:0" id="endTime" required>
                                        <option value="">Select End Time</option>
                                        @php
                                            $endOptions = $shiftTimes[$selectedShift] ?? [];
                                        @endphp
                                        @forelse($endOptions as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @empty
                                            @for($i = 1; $i <= 24; $i += 2)
                                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00">
                                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00
                                                </option>
                                            @endfor
                                        @endforelse
                                    </select>
                                    @if(in_array($form->shift['end_time'] ?? '', ['', '00:00']))
                                        <i class="fas fa-exclamation-triangle text-warning ml-2" title="Wajib diisi"></i>
                                    @endif
                                    </div>
                                    @error('form.shift.end_time') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="operator1">Operator 1 <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center">
                                        <select wire:model.live="form.shift.operator_1" class="custom-select flex-grow-1 @error('form.shift.operator_1') is-invalid @enderror" style="min-width:0" id="operator1" required>
                                            <option value="">Select Operator</option>
                                            @forelse ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @empty
                                                <option value="">Users Not Available</option>
                                            @endforelse
                                        </select>
                                        @if(empty($form->shift['operator_1'] ?? ''))
                                            <i class="fas fa-exclamation-triangle text-warning ml-2" title="Wajib diisi"></i>
                                        @endif
                                    </div>
                                    @error('form.shift.operator_1') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="operator2">Operator 2</label>
                                    <div class="d-flex align-items-center">
                                        <select wire:model.live="form.shift.operator_2" class="custom-select flex-grow-1" style="min-width:0" id="operator2">
                                            <option value="">Select Operator</option>
                                            @forelse ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @empty
                                                <option value="">Users Not Available</option>
                                            @endforelse
                                        </select>
                                        @if(empty($form->shift['operator_2'] ?? ''))
                                            <i class="fas fa-exclamation-triangle text-warning ml-2" title="Belum diisi"></i>
                                        @endif
                                    </div>
                                </div>

                                <!-- NEXT BUTTON -->
                                @if($this->isShiftInfoComplete())
                                    <div class="col-12 mt-4 mb-3">
                                        <button 
                                            type="button"
                                            class="btn btn-primary w-100"
                                            wire:click="loadPreviousShiftData">
                                            Click Next
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ============ MONITORING DATA (SEMUA) ============ -->
             @if($this->monitoringDataLoaded)
                <div class="card mb-3 border-left-info">
                    <div class="card-header bg-light cursor-pointer" wire:click="toggleSection('monitoringData')" style="cursor: pointer;">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas {{ $showMonitoringData ? 'fa-chevron-down' : 'fa-chevron-right' }}"></i>
                            📊 Monitoring Data
                        </h6>
                    </div>
                    @if($showMonitoringData)
                        <div class="card-body">
                            <!-- NOTES -->
                            <div class="form-row mb-4">
                                <div class="col-12 mb-3">
                                    <label for="notes">Notes</label>
                                    <textarea wire:model="form.shift.notes" class="form-control" id="notes" rows="10" placeholder="Optional notes..." @keydown.enter.stop></textarea>
                                    @error('form.shift.notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- AIR BAKU -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">💧 Air Baku</h6>
                            <div class="form-row">
                                <div class="col-md-3 mb-3">
                                    <label>pH</label>
                                    <input wire:model="form.airBaku.ph" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Turbidity (NTU)</label>
                                    <input wire:model="form.airBaku.turbidity" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Warna (PCU)</label>
                                    <input wire:model="form.airBaku.color" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>TDS</label>
                                    <input wire:model="form.airBaku.tds" type="number" step="any" class="form-control" required>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- SEDIMENTATION -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">🏭 Sedimentation</h6>
                            <div class="form-row">
                                <div class="col-md-3 mb-3">
                                    <label>pH</label>
                                    <input wire:model="form.sedimentation.ph" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Turbidity (NTU)</label>
                                    <input wire:model="form.sedimentation.turbidity" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Warna (PCU)</label>
                                    <input wire:model="form.sedimentation.color" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>TDS</label>
                                    <input wire:model="form.sedimentation.tds" type="number" step="any" class="form-control" required>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- RESERVOIR -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">🏊 Reservoir</h6>
                            <div class="form-row">
                                <div class="col-md-3 mb-3">
                                    <label>pH</label>
                                    <input wire:model="form.reservoir.ph" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Turbidity (NTU)</label>
                                    <input wire:model="form.reservoir.turbidity" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Warna (PCU)</label>
                                    <input wire:model="form.reservoir.color" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>TDS</label>
                                    <input wire:model="form.reservoir.tds" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Free Chlor (mg/L)</label>
                                    <input wire:model="form.reservoir.free_chlor" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>ORP (mV)</label>
                                    <input wire:model="form.reservoir.orp" type="number" step="any" class="form-control" required>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- FLOW METER AIR BAKU -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">📊 Flow Meter Air Baku</h6>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label>Flow (L/s)</label>
                                    <input wire:model="form.flowAirBaku.flow" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Totalizer (m³)</label>
                                    <div class="d-flex align-items-center">
                                        <input wire:model.blur="form.flowAirBaku.totalizer" type="number" step="any" class="form-control flex-grow-1" style="min-width:0" required>
                                        @if(!empty($prevTotalizers) && isset($prevTotalizers['air_baku']) && $prevTotalizers['air_baku'] !== '' && (string)($form->flowAirBaku['totalizer'] ?? '') === $prevTotalizers['air_baku'])
                                            <i class="fas fa-exclamation-triangle text-warning ml-2" title="Totalizer tidak berubah dari shift sebelumnya"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- FLOWMETER DISTRIBUSI -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">📌 Flowmeter Distribusi</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold mb-2">Yos Sudarso</h6>
                                    <div class="form-row">
                                        <div class="col-6 mb-3">
                                            <label>Flow (L/s)</label>
                                            <input wire:model="form.flowSudarso.flow" type="number" step="any" class="form-control" required>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label>Totalizer (m³)</label>
                                            <div class="d-flex align-items-center">
                                                <input wire:model.blur="form.flowSudarso.totalizer" type="number" step="any" class="form-control flex-grow-1" style="min-width:0" required>
                                                @if(!empty($prevTotalizers) && isset($prevTotalizers['sudarso']) && $prevTotalizers['sudarso'] !== '' && (string)($form->flowSudarso['totalizer'] ?? '') === $prevTotalizers['sudarso'])
                                                    <i class="fas fa-exclamation-triangle text-warning ml-2" title="Totalizer tidak berubah dari shift sebelumnya"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold mb-2">Veteran</h6>
                                    <div class="form-row">
                                        <div class="col-6 mb-3">
                                            <label>Flow (L/s)</label>
                                            <input wire:model="form.flowVeteran.flow" type="number" step="any" class="form-control" required>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label>Totalizer (m³)</label>
                                            <div class="d-flex align-items-center">
                                                <input wire:model.blur="form.flowVeteran.totalizer" type="number" step="any" class="form-control flex-grow-1" style="min-width:0" required>
                                                @if(!empty($prevTotalizers) && isset($prevTotalizers['veteran']) && $prevTotalizers['veteran'] !== '' && (string)($form->flowVeteran['totalizer'] ?? '') === $prevTotalizers['veteran'])
                                                    <i class="fas fa-exclamation-triangle text-warning ml-2" title="Totalizer tidak berubah dari shift sebelumnya"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- LEVEL RESERVOIR -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">📏 Level Reservoir</h6>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label>Level A (m)</label>
                                    <input wire:model="form.reservoirLevel.level_a" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Level B (m)</label>
                                    <input wire:model="form.reservoirLevel.level_b" type="number" step="any" class="form-control" required>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- MDP PANEL -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">⚡ In Comer MDP Panel</h6>
                            <div class="form-row">
                                <div class="col-md-3 mb-3">
                                    <label>Kwh</label>
                                    <input wire:model="form.mdpPanel.kwh_total" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>WBP</label>
                                    <input wire:model="form.mdpPanel.wdp" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>LWBP</label>
                                    <input wire:model="form.mdpPanel.lwbp" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Kvar</label>
                                    <input wire:model="form.mdpPanel.kvar" type="number" step="any" class="form-control" required>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- LEVEL AIR BAK PENGUMPUL -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">🔧 Level Air Bak Pengumpul</h6>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label>Level (m)</label>
                                    <input wire:model="form.shift.collection_tank" type="number" step="any" class="form-control" required>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- PRESSURE STATIC MIXER -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">⚙️ Pressure Static Mixer</h6>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label>Inlet (Bar)</label>
                                    <input wire:model="form.pressStatic.inlet" type="number" step="any" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Outlet (Bar)</label>
                                    <input wire:model="form.pressStatic.outlet" type="number" step="any" class="form-control" required>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- POMPA INTAKE -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">🚰 Pompa Intake</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="font-weight-bold mb-2">Pompa A</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpIntakeA.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpIntakeA.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Ampere (A)</label>
                                                <input wire:model="form.pumpIntakeA.ampere" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Frekuensi (Hz)</label>
                                                <input wire:model="form.pumpIntakeA.frequency" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pressure (Bar)</label>
                                                <input wire:model="form.pumpIntakeA.pressure" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <h6 class="font-weight-bold mb-2">Pompa B</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpIntakeB.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpIntakeB.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Ampere (A)</label>
                                                <input wire:model="form.pumpIntakeB.ampere" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Frekuensi (Hz)</label>
                                                <input wire:model="form.pumpIntakeB.frequency" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pressure (Bar)</label>
                                                <input wire:model="form.pumpIntakeB.pressure" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <h6 class="font-weight-bold mb-2">Pompa C</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpIntakeC.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpIntakeC.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Ampere (A)</label>
                                                <input wire:model="form.pumpIntakeC.ampere" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Frekuensi (Hz)</label>
                                                <input wire:model="form.pumpIntakeC.frequency" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pressure (Bar)</label>
                                                <input wire:model="form.pumpIntakeC.pressure" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- POMPA DISTRIBUSI -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">💧 Pompa Distribusi</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <h6 class="font-weight-bold mb-2">Pompa A</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpDistriA.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpDistriA.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Ampere (A)</label>
                                                <input wire:model="form.pumpDistriA.ampere" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Frekuensi (Hz)</label>
                                                <input wire:model="form.pumpDistriA.frequency" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pressure (Bar)</label>
                                                <input wire:model="form.pumpDistriA.pressure" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <h6 class="font-weight-bold mb-2">Pompa B</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpDistriB.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpDistriB.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Ampere (A)</label>
                                                <input wire:model="form.pumpDistriB.ampere" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Frekuensi (Hz)</label>
                                                <input wire:model="form.pumpDistriB.frequency" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pressure (Bar)</label>
                                                <input wire:model="form.pumpDistriB.pressure" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <h6 class="font-weight-bold mb-2">Pompa C</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpDistriC.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpDistriC.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Ampere (A)</label>
                                                <input wire:model="form.pumpDistriC.ampere" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Frekuensi (Hz)</label>
                                                <input wire:model="form.pumpDistriC.frequency" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pressure (Bar)</label>
                                                <input wire:model="form.pumpDistriC.pressure" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <h6 class="font-weight-bold mb-2">Pompa D</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpDistriD.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpDistriD.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Ampere (A)</label>
                                                <input wire:model="form.pumpDistriD.ampere" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Frekuensi (Hz)</label>
                                                <input wire:model="form.pumpDistriD.frequency" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pressure (Bar)</label>
                                                <input wire:model="form.pumpDistriD.pressure" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- POMPA DOSING PAC -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">💉 Pompa Dosing PAC</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold mb-2">Pompa A</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpPacA.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpPacA.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Frekuensi (Hz)</label>
                                                <input wire:model="form.pumpPacA.frequency" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Dosis (ppm)</label>
                                                <input wire:model="form.pumpPacA.dosage" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Konsentrasi (%)</label>
                                                <input wire:model="form.pumpPacA.concentration" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Level Tangki (cm)</label>
                                                <input wire:model="form.pumpPacA.tank_level" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <h6 class="font-weight-bold mb-2">Pompa B</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpPacB.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpPacB.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Frekuensi (Hz)</label>
                                                <input wire:model="form.pumpPacB.frequency" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Dosis (ppm)</label>
                                                <input wire:model="form.pumpPacB.dosage" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Konsentrasi (%)</label>
                                                <input wire:model="form.pumpPacB.concentration" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Level Tangki (cm)</label>
                                                <input wire:model="form.pumpPacB.tank_level" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- POMPA DOSING CHLORINE -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">🧪 Pompa Dosing Chlorine/Kaporit</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold mb-2">Pompa A</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpChlorA.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpChlorA.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Flowrate (lh)</label>
                                                <input wire:model="form.pumpChlorA.flow_rate" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Dosis (ppm)</label>
                                                <input wire:model="form.pumpChlorA.dosage" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Konsentrasi (%)</label>
                                                <input wire:model="form.pumpChlorA.concentration" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pengadukan (Kg)</label>
                                                <input wire:model="form.pumpChlorA.stirring" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Level Tangki (cm)</label>
                                                <input wire:model="form.pumpChlorA.tank_level" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <h6 class="font-weight-bold mb-2">Pompa B</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpChlorB.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpChlorB.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Flowrate (lh)</label>
                                                <input wire:model="form.pumpChlorB.flow_rate" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Dosis (ppm)</label>
                                                <input wire:model="form.pumpChlorB.dosage" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Konsentrasi (%)</label>
                                                <input wire:model="form.pumpChlorB.concentration" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pengadukan (Kg)</label>
                                                <input wire:model="form.pumpChlorB.stirring" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Level Tangki (cm)</label>
                                                <input wire:model="form.pumpChlorB.tank_level" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- POMPA DOSING SODA ASH -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">🧪 Pompa Dosing Soda Ash</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold mb-2">Pompa A</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpSodaA.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpSodaA.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Flow Rate (%)</label>
                                                <input wire:model="form.pumpSodaA.flow_rate" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Dosis (ppm)</label>
                                                <input wire:model="form.pumpSodaA.dosage" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Konsentrasi (%)</label>
                                                <input wire:model="form.pumpSodaA.concentration" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pengadukan (Kg)</label>
                                                <input wire:model="form.pumpSodaA.stirring" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Level Tangki (cm)</label>
                                                <input wire:model="form.pumpSodaA.tank_level" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <h6 class="font-weight-bold mb-2">Pompa B</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpSodaB.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpSodaB.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Flow Rate (%)</label>
                                                <input wire:model="form.pumpSodaB.flow_rate" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Dosis (ppm)</label>
                                                <input wire:model="form.pumpSodaB.dosage" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Konsentrasi (%)</label>
                                                <input wire:model="form.pumpSodaB.concentration" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pengadukan (Kg)</label>
                                                <input wire:model="form.pumpSodaB.stirring" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Level Tangki (cm)</label>
                                                <input wire:model="form.pumpSodaB.tank_level" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr style="border-color: #00664A; border-width: 2px;">

                            <!-- POMPA DOSING POLYMER -->
                            <h6 class="font-weight-bolder mb-3 mt-5" style="color: #00664A;">🧪 Pompa Dosing Polymer</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="font-weight-bold mb-2">Pompa A</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpPolymerA.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpPolymerA.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Flow Rate (%)</label>
                                                <input wire:model="form.pumpPolymerA.flow_rate" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Dosis (ppm)</label>
                                                <input wire:model="form.pumpPolymerA.dosage" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Konsentrasi (%)</label>
                                                <input wire:model="form.pumpPolymerA.concentration" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pengadukan (Kg)</label>
                                                <input wire:model="form.pumpPolymerA.stirring" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Level Tangki (cm)</label>
                                                <input wire:model="form.pumpPolymerA.tank_level" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <h6 class="font-weight-bold mb-2">Pompa B</h6>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label>Status</label>
                                            <select wire:model.live="form.pumpPolymerB.status" class="custom-select">
                                                <option value="standby">Standby</option>
                                                <option value="running">Running</option>
                                            </select>
                                        </div>
                                        @if(data_get($this->form, 'pumpPolymerB.status') == 'running')
                                            <div class="col-12 mb-3">
                                                <label>Flow Rate (%)</label>
                                                <input wire:model="form.pumpPolymerB.flow_rate" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Dosis (ppm)</label>
                                                <input wire:model="form.pumpPolymerB.dosage" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Konsentrasi (%)</label>
                                                <input wire:model="form.pumpPolymerB.concentration" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Pengadukan (Kg)</label>
                                                <input wire:model="form.pumpPolymerB.stirring" type="number" step="any" class="form-control">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label>Level Tangki (cm)</label>
                                                <input wire:model="form.pumpPolymerB.tank_level" type="number" step="any" class="form-control">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
             @else
                <div class="alert alert-info mt-3" role="alert">
                    <i class="fas fa-info-circle"></i>
                    <strong> Complete Shift Information first</strong> - Pastikan Semua Form Diatas Terisi
                </div>
              @endif

                <!-- BUTTON SUBMIT -->
                <div class="d-flex justify-content-between mt-4">
                    <a class="btn btn-secondary" href="/monitoring-index" wire:navigated>Back</a>
                    @if($this->monitoringDataLoaded)
                        <button class="btn btn-primary" type="submit">
                            {{ $id ? 'Update' : 'Create' }} Data ✓
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>