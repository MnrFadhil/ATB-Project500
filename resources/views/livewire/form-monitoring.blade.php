<div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $id ? 'Edit' : 'Create' }} Record</h1>
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
                            <option value="00:00">00:00</option>
                            <option value="01:00">01:00</option>
                            <option value="02:00">02:00</option>
                            <option value="03:00">03:00</option>
                            <option value="04:00">04:00</option>
                            <option value="05:00">05:00</option>
                            <option value="06:00">06:00</option>
                            <option value="07:00">07:00</option>
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="12:00">12:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                            <option value="18:00">18:00</option>
                            <option value="19:00">19:00</option>
                            <option value="20:00">20:00</option>
                            <option value="21:00">21:00</option>
                            <option value="22:00">22:00</option>
                            <option value="23:00">23:00</option>
                            <option value="24:00">24:00</option>
                        </select>
                        @error('form.shift.start_time')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6  mb-3">
                        <label for="endTime">End Time</label>
                        <select wire:model="form.shift.end_time" class="custom-select" id="endTime" required>
                            <option value="00:00">00:00</option>
                            <option value="01:00">01:00</option>
                            <option value="02:00">02:00</option>
                            <option value="03:00">03:00</option>
                            <option value="04:00">04:00</option>
                            <option value="05:00">05:00</option>
                            <option value="06:00">06:00</option>
                            <option value="07:00">07:00</option>
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="12:00">12:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                            <option value="18:00">18:00</option>
                            <option value="19:00">19:00</option>
                            <option value="20:00">20:00</option>
                            <option value="21:00">21:00</option>
                            <option value="22:00">22:00</option>
                            <option value="23:00">23:00</option>
                            <option value="24:00">24:00</option>
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
                                <select wire:model="form.pumpDistriC.status" class="custom-select"
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
                                <select wire:model="form.pumpDistriD.status" class="custom-select"
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
                    <div class="col-6 mb-3">
                        <label for="pompaPacFreq"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                            (Hz)</label>
                        <input wire:model="form.pumpPac.frequency" type="number" step="any"
                            class="form-control" id="pompaPacFreq" required>
                        @error('form.pumpPac.frequency')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaPacDosis"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis
                            (ppm)</label>
                        <input wire:model="form.pumpPac.dosage" type="number" step="any" class="form-control"
                            id="pompaPacDosis" required>
                        @error('form.pumpPac.dosage')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaPacKonsen"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi
                            (%)</label>
                        <input wire:model="form.pumpPac.concentration" type="number" step="any"
                            class="form-control" id="pompaPacKonsen" required>
                        @error('form.pumpPac.concentration')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaPacPengaduk"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pengadukan
                            (Kg)</label>
                        <input wire:model="form.pumpPac.stirring" type="number" step="any" class="form-control"
                            id="pompaPacPengaduk" required>
                        @error('form.pumpPac.stirring')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaPacTankLv">Level Tangki</label>
                        <input wire:model="form.pumpPac.tank_level" type="number" step="any"
                            class="form-control" id="pompaPacTankLv" required>
                        @error('form.pumpPac.tank_level')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Pompa Dosing Clorine/Kaporit </div>
                    <div class="col-6 mb-3">
                        <label for="pompaDosFlowrate"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Flowrate
                            (lh)</label>
                        <input wire:model="form.pumpChlor.flow_rate" type="number" step="any"
                            class="form-control" id="pompaDosFlowrate" required>
                        @error('form.pumpChlor.flow_rate')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaDosDosis"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis
                            (ppm)</label>
                        <input wire:model="form.pumpChlor.dosage" type="number" step="any" class="form-control"
                            id="pompaDosDosis" required>
                        @error('form.pumpChlor.dosage')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaDosKonsen"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi
                            (%)</label>
                        <input wire:model="form.pumpChlor.concentration" type="number" step="any"
                            class="form-control" id="pompaDosKonsen" required>
                        @error('form.pumpChlor.concentration')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaDosPengaduk"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pengadukan
                            (Kg)</label>
                        <input wire:model="form.pumpChlor.stirring" type="number" step="any"
                            class="form-control" id="pompaDosPengaduk" required>
                        @error('form.pumpChlor.stirring')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaDosTankLv">Level Tangki</label>
                        <input wire:model="form.pumpChlor.tank_level" type="number" step="any"
                            class="form-control" id="pompaDosTankLv" required>
                        @error('form.pumpChlor.tank_level')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-6 mb-3">
                        <label for="barScreen">Bar Screen</label>
                        <select wire:model="form.unitOper.barscreen" class="custom-select" id="barScreen" required>
                            <option value="normal">Normal</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        @error('form.unitOper.barscreen')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 mb-3">
                        <label for="airDrayer">Air Dryer</label>
                        <select wire:model="form.unitOper.air_drayer" class="custom-select" id="airDrayer" required>
                            <option value="normal">Normal</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        @error('form.unitOper.air_drayer')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="fineScreenA">Fine Screen A</label>
                        <select wire:model="form.unitOper.finescreen_a" class="custom-select" id="fineScreenA"
                            required>
                            <option value="normal">Normal</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        @error('form.unitOper.finescreen_a')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="fineScreenB">Fine Screen B</label>
                        <select wire:model="form.unitOper.finescreen_b" class="custom-select" id="fineScreenB"
                            required>
                            <option value="normal">Normal</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        @error('form.unitOper.finescreen_b')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="compressorA">Compressor A</label>
                        <select wire:model="form.unitOper.compressor_a" class="custom-select" id="compressorA"
                            required>
                            <option value="normal">Normal</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        @error('form.unitOper.compressor_a')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="compressorB">Compressor B</label>
                        <select wire:model="form.unitOper.compressor_b" class="custom-select" id="compressorB"
                            required>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        @error('form.unitOper.compressor_b')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">WTP</div>
                    <div class="col-6 col-md-3 mb-3">
                        <label for="wtpFlocA">Flocullation A</label>
                        <select wire:model="form.wtp.flokulator_a" class="custom-select" id="wtpFlocA" required>
                            <option value="off">Off</option>
                            <option value="on">On</option>
                        </select>
                        @error('form.wtp.flokulator_a')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="wtpFlocB">Flocullation B</label>
                        <select wire:model="form.wtp.flokulator_b" class="custom-select" id="wtpFlocB" required>
                            <option value="off">Off</option>
                            <option value="on">On</option>
                        </select>
                        @error('form.wtp.flokulator_b')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="wtpClafA">Clarifier A</label>
                        <select wire:model="form.wtp.clarifier_a" class="custom-select" id="wtpClafA" required>
                            <option value="off">Off</option>
                            <option value="on">On</option>
                        </select>
                        @error('form.wtp.clarifier_a')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="wtpClafB">Clarifier B</label>
                        <select wire:model="form.wtp.clarifier_b" class="custom-select" id="wtpClafB" required>
                            <option value="off">Off</option>
                            <option value="on">On</option>
                        </select>
                        @error('form.wtp.clarifier_b')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label for="wtpFiltrasi">Filtrasi</label>
                        <select wire:model="form.wtp.filtration" class="custom-select" id="wtpFiltrasi" required>
                            <option value="off">Off</option>
                            <option value="on">On</option>
                        </select>
                        @error('form.wtp.filtration')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2" style="font-size: 15px">Level Grafity Filtrasi</div>
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Filtrasi A</div>
                            <div class="col-6 mb-3">
                                <label for="filtrasiAStatus">Status</label>
                                <select wire:model.live="form.wtp.gravity_filter_a_status" class="custom-select"
                                    id="filtrasiAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.wtp.gravity_filter_a_status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'wtp.gravity_filter_a_status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="filtrasiA">(m)</label>
                                    <input wire:model="form.wtp.gravity_filter_a" type="number" step="any"
                                        class="form-control" id="filtrasiA" required>
                                    @error('form.wtp.gravity_filter_a')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Filtrasi B</div>
                            <div class="col-6 mb-3">
                                <label for="filtrasiAStatus">Status</label>
                                <select wire:model.live="form.wtp.gravity_filter_b_status" class="custom-select"
                                    id="filtrasiAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.wtp.gravity_filter_b_status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'wtp.gravity_filter_b_status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="filtrasiA">(m)</label>
                                    <input wire:model="form.wtp.gravity_filter_b" type="number" step="any"
                                        class="form-control" id="filtrasiA" required>
                                    @error('form.wtp.gravity_filter_b')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Filtrasi C</div>
                            <div class="col-6 mb-3">
                                <label for="filtrasiAStatus">Status</label>
                                <select wire:model.live="form.wtp.gravity_filter_c_status" class="custom-select"
                                    id="filtrasiAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.wtp.gravity_filter_c_status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'wtp.gravity_filter_c_status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="filtrasiA">(m)</label>
                                    <input wire:model="form.wtp.gravity_filter_c" type="number" step="any"
                                        class="form-control" id="filtrasiA" required>
                                    @error('form.wtp.gravity_filter_c')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Filtrasi D</div>
                            <div class="col-6 mb-3">
                                <label for="filtrasiAStatus">Status</label>
                                <select wire:model.live="form.wtp.gravity_filter_d_status" class="custom-select"
                                    id="filtrasiAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.wtp.gravity_filter_d_status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'wtp.gravity_filter_d_status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="filtrasiA">(m)</label>
                                    <input wire:model="form.wtp.gravity_filter_d" type="number" step="any"
                                        class="form-control" id="filtrasiA" required>
                                    @error('form.wtp.gravity_filter_d')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Filtrasi E</div>
                            <div class="col-6 mb-3">
                                <label for="filtrasiAStatus">Status</label>
                                <select wire:model.live="form.wtp.gravity_filter_e_status" class="custom-select"
                                    id="filtrasiAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.wtp.gravity_filter_e_status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'wtp.gravity_filter_e_status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="filtrasiA">(m)</label>
                                    <input wire:model="form.wtp.gravity_filter_e" type="number" step="any"
                                        class="form-control" id="filtrasiA" required>
                                    @error('form.wtp.gravity_filter_e')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Filtrasi F</div>
                            <div class="col-6 mb-3">
                                <label for="filtrasiAStatus">Status</label>
                                <select wire:model.live="form.wtp.gravity_filter_f_status" class="custom-select"
                                    id="filtrasiAStatus" required>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('form.wtp.gravity_filter_f_status')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (data_get($this->form, 'wtp.gravity_filter_f_status') == 'running')
                                <div class="col-6 mb-3">
                                    <label for="filtrasiA">(m)</label>
                                    <input wire:model="form.wtp.gravity_filter_f" type="number" step="any"
                                        class="form-control" id="filtrasiA" required>
                                    @error('form.wtp.gravity_filter_f')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a class="btn btn-secondary" href="/monitoring-index" wire:navigated type="button">Back</a>
                    <button class="btn btn-primary ml-2" type="submit">{{ $id ? 'Update' : 'Submit' }} </button>
                </div>
            </form>
        </div>
    </div>
</div>
