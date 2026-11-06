<div class="inline field file eight wide column">
    <input type="text" hidden name="is_deleted_additional" id="is_deleted_language_file_additional" class="hid"/>
    <input type="text" hidden name="dd_file_additional" id="dd_file_language_file_additional" class="hid"/>
    <label>Task image</label>
    <label id="dropped2" class="add-bg ">
        <input type="file" name="image-additional" accept="image/png"
               onchange="previewFile(this,'dd_file_language_file_additional','previewImgAdditional');$('#delete_flag_of_language').remove()"
               placeholder="IMG">
        <i class="upload icon">
            @if($game <> null)
                @if(json_decode($game->theory) <> null)
                    @if(json_decode($game->theory)[0] <> null)
                        <img class="previewImgAdditional" data-value=""
                             src="@if(isset(json_decode($game->theory)[0])){{json_decode($game->theory)[0]}}_S @endif">
                    @else
                        <img class="previewImgAdditional" data-value=""
                             src="">
                    @endif
                @else
                    <img class="previewImgAdditional" data-value=""
                         src="">
                @endif
            @else
                <img class="previewImgAdditional" data-value=""
                     src="">
            @endif
        </i>
        <a href="#" class="dis-img" onclick="delLanguageFlagAdditional()"><i class="close icon"></i></a>
    </label>
</div>

<script>
    function delLanguageFlagAdditional() {
        $('.previewImgAdditional').attr('src', '')
        $('#is_deleted_language_file_additional').val(1);
        $('#dd_file_language_file_additional').val(null)
    }
</script>
