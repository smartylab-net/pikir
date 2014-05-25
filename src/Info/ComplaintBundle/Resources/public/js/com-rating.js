var Rating = {
    config : {
        image: '',
        loadImg: '',
        idRating: '',
        idHidden: ''
    },

    init : function(config){
        $.extend(Rating.config, config);
        Rating.loadRating();
    },

    loadRating : function(){
        $('#'+Rating.config.idRating).rating({
            fx: 'full',
            image: Rating.config.image,
            loader: Rating.config.loadImg,
            width: 32,
            stars:10,
            click: function (v){
                $('#'+Rating.config.idHidden).val(v);
            }
        });
    }
};