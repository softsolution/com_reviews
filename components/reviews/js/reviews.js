function sendReview(){
    if($('#review_description').attr('value').length < 10){
        alert('Ваш отзыв слишком короткий!');
    } else {
        document.reviews_addform.submit();
    }
}