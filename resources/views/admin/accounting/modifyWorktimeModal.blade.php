<div class="modal fade" id="editWorktimeModal" tabindex="-1" aria-labelledby="editWorktimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('accounting.modifyWorktime') }}" class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fs-5">Modyfikuj godziny pracy</h4>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" name="workTimingId" id="workTimingId" />
                <div class="mb-3">
                    <label for="workTimingStart" class="col-form-label">Rozpoczęcie pracy:</label>
                    <input type="datetime-local" id="workTimingStart" name="workTimingStart" step="1">
                </div>
                <div class="mb-3">
                    <label for="workTimingEnd" class="col-form-label">Zakończenie pracy:</label>
                    <input type="datetime-local" id="workTimingEnd" name="workTimingEnd" step="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                <input type="submit" class="btn btn-primary" value="Zapisz" />
            </div>
        </form>
    </div>
</div>

<script>

function date_format( d, p ) {
    var pad = function (n, l) {
        for (n = String(n), l -= n.length; --l >= 0; n = '0'+n);
        return n;
    };
    var tz = function (n, s) {
        return ((n<0)?'+':'-')+pad(Math.abs(n/60),2)+s+pad(Math.abs(n%60),2);
    };
    return p.replace(/([DdFHhKkMmSsyZ])\1*|'[^']*'|"[^"]*"/g, function (m) {
        l = m.length;
        switch (m.charAt(0)) {
                case 'D': return pad(d.getDayOfYear(), l);
                case 'd': return pad(d.getDate(), l);
                case 'F': return pad(d.getDayOfWeek(i18n), l);
                case 'H': return pad(d.getHours(), l);
                case 'h': return pad(d.getHours() % 12 || 12, l);
                case 'K': return pad(d.getHours() % 12, l);
                case 'k': return pad(d.getHours() || 24, l);
                case 'M': return pad(d.getMonth() + 1, l );
                case 'm': return pad(d.getMinutes(), l);
                case 'S': return pad(d.getMilliseconds(), l);
                case 's': return pad(d.getSeconds(), l);
                case 'y': return (l == 2) ? String(d.getFullYear()).substr(2) : pad(d.getFullYear(), l);
                case 'Z': return tz(d.getTimezoneOffset(), ' ');
                case "'":
                case '"': return m.substr(1, l - 2);
                default: throw new Error('Illegal pattern');
        }
    });
};

function getFirstDayOfMonth(year, month) {
    console.log(month);
    return date_format(new Date(year, month, 1, 0, 0), "yyyy-MM-ddTHH:mm:ss");
}
function getLastDayOfMonth(year, month) {
  return date_format(new Date(year, month + 1, 0, 23, 59), "yyyy-MM-ddTHH:mm:ss");
}


const editWorktimeModal = document.getElementById('editWorktimeModal')
editWorktimeModal.addEventListener('show.bs.modal', event => {

    const button = event.relatedTarget

    dateArray = button.getAttribute('data-bs-worktimingstart').split("-");
    minDay = getFirstDayOfMonth(dateArray[0], (dateArray[1] - 1));
    maxDay = getLastDayOfMonth(dateArray[0], (dateArray[1] - 1));

    editWorktimeModal.querySelector('#workTimingId').value =  button.getAttribute('data-bs-worktimingid');
    editWorktimeModal.querySelector('#workTimingStart').value =  button.getAttribute('data-bs-worktimingstart');
    editWorktimeModal.querySelector('#workTimingEnd').value =  button.getAttribute('data-bs-worktimingstop');

    $(editWorktimeModal.querySelector('#workTimingStart')).attr("min", minDay);
    $(editWorktimeModal.querySelector('#workTimingStart')).attr("max", maxDay);

    console.log(minDay);
    console.log(maxDay);
})


</script>