$(document).ready(function() {

    $("#reviewformsend").live("click", function() {

        if($('#mod_description').attr('value').length < 10){
            alert('Ваш отзыв слишком короткий!');
            return false;
        } else {

            $("#mod_review_preloader").show();
            $.ajax({
            type : "POST",
            cache : false,
            url : "/modules/mod_reviews_form/ajax/addreview.php",
            data : $('#modreviewsform').serializeArray(),
            success: function(data) {
                $("#modreviews_form").html(data);
                $("#mod_review_preloader").hide();
            }
        });
        return false;

        }
    });
});