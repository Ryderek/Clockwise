<!-- Modal -->
<div class="modal fade" id="createHolidayOrSickLeaveModal" tabindex="-1" aria-labelledby="createHolidayOrSickLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('settlement.create-break') }}">
            @csrf
            <input type="hidden" name="workTimingUserId" value="{{ $employee->id }}" />
            <div class="modal-header">
                <h1 class="modal-title h4 fs-5" id="createHolidayOrSickLeaveModalLabel">Definiowanie urlopu</h1>
            </div>
            <div class="modal-body row">
                <div class="col-12">
                    <div class="input-group mb-3">
                        <label class="input-group-text" style="border-top-right-radius: 0; border-bottom-right-radius: 0; width: 15%;" for="inputGroupSelect01">Typ</label>
                        <select required id="holidayType" name="holidayType" style="border-top-right-radius: 5px; border-bottom-right-radius: 5px; border: 1px solid #aaa; width: 85%; background-color: #fff;" class="form-select">
                            <option selected disabled>Wybierz typ urlopu</option>
                            <option value="holiday">Urlop</option>
                            <option value="sickleave">Zwolnienie</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="input-group mb-3 pr-2">
                        <label class="input-group-text" style="border-top-right-radius: 0; border-bottom-right-radius: 0; width: 40%;" for="inputGroupSelect01">Procent płatnego</label>
                        <input type="number" id="paidPercent" name="paidPercent" step="1" min="1" max="100" class="form-control text-right"  placeholder="Podaj liczbę" style="width: 30%;" value="100" id="precentageOfPaid" aria-label="Podaj liczbę" aria-describedby="basic-addon2">
                        <div class="input-group-append" style="width: 7%;">
                            <span class="input-group-text" id="basic-addon2">%</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-6">
                    <label for="startDate">Od</label>
                    <input id="startDate" class="form-control" name="startDate" type="date" required />
                </div>

                <div class="col-12 col-md-6">
                    <label for="endDate">Do</label>
                    <input id="endDate" class="form-control" name="endDate" type="date" required />
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                <button type="submit" class="btn btn-primary">Zdefiniuj</button>
            </div>
        </form>
    </div>
</div>

<script>
    $("#holidayType").change(function() {
        currentType = $("#holidayType option:selected").val();  
        if(currentType == "holiday"){
            $("#paidPercent").val(100);
            $("#paidPercent").attr("disabled", "disabled");
            $("#paidPercent").attr("disabled", "enabled");
        }else{
            $("#paidPercent").removeAttr("disabled", "disabled");
        }      
    });
</script>

<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number] {
    -moz-appearance: textfield;
    }
</style>
