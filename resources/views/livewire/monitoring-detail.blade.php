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
                                <div class="col-5 col-md-4">Warna</div>
                                <div class="col-7 col-md-8">: {{ $waterQuality->color }} NTU</div>
                            </div>
                            <div class="row">
                                <div class="col-5 col-md-4">TDS</div>
                                <div class="col-7 col-md-8">: {{ $waterQuality->tds }} PCU</div>
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
                                    @if ($pumpProccess->status == 'normal')
                                        <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                    @elseif ($pumpProccess->status == 'standby')
                                        <h6> <span class="badge badge-info ml-1">Stand by</span></h6>
                                    @else
                                        <h6> <span class="badge badge-warning ml-1">Running</span></h6>
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
                                    <h6> <span class="badge badge-info ml-1">Stand by</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Running</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Air Drayer</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->air_drayer == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->air_drayer == 'standby')
                                    <h6> <span class="badge badge-info ml-1">Stand by</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Running</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Finescreen A</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->finescreen_a == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->finescreen_a == 'standby')
                                    <h6> <span class="badge badge-info ml-1">Stand by</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Running</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Finescreen B</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->finescreen_b == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->finescreen_b == 'standby')
                                    <h6> <span class="badge badge-info ml-1">Stand by</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Running</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Compressor A</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->compressor_a == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->compressor_a == 'standby')
                                    <h6> <span class="badge badge-info ml-1">Stand by</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Running</span></h6>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">Compressor B</div>
                            <div class="col-7 col-md-8 d-flex">:
                                @if ($shifts->unitOperation->compressor_b == 'normal')
                                    <h6> <span class="badge badge-success ml-1">Normal</span></h6>
                                @elseif ($shifts->unitOperation->compressor_b == 'standby')
                                    <h6> <span class="badge badge-info ml-1">Stand by</span></h6>
                                @else
                                    <h6> <span class="badge badge-warning ml-1">Running</span></h6>
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
                            <div class="col-7 col-md-8">:
                                {{ $shifts->wtps->gravity_filter_a }} m
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">B</div>
                            <div class="col-7 col-md-8">:
                                {{ $shifts->wtps->gravity_filter_b }} m
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">C</div>
                            <div class="col-7 col-md-8">:
                                {{ $shifts->wtps->gravity_filter_c }} m
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">D</div>
                            <div class="col-7 col-md-8">:
                                {{ $shifts->wtps->gravity_filter_d }} m
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">E</div>
                            <div class="col-7 col-md-8">:
                                {{ $shifts->wtps->gravity_filter_e }} m
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5 col-md-4">F</div>
                            <div class="col-7 col-md-8">:
                                {{ $shifts->wtps->gravity_filter_f }} m
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
        /**
         * Function to copy all monitoring data values from HTML to clipboard
         * This function extracts all measurement values from the monitoring report
         * and copies them as text in a structured format with section headers
         */


        $js('copyClipboard', () => {
            copyAllMonitoringValues();
        })

        function copyAllMonitoringValues() {
            try {
                // Final data structure to hold all grouped data
                let formattedOutput = [];

                // Function to extract data from a section
                function extractSectionData(sectionTitle, sectionElement) {
                    const dataRows = sectionElement.querySelectorAll('.row');
                    if (dataRows.length === 0) return null;

                    let sectionData = [`**---${sectionTitle}---**`];

                    // Extract normal label-value pairs
                    dataRows.forEach(row => {
                        const label = row.querySelector('.col-5.col-md-4')?.textContent?.trim();

                        // Get regular value or status badge
                        const valueCol = row.querySelector('.col-7.col-md-8');
                        let value;

                        if (valueCol?.classList.contains('d-flex')) {
                            // This is a status field with badge
                            const badge = valueCol.querySelector('.badge');
                            value = badge?.textContent?.trim();
                        } else {
                            // Regular value field
                            value = valueCol?.textContent?.trim().replace(/^:\s*/, ''); // Remove leading colon
                        }

                        if (label && value) {
                            sectionData.push(`${label}: ${value}`);
                        }
                    });

                    return sectionData.length > 1 ? sectionData : null; // Only return if we have actual data
                }

                // Extract shift information first
                const shiftSection = document.querySelector('.card-body');
                if (shiftSection) {
                    const shiftTitle = shiftSection.querySelector('.col-12.font-weight-bold.mb-2.text-uppercase')
                        ?.textContent?.trim() || 'Shift Information';
                    const shiftData = extractSectionData(shiftTitle, shiftSection.querySelector('.col-md-8.mb-3'));
                    if (shiftData) formattedOutput = formattedOutput.concat(shiftData, ['']);
                }

                // Process all major sections (those with headers)
                const sections = document.querySelectorAll('.font-weight-bold.mb-2.text-capitalize');
                sections.forEach(section => {
                    const sectionTitle = section.textContent?.trim();
                    if (!sectionTitle) return;

                    // Find the parent container that holds this section's data
                    let sectionContainer = section.closest('.col-md-6, .col-md-12');
                    if (!sectionContainer) return;

                    // For sections that contain multiple subsections (like pumps)
                    if (sectionTitle === 'Pompa Intake' || sectionTitle === 'Pompa Distribusi') {
                        const pumps = sectionContainer.parentElement.querySelectorAll(
                            '.col-md-6 .font-weight-bold.mb-2 .text-capitalize');

                        // Add the main section title
                        formattedOutput.push(`**---${sectionTitle}---**`);

                        // Process each pump separately
                        pumps.forEach(pump => {
                            formattedOutput.push(`${pump.textContent?.trim().toUpperCase()}`);
                            const pumpName = pump.textContent?.trim();
                            const pumpContainer = pump.closest('.col-md-6');
                            if (pumpName && pumpContainer) {
                                const pumpData = extractSectionData(pumpName, pumpContainer);
                                if (pumpData) {
                                    // Remove the header since we're using it as a subheader
                                    pumpData.shift();
                                    // Add the pump data with indentation
                                    pumpData.forEach(line => formattedOutput.push(`  ${line}`));
                                }
                            }
                        });
                        formattedOutput.push(''); // Add blank line after section
                    } else {
                        // Regular section without subsections
                        const sectionData = extractSectionData(sectionTitle, sectionContainer);
                        if (sectionData) formattedOutput = formattedOutput.concat(sectionData, ['']);
                    }
                });

                // Format the data for clipboard
                const textToCopy = formattedOutput.join('\n');

                // Copy to clipboard
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(textToCopy)
                        .then(() => {
                            console.log('Values copied to clipboard successfully');
                        })
                        .catch(err => {
                            console.error('Failed to copy values: ', err);
                        });
                } else {
                    // Fallback for browsers that don't support Clipboard API
                    const textarea = document.createElement('textarea');
                    textarea.value = textToCopy;
                    textarea.style.position = 'fixed';
                    document.body.appendChild(textarea);
                    textarea.focus();
                    textarea.select();

                    const successful = document.execCommand('copy');
                    document.body.removeChild(textarea);

                    if (successful) {
                        console.log('Values copied to clipboard successfully (fallback method)');
                    } else {
                        console.error('Failed to copy values (fallback method)');
                    }
                }
            } catch (error) {
                console.error('Error copying values to clipboard:', error);
            }
        }
    </script>
@endscript
