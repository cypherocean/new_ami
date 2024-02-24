<div class="modal fade" id="details" tabindex="-1" role="dialog" aria-labelledby="exampledetails" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampledetails' . $data->id . '">Followup Details - {!! $data->name !!}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="detail_form" id=' . $data->id . '>
                <div class="modal-body">{!! $details !!}</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>