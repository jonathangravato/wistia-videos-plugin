jQuery(document).ready(function( $ ) {

    $( "body.page-id-2339" ).addClass( 'video-library' );

    /**
     * Auto submit video search forms. 
     */
    
    var searchFields = ['#video-authors', '#video-topics', '#video-series'];

    $.each( searchFields, function( index, value ) {
        $(value).on('change', function() {
            this.form.submit();
         });
    });

    /**
     * Lazy Load All Videos Results
     */

    var ajaxLock = false;  //ajaxLock is just a flag to prevent double clicks and spamming

    if( ! ajaxLock ) {

        function ajax_next_posts() {

            ajaxLock = true;

            var totalPosts   = parseInt( $( '#total-posts-count' ).text() );   //How many posts there's total
            var postOffset   = $( '#all-videos .col-6.col-md-4.mb-1' ).length; //How many have been loaded
            var postsPerPage = 12;                                             //How many do you want to load in single patch 

            
            //Hide button if all posts are loaded
            if( totalPosts < postOffset + ( 1 * postsPerPage ) ) {
                $( '#more-posts-button' ).fadeOut();
            }

            var ajaxURL  = 'https://www.trialschool.org/wp-admin/admin-ajax.php';        //Change that to your right site url unless you've already set global ajaxURL
            var ajaxData = '&post_offset=' + postOffset + '&action=ajax_next_posts'; //Parameters you want to pass to query

            //Ajax call itself
            $.ajax({

                type: 'get',
                url:  ajaxURL,
                data: ajaxData,
                dataType: 'json',

                //Ajax call is successful
                success: function ( response ) {
                    console.log( response );
                    //Add new posts
                    $( '#all-videos' ).append( response[0] );
                    //Update the count of total posts
                    $( '#total-posts-count' ).text( response[1] );

                    ajaxLock = false;
                },

                //Ajax call is not successful, still remove lock in order to try again
                error: function () {
                    ajaxLock = false;
                }
            });
        }
    }

    $( '#more-posts-button' ).click( function( e ) {
        e.preventDefault();
        ajax_next_posts();
    });

});