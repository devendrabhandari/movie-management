jQuery(document).ready(function($){
  $("#movie-rating").starRating({    
    initialRating: movie_rating_object.initial_rating,
    readOnly: movie_rating_object.read_only,
    onHover: function(currentIndex, currentRating, $el){
      $('.live-rating').text(currentIndex);
    },
    onLeave: function(currentIndex, currentRating, $el){
      $('.live-rating').text(currentRating);
    },
    callback: function(currentRating, $el){      
      var data = {
        'action': 'rate_movie',
        'id': movie_rating_object.post_id,
        'rating': currentRating
      };
      jQuery.post(movie_rating_object.ajaxurl, data, function(response) {});
    }
  });
});