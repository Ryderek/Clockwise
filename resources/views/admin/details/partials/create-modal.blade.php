<div class="modal fade" id="createDetail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createDetailLabel" aria-hidden="true">
    <form class="modal-dialog" action="{{ route('detail.store') }}" method="POST" autocomplete="off">
        <input style="display: none;" type="text" name="fakeUsernameAutofill" />
        <input style="display: none;" value="{{ $order->orderId }}" type="hidden" name="orderDetailOrderId" required />
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fs-5" id="createDetailLabel">Dodawanie detalu</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input style="display: none" type="text" name="fakeUsernameAutofill" />
                    <div class="col-12 col-md-8">
                        <label for="orderDetailName" class="form-label">Nazwa detalu</label>
                        <input type="text" class="form-control" id="orderDetailName" name="orderDetailName" placeholder="Podaj nazwę detalu" maxlength="127" aria-describedby="detailNameHelp" required />
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="orderDetailItemsTotal" class="form-label">Ilość</label>
                        <input type="number" class="form-control" id="orderDetailItemsTotal" min="1" value="1" max="99999" name="orderDetailItemsTotal" required />
                    </div>
                    <div class="col-12 mb-3"></div>

                    <div class="col-12 col-md-8">
                        <label for="orderDetailPainting" class="form-label">Lakierowanie</label>
                        <input type="text" class="form-control" id="orderDetailPainting" name="orderDetailPainting" placeholder="Podaj kod lakieru" maxlength="127" aria-describedby="detailNameHelp" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="orderDetailCooperation" class="form-label">Kooperacja<br />
                            <input type="checkbox" id="orderDetailCooperation" class="mt-3" name="orderDetailCooperation" />&nbsp;Aktywna    
                        </label>
                        
                    </div>
                    
                    <div class="col-12 mt-3">
                        <label for="orderDetailItemsTotal" class="form-label">Opcje obróbki</label>
                    </div>
                    <div class="col-12" id="appendFactorChoicesHere">
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn" onclick="appendAnotherFactoryChoice();" style="border: 1px solid #bbb; color: #bbb; width: 100%; padding: .1rem .25rem; font-size: 1rem; line-height: 1.5; border-radius: .25rem;">Dodaj obróbkę</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                <button type="submit" class="btn btn-primary" value="Dodaj detal">Dodaj detal</button>
            </div>
        </div>
    </form>
    <div class="d-none" id="copyMetricaOfSelectFromHere">
        <select name="newWorkTiming[]" style="backgorund: #fff;" class="form-select px-3 py-2 mb-3 border identityCardFirstStep rounded w-100" aria-label="Wybierz typ obróbki">
            <option value="">Wybierz</option>
            @foreach($roles as $role)
                <option value="{{ $role->roleSlug }}">{{ $role->roleProcess }}</option>
            @endforeach
        </select>
    </div>
    <script>
        function appendAnotherFactoryChoice(){
            $("#appendFactorChoicesHere").append($("#copyMetricaOfSelectFromHere").html());
        }
    </script>
</div>