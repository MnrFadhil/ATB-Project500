<div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Record</h1>
    </div>

    {{-- Form --}}

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold ">Forms</h6>
        </div>
        <div class="card-body">
            <form>
                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Shift</div>
                    <div class="col-md-6 mb-3">
                        <label for="date">Date</label>
                        <input type="date" class="form-control" id="date" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="shift">Shift</label>
                        <select class="custom-select" id="shift" required>
                            <option value="shift i">Shift I</option>
                            <option value="shift ii">Shift II</option>
                            <option value="shift iii">Shift III</option>
                        </select>
                    </div>

                    <div class="col-6  mb-3">
                        <label for="startTime">Start Time</label>
                        <select class="custom-select" id="startTime" required>
                            <option value="'00:00'">00:00</option>
                            <option value="'02:00'">02:00</option>
                            <option value="'05:00'">05:00</option>
                            <option value="'07:00'">07:00</option>
                            <option value="'09:00'">09:00</option>
                            <option value="'11:00'">11:00</option>
                            <option value="'13:00'">13:00</option>
                            <option value="'15:00'">15:00</option>
                            <option value="'17:00'">17:00</option>
                            <option value="'19:00'">19:00</option>
                            <option value="'21:00'">21:00</option>
                            <option value="'23:00'">23:00</option>
                        </select>
                    </div>

                    <div class="col-6  mb-3">
                        <label for="endTime">End Time</label>
                        <select class="custom-select" id="endTime" required>
                            <option value="'00:00'">00:00</option>
                            <option value="'02:00'">02:00</option>
                            <option value="'05:00'">05:00</option>
                            <option value="'07:00'">07:00</option>
                            <option value="'09:00'">09:00</option>
                            <option value="'11:00'">11:00</option>
                            <option value="'13:00'">13:00</option>
                            <option value="'15:00'">15:00</option>
                            <option value="'17:00'">17:00</option>
                            <option value="'19:00'">19:00</option>
                            <option value="'21:00'">21:00</option>
                            <option value="'23:00'">23:00</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="operator1">Operator 1</label>
                        <select class="custom-select" id="operator1" required>
                            <option value="">Test</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="Operator2">Operator 2</label>
                        <select class="custom-select" id="Operator2" required>
                            <option value="">Test</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="exampleFormControlTextarea1">Note</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold mb-2">Air Baku</div>
                            <div class="col-6 mb-3">
                                <label for="phAirbaku">pH</label>
                                <input type="number" class="form-control" id="phAirbaku" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="turbidityAirBaku"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Turbidity
                                    (NTU)</label>
                                <input type="number" class="form-control" id="turbidityAirBaku" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="colorAirBaku"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Warna
                                    (PCU)</label>
                                <input type="number" class="form-control" id="colorAirBaku" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="tdsAirBaku">TDS</label>
                                <input type="number" class="form-control" id="tdsAirBaku" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 ">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold mb-2">Sedimentation</div>
                            <div class="col-6 mb-3">
                                <label for="phSedimentation">pH</label>
                                <input type="number" class="form-control" id="phSedimentation" required>
                            </div>
                            <div class="col-6 ">
                                <label for="turbiditySedimentation"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">
                                    Turbidity (NTU)
                                </label>

                                <input type="number" class="form-control" id="turbiditySedimentation" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="colorSedimentation"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Warna
                                    (PCU)</label>
                                <input type="number" class="form-control" id="colorSedimentation" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="tdsSedimentation">TDS</label>
                                <input type="number" class="form-control" id="tdsSedimentation" required>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Resrvoir</div>
                    <div class="col-6 col-md-3 mb-3">
                        <label for="phReservoir">pH</label>
                        <input type="number" class="form-control" id="phReservoir" required>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="turbidityReservoir"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Turbidity
                            (NTU)</label>
                        <input type="number" class="form-control" id="turbidityReservoir" required>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="pcuReservoir"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Warna
                            (PCU)</label>
                        <input type="number" class="form-control" id="pcuReservoir" required>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="tdsReservoir">TDS</label>
                        <input type="number" class="form-control" id="tdsReservoir" required>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="chlorReservoir"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Free
                            Chlor (mg/L)</label>
                        <input type="number" class="form-control" id="chlorReservoir" required>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="orpReservoir">ORP (mV)</label>
                        <input type="number" class="form-control" id="orpReservoir" required>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Flow Meter Air Baku</div>
                    <div class="col-6 mb-3">
                        <label for="airBakuFlow">Flow (L/s)</label>
                        <input type="number" class="form-control" id="airBakuFlow" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label for="airBakuTotalizer">Totalizer (m&#179)</label>
                        <input type="number" class="form-control" id="airBakuTotalizer" required>
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
                                <input type="number" class="form-control" id="sudarsoFlow" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="sudarsoTotalizer"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Totalizer
                                    (m&#179)</label>
                                <input type="number" class="form-control" id="sudarsoTotalizer" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Veteran</div>
                            <div class="col-6 mb-3">
                                <label for="veteranFlow">Flow (L/s)</label>
                                <input type="number" class="form-control" id="veteranFlow" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="veteranTotalizer"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Totalizer
                                    (m&#179)</label>
                                <input type="number" class="form-control" id="veteranTotalizer" required>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Level Reservoir</div>
                    <div class="col-6 mb-3">
                        <label for="levelAReservoir">Level A (m)</label>
                        <input type="number" class="form-control" id="levelAReservoir" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label for="levelBReservoir">Level B (m)</label>
                        <input type="number" class="form-control" id="levelBReservoir" required>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">In Comer MDP Panel </div>
                    <div class="col-6 col-md-3 mb-3">
                        <label for="kwhMdpPanel">Kwh</label>
                        <input type="number" class="form-control" id="kwhMdpPanel" required>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="wbpMdpPanel">Wbp
                            (NTU)</label>
                        <input type="number" class="form-control" id="wbpMdpPanel" required>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="lwbpMdpPanel"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Lwbp
                            (PCU)</label>
                        <input type="number" class="form-control" id="lwbpMdpPanel" required>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="kvarMdpPanel">Kvar</label>
                        <input type="number" class="form-control" id="kvarMdpPanel" required>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">In Comer MDP Panel </div>
                    <div class="col-md-6 mb-3">
                        <label for="cornerMdpPanel">Level Air Bak Pengumpul (m)</label>
                        <input type="number" class="form-control" id="cornerMdpPanel" required>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">⁠Pressure Static Mixer</div>
                    <div class="col-6 mb-3">
                        <label for="inlet">Inlet (Bar)</label>
                        <input type="number" class="form-control" id="inlet" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label for="outlet">Outlet (Bar)</label>
                        <input type="number" class="form-control" id="outlet" required>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Pompa Intake</div>
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa A</div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeAAmpere">Ampere (A)</label>
                                <input type="number" class="form-control" id="pompaIntakeAAmpere" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeAFreq"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                    (Hz)</label>
                                <input type="number" class="form-control" id="pompaIntakeAFreq" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeAPress"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                    (Bar)</label>
                                <input type="number" class="form-control" id="pompaIntakeAPress" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeAStatus">Status</label>
                                <select class="custom-select" id="pompaIntakeAStatus" required>
                                    <option value="normal">Normal</option>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa B</div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeBAmpere">Ampere (A)</label>
                                <input type="number" class="form-control" id="pompaIntakeBAmpere" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeBFreq"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                    (Hz)</label>
                                <input type="number" class="form-control" id="pompaIntakeBFreq" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeBPress"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                    (Bar)</label>
                                <input type="number" class="form-control" id="pompaIntakeBPress" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeBStatus">Status</label>
                                <select class="custom-select" id="pompaIntakeBStatus" required>
                                    <option value="normal">Normal</option>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa C</div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeCAmpere">Ampere (A)</label>
                                <input type="number" class="form-control" id="pompaIntakeCAmpere" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeCFreq"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                    (Hz)</label>
                                <input type="number" class="form-control" id="pompaIntakeCFreq" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeCPress"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                    (Bar)</label>
                                <input type="number" class="form-control" id="pompaIntakeCPress" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaIntakeCStatus">Status</label>
                                <select class="custom-select" id="pompaIntakeCStatus" required>
                                    <option value="normal">Normal</option>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                            </div>
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
                                <label for="pompaDistribusiAAmpere">Ampere (A)</label>
                                <input type="number" class="form-control" id="pompaDistribusiAAmpere" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiAFreq"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                    (Hz)</label>
                                <input type="number" class="form-control" id="pompaDistribusiAFreq" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiAPress"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                    (Bar)</label>
                                <input type="number" class="form-control" id="pompaDistribusiAPress" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiAStatus">Status</label>
                                <select class="custom-select" id="pompaDistribusiAStatus" required>
                                    <option value="normal">Normal</option>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa B</div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiBAmpere">Ampere (A)</label>
                                <input type="number" class="form-control" id="pompaDistribusiBAmpere" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiBFreq"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                    (Hz)</label>
                                <input type="number" class="form-control" id="pompaDistribusiBFreq" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiBPress"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                    (Bar)</label>
                                <input type="number" class="form-control" id="pompaDistribusiBPress" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiBStatus">Status</label>
                                <select class="custom-select" id="pompaDistribusiBStatus" required>
                                    <option value="normal">Normal</option>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa C</div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiCAmpere">Ampere (A)</label>
                                <input type="number" class="form-control" id="pompaDistribusiCAmpere" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiCFreq"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                    (Hz)</label>
                                <input type="number" class="form-control" id="pompaDistribusiCFreq" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiCPress"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                    (Bar)</label>
                                <input type="number" class="form-control" id="pompaDistribusiCPress" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiCStatus">Status</label>
                                <select class="custom-select" id="pompaDistribusiCStatus" required>
                                    <option value="normal">Normal</option>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-12 font-weight-bold">Pompa D</div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiDAmpere">Ampere (A)</label>
                                <input type="number" class="form-control" id="pompaDistribusiDAmpere" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiDFreq"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Frekuensi
                                    (Hz)</label>
                                <input type="number" class="form-control" id="pompaDistribusiDFreq" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiDPress"
                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pressure
                                    (Bar)</label>
                                <input type="number" class="form-control" id="pompaDistribusiDPress" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="pompaDistribusiDStatus">Status</label>
                                <select class="custom-select" id="pompaDistribusiDStatus" required>
                                    <option value="normal">Normal</option>
                                    <option value="standby">Standby</option>
                                    <option value="running">Running</option>
                                </select>
                            </div>
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
                        <input type="number" class="form-control" id="pompaPacFreq" required>
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaPacDosis"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis
                            (ppm)</label>
                        <input type="number" class="form-control" id="pompaPacDosis" required>
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaPacKonsen"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi
                            (%)</label>
                        <input type="number" class="form-control" id="pompaPacKonsen" required>
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaPacPengaduk"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pengadukan
                            (Kg)</label>
                        <input type="number" class="form-control" id="pompaPacPengaduk" required>
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaPacTankLv">Level Tangki</label>
                        <input type="number" class="form-control" id="pompaPacTankLv" required>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">Pompa Dosing Clorine/Kaporit </div>
                    <div class="col-6 mb-3">
                        <label for="pompaDosFlowrate"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Flowrate
                            (lh)</label>
                        <input type="number" class="form-control" id="pompaDosFlowrate" required>
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaDosDosis"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Dosis
                            (ppm)</label>
                        <input type="number" class="form-control" id="pompaDosDosis" required>
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaDosKonsen"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Konsentrasi
                            (%)</label>
                        <input type="number" class="form-control" id="pompaDosKonsen" required>
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaDosPengaduk"
                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">Pengadukan
                            (Kg)</label>
                        <input type="number" class="form-control" id="pompaDosPengaduk" required>
                    </div>

                    <div class="col-6 mb-3">
                        <label for="pompaDosTankLv">Level Tangki</label>
                        <input type="number" class="form-control" id="pompaDosTankLv" required>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-6 mb-3">
                        <label for="barScreen">Bar Screen</label>
                        <select class="custom-select" id="barScreen" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>

                    <div class="col-6 mb-3">
                        <label for="airDrayer">Air Dryer</label>
                        <select class="custom-select" id="airDrayer" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="fineScreenA">Fine Screen A</label>
                        <select class="custom-select" id="fineScreenA" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="fineScreenB">Fine Screen B</label>
                        <select class="custom-select" id="fineScreenB" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="compressorA">Compressor A</label>
                        <select class="custom-select" id="compressorA" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="compressorB">Compressor B</label>
                        <select class="custom-select" id="compressorB" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>
                </div>

                <hr>

                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2">WTP</div>
                    <div class="col-6 col-md-3 mb-3">
                        <label for="wtpFlocA">Flocullation A</label>
                        <select class="custom-select" id="wtpFlocA" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="wtpFlocB">Flocullation B</label>
                        <select class="custom-select" id="wtpFlocB" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="wtpClafA">Clarifier A</label>
                        <select class="custom-select" id="wtpClafA" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 mb-3">
                        <label for="wtpClafB">Clarifier B</label>
                        <select class="custom-select" id="wtpClafB" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label for="wtpFiltrasi">Filtrasi</label>
                        <select class="custom-select" id="wtpFiltrasi" required>
                            <option value="normal">Normal</option>
                            <option value="standby">Standby</option>
                            <option value="running">Running</option>
                        </select>
                    </div>
                </div>


                <div class="form-row">
                    <div class="col-12 font-weight-bold mb-2" style="font-size: 15px">Level Grafity Filtrasi</div>
                    <div class="col-md-3 co mb-3">
                        <label for="filtrasiA">A (m)</label>
                        <input type="number" class="form-control" id="filtrasiA" required>
                    </div>
                    <div class="col-md-3 co mb-3">
                        <label for="filtrasiB">B (m)</label>
                        <input type="number" class="form-control" id="filtrasiB" required>
                    </div>
                    <div class="col-md-3 co mb-3">
                        <label for="filtrasiC">C (m)</label>
                        <input type="number" class="form-control" id="filtrasiC" required>
                    </div>
                    <div class="col-md-3 co mb-3">
                        <label for="filtrasiD">D (m)</label>
                        <input type="number" class="form-control" id="filtrasiD" required>
                    </div>
                    <div class="col-md-3 co mb-3">
                        <label for="filtrasiE">E (m)</label>
                        <input type="number" class="form-control" id="filtrasiE" required>
                    </div>
                    <div class="col-md-3 co mb-3">
                        <label for="filtrasiF">F (m)</label>
                        <input type="number" class="form-control" id="filtrasiF" required>
                    </div>

                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-secondary" type="submit">Back</button>
                    <button class="btn btn-primary ml-2" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
