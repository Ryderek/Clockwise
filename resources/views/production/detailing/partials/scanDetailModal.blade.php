<!-- Modal -->
<div class="modal fade" id="scanDetailModal" tabindex="-1" aria-labelledby="scanDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" action="{{ route('production.detailing.ean') }}">
            @csrf
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="scanDetailModalLabel">Zeskanuj kod detalu</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="text-align: center;">
                <span class="identityCardSecondStep form-label" style="display: inline;">Zeskanuj kod produktu czytnikiem, aby zatwierdzić.</span>
                
                <input type="button" id="unhideInputButton" onclick="replaceInputWithMe(this, 'detailUniqueCode')" class="identityCardSecondStep" />
                <input style="opacity: 0;" id="detailUniqueCode" type="text" value="" name="detailUniqueCode" autofocus="" required="" minlength="6" maxlength="127">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
            </div>
        </form>
    </div>
</div>
<script>
</script>

<style>
    #unhideInputButton{
        display: block; 
        margin-left: auto; 
        margin-right: auto; 
        width: 40px; 
        height: 40px;  
        margin-top: 20px;
        background-color: rgba(0,0,0,0); 
        background-image: url('/build/images/displayInputButton.png'); 
        background-size: 40px 40px; 
        background-repeat: no-repeat; 
        background-position: center center;  
        border: none;
    }
</style>
<script>
    function replaceInputWithMe(whi, inp){
        $(whi).fadeOut(350, function (){
            $("#"+inp).css("transition", "0.3");
            setTimeout(() => {
                $("#"+inp).css("opacity", "1");
                $("#"+inp).fadeIn(300, function(){
                    $("#"+inp).focus();
                });
            }, 50);
        });
    }
</script>