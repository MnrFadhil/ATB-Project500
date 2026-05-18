<div>
    <!-- Page Heading -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $id ? 'Edit' : 'Create' }} Record</h1>
        <a class="btn btn-secondary" href="/monitoring-index" wire:navigated type="button">Back</a>
    </div>

    {{-- Form --}}

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold ">Forms</h6>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="submit">
                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Shift</div>
                    <div class="col-md-6 mb-3">
                        <label for="date">Date</label>
                        <input wire:model="form.shift.date" type="date" class="form-control" id="date" required>
                        @error('form.shift.date')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="shift">Shift</label>
                        <select wire:model="form.shift.shift" class="custom-select" id="shift" required>
                            <option value="">Select Shift</option>
                            <option value="shift i">Shift I</option>
                            <option value="shift ii">Shift II</option>
                            <option value="shift iii">Shift III</option>
                        </select>
                        @error('form.shift.shift')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6  mb-3">
                        <label for="startTime">Start Time</label>
                        <select wire:model="form.shift.start_time" class="custom-select" id="startTime" required>
                            <option value="">Select Jam Catatan</option>
                            <option value="01:00">01:00</option>
                            <option value="03:00">03:00</option>
                            <option value="05:00">05:00</option>
                            <option value="07:00">07:00</option>
                            <option value="09:00">09:00</option>
                            <option value="11:00">11:00</option>
                            <option value="13:00">13:00</option>
                            <option value="15:00">15:00</option>
                            <option value="17:00">17:00</option>
                            <option value="19:00">19:00</option>
                            <option value="21:00">21:00</option>
                            <option value="23:00">23:00</option>
                        </select>
                        @error('form.shift.start_time')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6  mb-3">
                        <label for="endTime">End Time</label>
                        <select wire:model="form.shift.end_time" class="custom-select" id="endTime" required>
                            <option value="">Select Jam Catatan</option>
                            <option value="01:00">01:00</option>
                            <option value="03:00">03:00</option>
                            <option value="05:00">05:00</option>
                            <option value="07:00">07:00</option>
                            <option value="09:00">09:00</option>
                            <option value="11:00">11:00</option>
                            <option value="13:00">13:00</option>
                            <option value="15:00">15:00</option>
                            <option value="17:00">17:00</option>
                            <option value="19:00">19:00</option>
                            <option value="21:00">21:00</option>
                            <option value="23:00">23:00</option>
                        </select>
                        @error('form.shift.end_time')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="operator1">Operator 1</label>
                        <select wire:model="form.shift.operator_1" class="custom-select" id="operator1" required>
                            <option value="">Select Operator</option>
                            @forelse ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @empty
                                <option value="">Users Not Available</option>
                            @endforelse
                        </select>
                        @error('form.shift.operator_1')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="Operator2">Operator 2</label>
                        <select wire:model="form.shift.operator_2" class="custom-select" id="Operator2">
                            <option value="">Select Operator</option>
                            @forelse ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @empty
                                <option value="">Users Not Available</option>
                            @endforelse
                        </select>
                        @error('form.shift.operator_2')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="exampleFormControlTextarea1">Note</label>
                        <textarea wire:model="form.shift.notes" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                        @error('form.shift.notes')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold mb-2">Air Baku</div>
                            <div class="col-6 mb-3">
                                <label for="phAirbaku">pH</label>
                                <input wire:model="form.airBaku.ph" type="number" step="any"
                                    class="form-control" id="phAirbaku" required>
                                @error('form.airBaku.ph')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label for="turbidityAirBaku"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Turbidity
                                    (NTU)</label>
                                <input wire:model="form.airBaku.turbidity" type="number" step="any"
                                    class="form-control" id="turbidityAirBaku" required>
                                @error('form.airBaku.turbidity')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label for="colorAirBaku"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Warna
                                    (PCU)</label>
                                <input wire:model="form.airBaku.color" type="number" step="any"
                                    class="form-control" id="colorAirBaku" required>
                                @error('form.airBaku.color')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label for="tdsAirBaku">TDS</label>
                                <input wire:model="form.airBaku.tds" type="number" step="any"
                                    class="form-control" id="tdsAirBaku" required>
                                @error('form.airBaku.tds')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 ">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold mb-2">Sedimentation</div>
                            <div class="col-6 mb-3">
                                <label for="phSedimentation">pH</label>
                                <input wire:model="form.sedimentation.ph" type="number" step="any"
                                    class="form-control" id="phSedimentation" required>
                                @error('form.sedimentation.ph')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 ">
                                <label for="turbiditySedimentation"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">
                                    Turbidity (NTU)
                                </label>
                                <input wire:model="form.sedimentation.turbidity" type="number" step="any"
                                    class="form-control" id="turbiditySedimentation" required>
                                @error('form.sedimentation.turbidity')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label for="colorSedimentation"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Warna
                                    (PCU)</label>
                                <input wire:model="form.sedimentation.color" type="number" step="any"
                                    class="form-control" id="colorSedimentation" required>
                                @error('form.sedimentation.color')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label for="tdsSedimentation">TDS</label>
                                <input wire:model="form.sedimentation.tds" type="number" step="any"
                                    class="form-control" id="tdsSedimentation" required>
                                @error('form.sedimentation.tds')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Reservoir</div>
                    <div class="col-6 col-md-3 mb-3">
                        <label for="phReservoir">pH</label>
                        <input wire:model="form.reservoir.ph" type="number" step="any" class="form-control"
                            id="phReservoir" required>
                        @error('form.reservoir.ph')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="turbidityReservoir"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Turbidity
                            (NTU)</label>
                        <input wire:model="form.reservoir.turbidity" type="number" step="any"
                            class="form-control" id="turbidityReservoir" required>
                        @error('form.reservoir.turbidity')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="pcuReservoir"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Warna
                            (PCU)</label>
                        <input wire:model="form.reservoir.color" type="number" step="any" class="form-control"
                            id="pcuReservoir" required>
                        @error('form.reservoir.color')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="tdsReservoir">TDS</label>
                        <input wire:model="form.reservoir.tds" type="number" step="any" class="form-control"
                            id="tdsReservoir" required>
                        @error('form.reservoir.tds')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="chlorReservoir"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Free
                            Chlor (mg/L)</label>
                        <input wire:model="form.reservoir.free_chlor" type="number" step="any"
                            class="form-control" id="chlorReservoir" required>
                        @error('form.reservoir.free_chlor')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="orpReservoir">ORP (mV)</label>
                        <input wire:model="form.reservoir.orp" type="number" step="any" class="form-control"
                            id="orpReservoir" required>
                        @error('form.reservoir.orp')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Flow Meter Air Baku</div>
                    <div class="col-6 mb-3">
                        <label for="airBakuFlow">Flow (L/s)</label>
                        <input wire:model="form.flowAirBaku.flow" type="number" step="any" class="form-control"
                            id="airBakuFlow" required>
                        @error('form.flowAirBaku.flow')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label for="airBakuTotalizer">Totalizer (m&#179)</label>
                        <input wire:model="form.flowAirBaku.totalizer" type="number" step="any"
                            class="form-control" id="airBakuTotalizer" required>
                        @error('form.flowAirBaku.totalizer')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Flowmeter Distribusi</div>
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Yos Sudarso</div>
                            <div class="col-6 mb-3">
                                <label for="sudarsoFlow">Flow (L/s)</label>
                                <input wire:model="form.flowSudarso.flow" type="number" step="any"
                                    class="form-control" id="sudarsoFlow" required>
                                @error('form.flowSudarso.flow')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label for="sudarsoTotalizer"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Totalizer
                                    (m&#179)</label>
                                <input wire:model="form.flowSudarso.totalizer" type="number" step="any"
                                    class="form-control" id="sudarsoTotalizer" required>
                                @error('form.flowSudarso.totalizer')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Veteran</div>
                            <div class="col-6 mb-3">
                                <label for="veteranFlow">Flow (L/s)</label>
                                <input wire:model="form.flowVeteran.flow" type="number" step="any"
                                    class="form-control" id="veteranFlow" required>
                                @error('form.flowVeteran.flow')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label for="veteranTotalizer"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Totalizer
                                    (m&#179)</label>
                                <input wire:model="form.flowVeteran.totalizer" type="number" step="any"
                                    class="form-control" id="veteranTotalizer" required>
                                @error('form.flowVeteran.totalizer')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Level Reservoir</div>
                    <div class="col-6 mb-3">
                        <label for="levelAReservoir">Level A (m)</label>
                        <input wire:model="form.reservoirLevel.level_a" type="number" step="any"
                            class="form-control" id="levelAReservoir" required>
                        @error('form.reservoirLevel.level_a')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label for="levelBReservoir">Level B (m)</label>
                        <input wire:model="form.reservoirLevel.level_b" type="number" step="any"
                            class="form-control" id="levelBReservoir" required>
                        @error('form.reservoirLevel.level_b')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">In Comer MDP Panel </div>
                    <div class="col-6 col-md-3 mb-3">
                        <label for="kwhMdpPanel">Kwh</label>
                        <input wire:model="form.mdpPanel.kwh_total" type="number" step="any"
                            class="form-control" id="kwhMdpPanel" required>
                        @error('form.mdpPanel.kwh_total')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="wbpMdpPanel">Wbp
                        </label>
                        <input wire:model="form.mdpPanel.wdp" type="number" step="any" class="form-control"
                            id="wbpMdpPanel" required>
                        @error('form.mdpPanel.mdpPanel.wdp')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="lwbpMdpPanel"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Lwbp
                        </label>
                        <input wire:model="form.mdpPanel.lwbp" type="number" step="any" class="form-control"
                            id="lwbpMdpPanel" required>
                        @error('form.mdpPanel.mdpPanel.lwbp')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="kvarMdpPanel">Kvar</label>
                        <input wire:model="form.mdpPanel.kvar" type="number" step="any" class="form-control"
                            id="kvarMdpPanel" required>
                        @error('form.mdpPanel.mdpPanel.kvar')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="cornerMdpPanel">Level Air Bak Pengumpul (m)</label>
                        <input wire:model="form.shift.collection_tank" type="number" step="any"
                            class="form-control" id="cornerMdpPanel" required>
                        @error('form.shift.collection_tank')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">⁠Pressure Static Mixer</div>
                    <div class="col-6 mb-3">
                        <label for="inlet">Inlet (Bar)</label>
                        <input wire:model="form.pressStatic.inlet" type="number" step="any"
                            class="form-control" id="inlet" required>
                        @error('form.pressStatic.inlet')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-6 mb-3">
                        <label for="outlet">Outlet (Bar)</label>
                        <input wire:model="form.pressStatic.outlet" type="number" step="any"
                            class="form-control" id="outlet" required>
                        @error('form.pressStatic.outlet')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Pompa Intake</div>
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa A</div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeAStatus">Status</label>
                                <select wire:model.live="form.pumpIntakeA.status" class="custom-select"
                                    id="pompaIntakeAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.pumpIntakeA.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpIntakeA.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaIntakeAAmpere">Ampere (A)</label>
                                    <input wire:model="form.pumpIntakeA.ampere" type="number" step="any"
                                        class="form-control" id="pompaIntakeAAmpere" required>
                                    @error('form.pumpIntakeA.ampere')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaIntakeAFreq"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                        (Hz)</label>
                                    <input wire:model="form.pumpIntakeA.frequency" type="number" step="any"
                                        class="form-control" id="pompaIntakeAFreq" required>
                                    @error('form.pumpIntakeA.frequency')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaIntakeAPress"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                        (Bar)</label>
                                    <input wire:model="form.pumpIntakeA.pressure" type="number" step="any"
                                        class="form-control" id="pompaIntakeAPress" required>
                                    @error('form.pumpIntakeA.pressure')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa B</div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeBStatus">Status</label>
                                <select wire:model.live="form.pumpIntakeB.status" class="custom-select"
                                    id="pompaIntakeBStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.pumpIntakeB.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpIntakeB.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaIntakeBAmpere">Ampere (A)</label>
                                    <input wire:model="form.pumpIntakeB.ampere" type="number" step="any"
                                        class="form-control" id="pompaIntakeBAmpere" required>
                                    @error('form.pumpIntakeB.ampere')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaIntakeBFreq"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                        (Hz)</label>
                                    <input wire:model="form.pumpIntakeB.frequency" type="number" step="any"
                                        class="form-control" id="pompaIntakeBFreq" required>
                                    @error('form.pumpIntakeB.frequency')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaIntakeBPress"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                        (Bar)</label>
                                    <input wire:model="form.pumpIntakeB.pressure" type="number" step="any"
                                        class="form-control" id="pompaIntakeBPress" required>
                                    @error('form.pumpIntakeB.pressure')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa C</div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeCStatus">Status</label>
                                <select wire:model.live="form.pumpIntakeC.status" class="custom-select"
                                    id="pompaIntakeCStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.pumpIntakeC.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            @if (data_get($this->form, 'pumpIntakeC.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaIntakeCAmpere">Ampere (A)</label>
                                    <input wire:model="form.pumpIntakeC.ampere" type="number" step="any"
                                        class="form-control" id="pompaIntakeCAmpere" required>
                                    @error('form.pumpIntakeC.ampere')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaIntakeCFreq"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                        (Hz)</label>
                                    <input wire:model="form.pumpIntakeC.frequency" type="number" step="any"
                                        class="form-control" id="pompaIntakeCFreq" required>
                                    @error('form.pumpIntakeC.frequency')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaIntakeCPress"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                        (Bar)</label>
                                    <input wire:model="form.pumpIntakeC.pressure" type="number" step="any"
                                        class="form-control" id="pompaIntakeCPress" required>
                                    @error('form.pumpIntakeC.pressure')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Pompa Distribusi</div>
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa A</div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiAStatus">Status</label>
                                <select wire:model.live="form.pumpDistriA.status" class="custom-select"
                                    id="pompaDistribusiAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.pumpDistriA.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            @if (data_get($this->form, 'pumpDistriA.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiAAmpere">Ampere (A)</label>
                                    <input wire:model="form.pumpDistriA.ampere" type="number" step="any"
                                        class="form-control" id="pompaDistribusiAAmpere" required>
                                    @error('form.pumpDistriA.ampere')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiAFreq"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                        (Hz)</label>
                                    <input wire:model="form.pumpDistriA.frequency" type="number" step="any"
                                        class="form-control" id="pompaDistribusiAFreq" required>
                                    @error('form.pumpDistriA.frequency')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiAPress"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                        (Bar)</label>
                                    <input wire:model="form.pumpDistriA.pressure" type="number" step="any"
                                        class="form-control" id="pompaDistribusiAPress" required>
                                    @error('form.pumpDistriA.pressure')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa B</div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiBStatus">Status</label>
                                <select wire:model.live="form.pumpDistriB.status" class="custom-select"
                                    id="pompaDistribusiBStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.pumpDistrB.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpDistriB.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiBAmpere">Ampere (A)</label>
                                    <input wire:model="form.pumpDistriB.ampere" type="number" step="any"
                                        class="form-control" id="pompaDistribusiBAmpere" required>
                                    @error('form.pumpDistriB.ampere')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiBFreq"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                        (Hz)</label>
                                    <input wire:model="form.pumpDistriB.frequency" type="number" step="any"
                                        class="form-control" id="pompaDistribusiBFreq" required>
                                    @error('form.pumpDistrB.frequency')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiBPress"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                        (Bar)</label>
                                    <input wire:model="form.pumpDistriB.pressure" type="number" step="any"
                                        class="form-control" id="pompaDistribusiBPress" required>
                                    @error('form.pumpDistrB.pressure')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa C</div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiCStatus">Status</label>
                                <select wire:model.live="form.pumpDistriC.status" class="custom-select"
                                    id="pompaDistribusiCStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.pumpDistriC.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpDistriC.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiCAmpere">Ampere (A)</label>
                                    <input wire:model="form.pumpDistriC.ampere" type="number" step="any"
                                        class="form-control" id="pompaDistribusiCAmpere" required>
                                    @error('form.pumpDistriC.ampere')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiCFreq"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                        (Hz)</label>
                                    <input wire:model="form.pumpDistriC.frequency" type="number" step="any"
                                        class="form-control" id="pompaDistribusiCFreq" required>
                                    @error('form.pumpDistriC.frequency')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiCPress"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                        (Bar)</label>
                                    <input wire:model="form.pumpDistriC.pressure" type="number" step="any"
                                        class="form-control" id="pompaDistribusiCPress" required>
                                    @error('form.pumpDistriC.pressure')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa D</div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiDStatus">Status</label>
                                <select wire:model.live="form.pumpDistriD.status" class="custom-select"
                                    id="pompaDistribusiDStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.pumpDistriD.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            @if (data_get($this->form, 'pumpDistriD.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiDAmpere">Ampere (A)</label>
                                    <input wire:model="form.pumpDistriD.ampere" type="number" step="any"
                                        class="form-control" id="pompaDistribusiDAmpere" required>
                                    @error('form.pumpDistriD.ampere')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiDFreq"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                        (Hz)</label>
                                    <input wire:model="form.pumpDistriD.frequency" type="number" step="any"
                                        class="form-control" id="pompaDistribusiDFreq" required>
                                    @error('form.pumpDistriD.frequency')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaDistribusiDPress"
                                        style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                        (Bar)</label>
                                    <input wire:model="form.pumpDistriD.pressure" type="number" step="any"
                                        class="form-control" id="pompaDistribusiDPress" required>
                                    @error('form.pumpDistriD.pressure')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>
                
                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Pompa Dosing PAC</div>
                    
                    {{-- Pompa A --}}
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa A</div>
                            <div class="col-6 mb-3">
                                <label for="pompaPacAStatus">Status</label>
                                <select wire:model.live="form.pumpPacA.status" class="custom-select" id="pompaPacAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                                @error('form.pumpPacA.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpPacA.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaPacAFreq" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi (Hz)</label>
                                    <input wire:model="form.pumpPacA.frequency" type="number" step="any" class="form-control" id="pompaPacAFreq" required>
                                    @error('form.pumpPacA.frequency')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPacADosis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis (ppm)</label>
                                    <input wire:model="form.pumpPacA.dosage" type="number" step="any" class="form-control" id="pompaPacADosis" required>
                                    @error('form.pumpPacA.dosage')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPacAKonsen" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi (%)</label>
                                    <input wire:model="form.pumpPacA.concentration" type="number" step="any" class="form-control" id="pompaPacAKonsen" required>
                                    @error('form.pumpPacA.concentration')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPacATankLv">Level Tangki (cm)</label>
                                    <input wire:model="form.pumpPacA.tank_level" type="number" step="any" class="form-control" id="pompaPacATankLv" required>
                                    @error('form.pumpPacA.tank_level')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Pompa B --}}
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa B</div>
                            <div class="col-6 mb-3">
                                <label for="pompaPacBStatus">Status</label>
                                <select wire:model.live="form.pumpPacB.status" class="custom-select" id="pompaPacBStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                                @error('form.pumpPacB.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpPacB.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaPacBFreq" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi (Hz)</label>
                                    <input wire:model="form.pumpPacB.frequency" type="number" step="any" class="form-control" id="pompaPacBFreq" required>
                                    @error('form.pumpPacB.frequency')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPacBDosis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis (ppm)</label>
                                    <input wire:model="form.pumpPacB.dosage" type="number" step="any" class="form-control" id="pompaPacBDosis" required>
                                    @error('form.pumpPacB.dosage')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPacBKonsen" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi (%)</label>
                                    <input wire:model="form.pumpPacB.concentration" type="number" step="any" class="form-control" id="pompaPacBKonsen" required>
                                    @error('form.pumpPacB.concentration')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPacBTankLv">Level Tangki (cm)</label>
                                    <input wire:model="form.pumpPacB.tank_level" type="number" step="any" class="form-control" id="pompaPacBTankLv" required>
                                    @error('form.pumpPacB.tank_level')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Pompa Dosing Chlorine/Kaporit</div>
                    
                    {{-- Pompa A --}}
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa A</div>
                            <div class="col-6 mb-3">
                                <label for="pompaChlorAStatus">Status</label>
                                <select wire:model.live="form.pumpChlorA.status" class="custom-select" id="pompaChlorAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                                @error('form.pumpChlorA.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpChlorA.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaChlorAFlowrate" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Flowrate (lh)</label>
                                    <input wire:model="form.pumpChlorA.flow_rate" type="number" step="any" class="form-control" id="pompaChlorAFlowrate" required>
                                    @error('form.pumpChlorA.flow_rate')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaChlorADosis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis (ppm)</label>
                                    <input wire:model="form.pumpChlorA.dosage" type="number" step="any" class="form-control" id="pompaChlorADosis" required>
                                    @error('form.pumpChlorA.dosage')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaChlorAKonsen" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi (%)</label>
                                    <input wire:model="form.pumpChlorA.concentration" type="number" step="any" class="form-control" id="pompaChlorAKonsen" required>
                                    @error('form.pumpChlorA.concentration')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaChlorAPengaduk" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pengadukan (Kg)</label>
                                    <input wire:model="form.pumpChlorA.stirring" type="number" step="any" class="form-control" id="pompaChlorAPengaduk" required>
                                    @error('form.pumpChlorA.stirring')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaChlorATankLv">Level Tangki (cm)</label>
                                    <input wire:model="form.pumpChlorA.tank_level" type="number" step="any" class="form-control" id="pompaChlorATankLv" required>
                                    @error('form.pumpChlorA.tank_level')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Pompa B --}}
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa B</div>
                            <div class="col-6 mb-3">
                                <label for="pompaChlorBStatus">Status</label>
                                <select wire:model.live="form.pumpChlorB.status" class="custom-select" id="pompaChlorBStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                                @error('form.pumpChlorB.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpChlorB.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaChlorBFlowrate" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Flowrate (lh)</label>
                                    <input wire:model="form.pumpChlorB.flow_rate" type="number" step="any" class="form-control" id="pompaChlorBFlowrate" required>
                                    @error('form.pumpChlorB.flow_rate')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaChlorBDosis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis (ppm)</label>
                                    <input wire:model="form.pumpChlorB.dosage" type="number" step="any" class="form-control" id="pompaChlorBDosis" required>
                                    @error('form.pumpChlorB.dosage')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaChlorBKonsen" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi (%)</label>
                                    <input wire:model="form.pumpChlorB.concentration" type="number" step="any" class="form-control" id="pompaChlorBKonsen" required>
                                    @error('form.pumpChlorB.concentration')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaChlorBPengaduk" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pengadukan (Kg)</label>
                                    <input wire:model="form.pumpChlorB.stirring" type="number" step="any" class="form-control" id="pompaChlorBPengaduk" required>
                                    @error('form.pumpChlorB.stirring')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaChlorBTankLv">Level Tangki (cm)</label>
                                    <input wire:model="form.pumpChlorB.tank_level" type="number" step="any" class="form-control" id="pompaChlorBTankLv" required>
                                    @error('form.pumpChlorB.tank_level')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Pompa Dosing Soda Ash</div>
                    
                    {{-- Pompa A --}}
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa A</div>
                            <div class="col-6 mb-3">
                                <label for="pompaSodaAStatus">Status</label>
                                <select wire:model.live="form.pumpSodaA.status" class="custom-select" id="pompaSodaAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                                @error('form.pumpSodaA.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpSodaA.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaSodaAFlowRate" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Flow Rate %</label>
                                    <input wire:model="form.pumpSodaA.flow_rate" type="number" step="any" class="form-control" id="pompaSodaAFlowRate" required>
                                    @error('form.pumpSodaA.flow_rate')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaSodaADosis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis (ppm)</label>
                                    <input wire:model="form.pumpSodaA.dosage" type="number" step="any" class="form-control" id="pompaSodaADosis" required>
                                    @error('form.pumpSodaA.dosage')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaSodaAKonsen" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi (%)</label>
                                    <input wire:model="form.pumpSodaA.concentration" type="number" step="any" class="form-control" id="pompaSodaAKonsen" required>
                                    @error('form.pumpSodaA.concentration')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaSodaAPengaduk" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pengadukan (Kg)</label>
                                    <input wire:model="form.pumpSodaA.stirring" type="number" step="any" class="form-control" id="pompaSodaAPengaduk" required>
                                    @error('form.pumpSodaA.stirring')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaSodaATankLv">Level Tangki (cm)</label>
                                    <input wire:model="form.pumpSodaA.tank_level" type="number" step="any" class="form-control" id="pompaSodaATankLv" required>
                                    @error('form.pumpSodaA.tank_level')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Pompa B --}}
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa B</div>
                            <div class="col-6 mb-3">
                                <label for="pompaSodaBStatus">Status</label>
                                <select wire:model.live="form.pumpSodaB.status" class="custom-select" id="pompaSodaBStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                                @error('form.pumpSodaB.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpSodaB.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaSodaBFlowRate" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Flow Rate %</label>
                                    <input wire:model="form.pumpSodaB.flow_rate" type="number" step="any" class="form-control" id="pompaSodaBFlowRate" required>
                                    @error('form.pumpSodaB.flow_rate')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaSodaBDosis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis (ppm)</label>
                                    <input wire:model="form.pumpSodaB.dosage" type="number" step="any" class="form-control" id="pompaSodaBDosis" required>
                                    @error('form.pumpSodaB.dosage')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaSodaBKonsen" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi (%)</label>
                                    <input wire:model="form.pumpSodaB.concentration" type="number" step="any" class="form-control" id="pompaSodaBKonsen" required>
                                    @error('form.pumpSodaB.concentration')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaSodaBPengaduk" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pengadukan (Kg)</label>
                                    <input wire:model="form.pumpSodaB.stirring" type="number" step="any" class="form-control" id="pompaSodaBPengaduk" required>
                                    @error('form.pumpSodaB.stirring')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaSodaBTankLv">Level Tangki (cm)</label>
                                    <input wire:model="form.pumpSodaB.tank_level" type="number" step="any" class="form-control" id="pompaSodaBTankLv" required>
                                    @error('form.pumpSodaB.tank_level')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Pompa Dosing Polymer</div>
                    
                    {{-- Pompa A --}}
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa A</div>
                            <div class="col-6 mb-3">
                                <label for="pompaPolymerAStatus">Status</label>
                                <select wire:model.live="form.pumpPolymerA.status" class="custom-select" id="pompaPolymerAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                                @error('form.pumpPolymerA.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpPolymerA.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaPolymerAFlowRate" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Flow Rate %</label>
                                    <input wire:model="form.pumpPolymerA.flow_rate" type="number" step="any" class="form-control" id="pompaPolymerAFlowRate" required>
                                    @error('form.pumpPolymerA.flow_rate')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPolymerADosis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis (ppm)</label>
                                    <input wire:model="form.pumpPolymerA.dosage" type="number" step="any" class="form-control" id="pompaPolymerADosis" required>
                                    @error('form.pumpPolymerA.dosage')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPolymerAKonsen" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi (%)</label>
                                    <input wire:model="form.pumpPolymerA.concentration" type="number" step="any" class="form-control" id="pompaPolymerAKonsen" required>
                                    @error('form.pumpPolymerA.concentration')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPolymerAPengaduk" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pengadukan (Kg)</label>
                                    <input wire:model="form.pumpPolymerA.stirring" type="number" step="any" class="form-control" id="pompaPolymerAPengaduk" required>
                                    @error('form.pumpPolymerA.stirring')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPolymerATankLv">Level Tangki (cm)</label>
                                    <input wire:model="form.pumpPolymerA.tank_level" type="number" step="any" class="form-control" id="pompaPolymerATankLv" required>
                                    @error('form.pumpPolymerA.tank_level')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Pompa B --}}
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa B</div>
                            <div class="col-6 mb-3">
                                <label for="pompaPolymerBStatus">Status</label>
                                <select wire:model.live="form.pumpPolymerB.status" class="custom-select" id="pompaPolymerBStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                                @error('form.pumpPolymerB.status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'pumpPolymerB.status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="pompaPolymerBFlowRate" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Flow Rate %</label>
                                    <input wire:model="form.pumpPolymerB.flow_rate" type="number" step="any" class="form-control" id="pompaPolymerBFlowRate" required>
                                    @error('form.pumpPolymerB.flow_rate')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPolymerBDosis" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis (ppm)</label>
                                    <input wire:model="form.pumpPolymerB.dosage" type="number" step="any" class="form-control" id="pompaPolymerBDosis" required>
                                    @error('form.pumpPolymerB.dosage')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPolymerBKonsen" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi (%)</label>
                                    <input wire:model="form.pumpPolymerB.concentration" type="number" step="any" class="form-control" id="pompaPolymerBKonsen" required>
                                    @error('form.pumpPolymerB.concentration')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPolymerBPengaduk" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pengadukan (Kg)</label>
                                    <input wire:model="form.pumpPolymerB.stirring" type="number" step="any" class="form-control" id="pompaPolymerBPengaduk" required>
                                    @error('form.pumpPolymerB.stirring')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="pompaPolymerBTankLv">Level Tangki (cm)</label>
                                    <input wire:model="form.pumpPolymerB.tank_level" type="number" step="any" class="form-control" id="pompaPolymerBTankLv" required>
                                    @error('form.pumpPolymerB.tank_level')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a class="btn btn-secondary" href="/monitoring-index" wire:navigated type="button">Back</a>
                    <button class="btn btn-primary ml-2" type="submit">{{ $id ? 'Update' : 'Submit' }} </button>
                </div>
            </form>
        </div>
    </div>
</div>
    
