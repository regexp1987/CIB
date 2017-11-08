var fireEvent;
(function(jQuery){
	 'use strict';
    jQuery(document).ready(function(){		//when DOM is ready
        theme.init();
    });
    jQuery(window).resize(function() {
        clearTimeout(fireEvent);
        fireEvent = setTimeout(theme.resizeEvent, 200);
	});
})(jQuery);
jQuery(window).load(function() {
    theme.resizePostCategorySliderOverlays();
	theme.resizeGalleryOverlays();
	theme.initFeaturedOverlay();
	theme.initAffixSidebar();
});

var theme = {
	mosaicTimeout: false,
    init: function() {
		theme.initOverlayClicks();
		theme.initPostTabSwitcher();
		theme.initCategorySliderSwitching();
		theme.initSliderBlurOverlay();
		theme.initFeaturedOverlay();
		theme.initScrollTop();
		theme.resizePostCategorySliderOverlays();
        theme.resizePostSliderLargeOverlays();
		theme.resizeGalleryOverlays();
		theme.initGalleryLightbox();
		theme.initGallerySliderControls();
		theme.bindPostCategorySliderSlideResizeEvent();
		theme.bindFeaturedPostSliderOverlayEvents();
		theme.initSocialShareButtons();
		theme.initWeatherWidget();
		theme.initDropdownCategoryPostTabs();
		theme.initLoginLightbox();
		theme.initSearchLightbox();
		theme.initLikeButtons();
		theme.initReviewSummary();
		theme.initReaderReviews();
		theme.initTrendingNewsSliderHiding();
		
		theme.initNiceScroll();
		theme.initDockTrendingPosts();
		theme.initMoreCategoryPopup();
		theme.hideEditorsChoiceLabelOnHover();
		theme.initLargeTrending();
		theme.initFeaturedPostPhoto();
		theme.initExclusivePosts();
		
		theme.activeMenuItemTitle();
		theme.IeEdgeFilters();
		theme.initTouchMenu();
		theme.initParticles();

        theme.addMobileBodyClass();
		
	},
    resizeEvent: function() {
		theme.resizeSLiderBlurOverlay();
		theme.resizePostCategorySliderOverlays();
		theme.initFeaturedOverlay();
		theme.adjustGalleryLightboxHeight();
		theme.initNiceScroll();
		theme.initLargeTrending();
		theme.initFeaturedPostPhoto();
		theme.resizeRatings();
    },
	initOverlayClicks: function() {
		
		jQuery('*[data-click-url]').click(function(){
			window.location.href = jQuery(this).data('click-url');
			return false;
		});
		
	},
	initPostTabSwitcher: function() {
	
		jQuery('.switchable-tabs .sorting .buttons a').click(function(){
            var parent = jQuery(this).parents('.switchable-tabs');
            var index = parent.find('.sorting .buttons a').index(jQuery(this));
            
			parent.find('.sorting .buttons a').removeClass('active');
            jQuery(this).addClass('active');
			
            parent.find('.switcher-tab-content').fadeOut(250).promise().done(function(){
                parent.find('.switcher-tab-content').eq(index).fadeIn(250);
            });
            
            return false;
        });
		
	},
	initCategorySliderSwitching: function() {
        
        jQuery('.dynamic-category-slider .buttons .btn-sort').click(function(e){
            
            e.preventDefault();
            
			if(jQuery(this).hasClass('active')) return false;
			
			var parent = jQuery(this).parents('.dynamic-category-slider');
			var block = 'slider-' + jQuery(this).attr('id').substring(4);
			
			//set tab item active
			parent.find('.buttons .btn-sort').removeClass('active');
			jQuery(this).addClass('active');
			
			parent.find('.dynamic-slide').fadeOut(300).promise().done(function(){
				
				var item = parent.find('#' + block);
				
				if(item.find('.loader').length > 0)    //if there is no content load it with AJAX
				{
					item.show();
					
					var data = {
						action: 'load_post_category_slider_items',
						count: item.data('count'),
						category: item.data('category'),
						unique_id: item.data('unique_id'),
						interval: item.data('interval'),
					};
					
					jQuery.post(magellan_js_params.ajaxurl, data, function(response) {
						item.html(response).hide().fadeIn(300).promise().done(function(){ 
							theme.resizePostCategorySliderOverlays();
						});
					});
				}
				else
				{
					item.fadeIn(300).promise().done(function(){ 
						theme.resizePostCategorySliderOverlays();
					});
				}
				
			});
			
        });
    },
	initSliderBlurOverlay: function() {
		theme.resizeSLiderBlurOverlay();
		
		//show slider after the init
		jQuery('.magellan-slider ').hide().css('visibility','visible').fadeIn(200);
	},
	resizeSLiderBlurOverlay: function() {
		var slidewidth = jQuery('.magellan-slider .slide').width();
		var containerwidth = jQuery('.magellan-slider .slide .container').width();
		var overlay = jQuery('.magellan-slider .slide .overlay-wrapper').width();
		var sidemargin = (slidewidth - containerwidth) / -2;

		jQuery('.magellan-slider .slide .overlay').css('left', sidemargin).css('right', - (containerwidth - overlay) + sidemargin);
	},
	initFeaturedOverlay: function() {
		
		jQuery('.post-featured').not('.featured-post-content, .no-animation').each(function(){
			
			theme.setFeaturedOverlayPosition(jQuery(this));
			
		});
		
		jQuery('.post-featured:not(.no-animation) .overlay-wrapper').hover(
			function(){ 
				jQuery(this).addClass('hovered');
				theme.recalculateOverlayPosition(jQuery(this)); 
			},
			function() {
				jQuery(this).removeClass('hovered');
				theme.recalculateOverlayPosition(jQuery(this));
			}
		);

		jQuery('.post-featured.no-animation').each(function(){

			var overlayposition = jQuery(this).outerHeight() - jQuery(this).find('.overlay-wrapper').outerHeight();
			jQuery(this).find('.overlay').css({ top: -overlayposition});
		});
	},
    setFeaturedOverlayPosition: function(elem) {
        
        elem.css('height', 'auto');
			
        var postfeaturedheight = elem.parents('.post-block').outerHeight();

        var btnheight = 0;
        if(jQuery(window).outerWidth() > 992) {	//don't hide the read more on mobile
            var btnheight = elem.find('.title .btn').eq(0).outerHeight(true);
        }

        elem.css('height', postfeaturedheight);
        var overlayposition = elem.outerHeight() - elem.find('.overlay-wrapper').outerHeight();

        elem.find('.overlay-wrapper .overlay').css('top', -overlayposition - btnheight);
        elem.find('.overlay-wrapper').css('bottom', (btnheight*-1) ); //position read more below the content frame
        elem.find('.overlay-wrapper .overlay').css('bottom', btnheight);
    },
	recalculateOverlayPosition: function(elem) {
		
		var parent = elem.parents('.post-featured');
		var overlayposition = parent.outerHeight() - parent.find('.overlay-wrapper').outerHeight();
		
		if(elem.hasClass('hovered'))
		{
			parent.find('.overlay').animate({
				top: -overlayposition,
				bottom: 0
			}, {duration: 150, queue: false});
			
			parent.find('.overlay-wrapper').animate({
				bottom: 0
			}, {duration: 150, queue: false});
		}
		else
		{
			var btnheight = elem.find('.btn').eq(0).outerHeight(true);
			
			parent.find('.overlay').animate({
				top: -overlayposition -btnheight,
				bottom: btnheight
			}, {duration: 150, queue: false});
			
			parent.find('.overlay-wrapper').animate({
				bottom: -btnheight
			}, {duration: 150, queue: false});
		}
		
	},
	bindFeaturedPostSliderOverlayEvents: function() {
		jQuery('.dynamic-category-slider').on('slid.bs.carousel', function () {
			theme.initFeaturedOverlay();
		});
	},
	resizePostCategorySliderOverlays: function() {
		jQuery('.post-video').each(function(){
			var videothumbnail = jQuery(this).find('.image').outerHeight();
			jQuery(this).find('.overlay-wrapper').css('height', videothumbnail);
		});
	},
	resizeGalleryOverlays: function(){
		jQuery('.post-gallery').each(function(){
			var videothumbnail = jQuery(this).find('.image').outerHeight();
			jQuery(this).find('.overlay-wrapper').css('height', videothumbnail);
		});
	},
	bindPostCategorySliderSlideResizeEvent: function() {
		jQuery('.dynamic-category-slider').on('slid.bs.carousel', function () {
			theme.resizePostCategorySliderOverlays();
		});
	},
    resizePostSliderLargeOverlays: function(){
        jQuery('.editors-choice-slider .carousel').on('slid.bs.carousel', function (e) {
            
            jQuery(e.currentTarget).find('.active .post-featured').each(function(){
                theme.setFeaturedOverlayPosition(jQuery(this));
            });
            
		});
        
        jQuery('.magellan_post_carousel .article-carousel .carousel').on('slid.bs.carousel', function (e) {
            
            jQuery(e.currentTarget).find('.active .post-featured').each(function(){
                
                var overlayposition = jQuery(this).outerHeight() - jQuery(this).find('.overlay-wrapper').outerHeight();
                jQuery(this).find('.overlay').css({ top: -overlayposition});
                
            });
        });
    },
    initComments: function() {
        //form submit
        jQuery('#comment-submit').click(function(){
            jQuery('#hidden-submit').trigger('click');
            return false;
        });
    },
	initScrollTop: function() {
		var offset = 500;
		var duration = 300;
		
		jQuery(window).scroll(function() {
			if (jQuery(this).scrollTop() > offset) {
				jQuery('.back-to-top').fadeIn(duration);
			}
			else {
				jQuery('.back-to-top').fadeOut(duration);
			}
		});
		
		jQuery('.back-to-top').click(function(event) {
			event.preventDefault();
			jQuery('html, body').animate({scrollTop: 0}, duration);
			return false;
		});
	},
	initGallerySliderControls: function() {
		if(jQuery('body').hasClass('single-gallery'))
		{
			
			jQuery('.thumbs .thumb a').click(function(){

				var parent = jQuery(this).parents('.thumbs');
				
				var index = parent.find('a').index(jQuery(this));
				jQuery('.gallery-slideshow').cycle(index);
				
				jQuery('.thumbs .thumb').removeClass('active');
				jQuery('.thumbs').each(function(){
					jQuery(this).find('.thumb').eq(index).addClass('active');
				});
				
				var total_slides = jQuery('.single-photo-thumbs .controls').data('total');
				jQuery('.single-photo-thumbs .controls s').html((index+1) + ' / ' + total_slides);
				
				return false;
			});
			
			jQuery('.gallery-slideshow').on('cycle-update-view', function(event, optionHash, slideOptionsHash, currentSlideEl) {
				
				var index = optionHash.currSlide;
				
				jQuery('.thumbs .thumb').removeClass('active');
				jQuery('.thumbs').each(function(){
					jQuery(this).find('.thumb').eq(index).addClass('active');
				});
				
				var total_slides = jQuery('.single-photo-thumbs .controls').data('total');
				jQuery('.single-photo-thumbs .controls s').html((index+1) + ' / ' + total_slides);
				
			});
			
			jQuery('.gallery-slideshow').on('cycle-initialized', function(event, optionHash, slideOptionsHash, currentSlideEl) {
				
				jQuery(this).hide().css('opacity', 1).fadeIn(500);
				
			});
			
			jQuery('.gallery-slideshow').cycle();
		}
	},
	initSocialShareButtons: function() {
		
		if(jQuery('.share-button').length > 0)
		{
			var button = jQuery('.share-button').eq(0);
			
			config = {
				url: button.data().url,
				title: button.data().title,
				description: button.data().description,
				image: button.data().image,
				ui: {
					buttonText: button.data().buttonText,
					icon_font: false,
					flyout: 'bottom right'
				},
				networks: {
					whatsapp: {
						enabled: false
					},
					reddit: {
						enabled: false
					},
					email: {
						enabled: false
					}
				}
			};
						
			var share = new ShareButton(config);
			//    flyout:       // change the flyout direction of the shares. chose from `top left`, `top center`, `top right`, `bottom left`, `bottom right`, `bottom center`, `middle left`, or `middle right` [Default: `top center`]

		}
        
        
        //secondary share
        jQuery('.social.share-popup > a').click(function(){
            
            var href = jQuery(this).attr('href');
            window.open(href, '_blank', 'location=yes,height=570,width=520,scrollbars=yes,status=yes');
            
            return false;
        });
	},
	initWeatherWidget: function() {
	
		if(jQuery('.weather-ajax-container').length > 0)
		{
			var cached = theme.getWeatherWidgetCache();
			
			if(cached !== false && cached.length > 0)
			{
				jQuery('.weather-ajax-container').html(cached);
			}
			else
			{
				var data = {
					action: 'weather_widget',
				};

				jQuery.post(magellan_js_params.ajaxurl, data, function(response) {

					jQuery('.weather-ajax-container').html(response);
					theme.setWeatherWidgetCache(response);
				});
			}
		}
	},
	setWeatherWidgetCache: function(data) {
		if(typeof(Storage) !== "undefined") {
			
			sessionStorage.setItem('weatherWidget', data);
		}
	},
	getWeatherWidgetCache: function() {
		if(typeof(Storage) !== "undefined") {
			
			var cached = sessionStorage.getItem('weatherWidget');
			if(typeof cached !== "undefined" && cached !== null)
			{
				return cached;
			}
		}
		
		return false;
	},
	initDropdownCategoryPostTabs: function() {
		
		if(jQuery('.magellan_dropdown_category_posts').length > 0)
		{
		
			jQuery('.mega-menu-wrapper').on('click', '.magellan_dropdown_category_posts .sorting a', function(){
            
				var href = jQuery(this).attr('href');
				jQuery(this).siblings().removeClass('active');
				jQuery(this).addClass('active');
				
				var items = jQuery(href);
				if(typeof items != 'undefined')
				{
					jQuery(this).parents('.magellan_dropdown_category_posts').find('.items').hide();
					items.show();
					
					jQuery(this).parents('.magellan_dropdown_category_posts').find('.btn-more a').attr('href', items.data('url'));
					
				}

				return false;
				
			});
		
		}
		
	},
	initLoginLightbox: function() {
		
		jQuery('.login .show-lightbox').click(function(){
			jQuery('body').addClass('lightboxed-login');
		});
		
		jQuery('.lightbox .close').click(function(){
			jQuery('body').removeClass('lightboxed-login');
		});
	},
	initGalleryLightbox: function() {
		jQuery('.galleries .btn-maximize').click(function(){
            
            jQuery('.lightbox-gallery .container-fluid').height(jQuery(window).outerHeight());
            
			jQuery('.lightbox-gallery').removeClass('lightbox-hidden');
			jQuery('body').addClass('lightboxed-gallery');
			return false;
		});
		jQuery('.lightbox .close').click(function(){
			jQuery('body').removeClass('lightboxed-gallery');
			jQuery('.lightbox-gallery').addClass('lightbox-hidden');
			return false;
		});
		
		theme.adjustGalleryLightboxHeight();
	},
	adjustGalleryLightboxHeight: function() {
		jQuery('.lightbox-gallery .thumbs-wrapper').each(function(){
			
			jQuery(this).find('.thumbs-scroll').height('auto');
			
			var lightboxheight = jQuery(this).height();
			var maintitleheight = jQuery(this).find('.gallery-title').outerHeight(true);
			var defaulttitleheight = jQuery(this).find('.title-default').outerHeight(true);
			var widgetsheight = jQuery(this).find('.widget-sidebar').outerHeight(true);
			var thumbsscrollheight = jQuery(this).find('.thumbs-scroll').outerHeight(true);
			
			var maxheight = lightboxheight - maintitleheight - defaulttitleheight - widgetsheight - 40  - 30;
			if (thumbsscrollheight > maxheight) {
				jQuery(this).find('.thumbs-scroll').css('height', maxheight);
			}
		});
	},
	initSearchLightbox: function() {
		
		jQuery('.lightbox-search .container > .row').each(function(){
			var lightboxwidth = jQuery(this).outerWidth();
			var searchbuttonwidth = jQuery(this).find('.btn-search-lightbox').outerWidth();
			jQuery(this).find('.search-input-lightbox').css('width', lightboxwidth - searchbuttonwidth);
		});
		
		jQuery('.search-launcher').click(function(){
			jQuery('body').addClass('lightboxed-search');
			return false;
		});
		
		jQuery('.lightbox .close').click(function(){
			jQuery('body').removeClass('lightboxed-search');
			return false;
		});
	},
	initLikeButtons: function() {
		
		jQuery('.post-controls .like, .post-controls .dislike').click(function(){
			
			var type = 1;
			if(jQuery(this).hasClass('dislike'))
			{
				type = 2;
			}
			
			var parent = jQuery(this).parents('.post-controls');
			var postid = parent.attr('id').substring(7);
			
			var nonce = parent.data('nonce'); 
			
			var data = {
				action: 'post_like',
				id: postid,
				type: type,
				_ajax_nonce: nonce
			};
						
			jQuery.post(magellan_js_params.ajaxurl, data, function(response) {
				
				jQuery('.post-controls .likes').html(response.likes);
				jQuery('.post-controls .dislikes').html(response.dislikes);
				
				var percent = (response.likes/(response.likes + response.dislikes)) * 100;
				jQuery('.post-controls .bar s').width(percent + '%');
				
			}, 'json');
			
			return false;
		});
	},
	initReviewSummary: function() {
        jQuery('.review-summary').bind('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView === true) {
                
                var bar_w = jQuery(this).find('.row .bar').width();
				var review_item = jQuery(this);
                
                jQuery(this).find('.row .bar s').each(function(){
                    var percent = jQuery(this).attr('data-value');
                    var add_width = ((percent*bar_w)/100)+'px';
                    jQuery(this).animate({
                        'width': '+=' + add_width 
                    }, 1000, 'easeInQuart').promise().done(function(){
						theme.displayPreviousRating(review_item.find('.reader-reviews'));
					});
                });
                jQuery(this).unbind('inview');
            }
        });  
    },
	initReaderReviews: function() {
		if(jQuery('.reader-reviews').length > 0)
		{
			jQuery('.reader-reviews').each(function(){
				
				var review_item = jQuery(this);
				var contain = jQuery(this).find('.bar');
				var bar_width = contain.width();
				var grip = contain.find('.grip');
				var saved = review_item.data('msg_saved');

				grip.draggable({
					containment: contain,
					axis: 'x',
					cursor: 'move',
					drag: function(event, ui) {
						
						theme.displayRatingStars(grip, bar_width);
					},
					stop: function(event, ui) {
						
						theme.snapRating(review_item, parseInt(grip.css('left')), bar_width);
						theme.displayRatingStars(grip, bar_width);	
						
						var result = theme.getStarRating(parseInt(grip.css('left')), bar_width, '');
						
						if(result)
						{
							var postid = review_item.data('post_id');
							var nonce = review_item.data('nonce'); 
							
							if(result.length === 1) { result += '0'; }
							
							var data = {
								action: 'post_reader_rate',
								id: postid,
								rating: result,
								_ajax_nonce: nonce
							};

							jQuery.post(magellan_js_params.ajaxurl, data, function(response) {
								
								if(response.status === 'ok')
								{
									var star_class = '';
									
									if(typeof String(response.rating)[1] !== 'undefined' && String(response.rating)[1] == '5')
									{
										star_class = 's-' + String(response.rating)[0] + '-' + String(response.rating)[1];
									}
									else
									{
										star_class = 's-' + String(response.rating)[0];
									}
									
									grip.find('.tooltip').prepend(saved);
									review_item.find('.bar s').width((response.rating * 2) + '%');
									review_item.find('.overview-title i').attr('class', 'stars rating ' + star_class);
								}
								
							}, 'json');
							
						}		
					}
				});
				
			});
		}
	},
	displayRatingStars: function(grip, bar_width) {
		var result = theme.getStarRating(parseInt(grip.css('left')), bar_width, '-');
		if(result)
		{
			grip.find('.tooltip').html('<i class="stars rating s-' + result + '"></i>');
		}		
	},
	displayPreviousRating: function(review_item) {
	
		var postid = review_item.data('post_id'); 
		var rating = String(getCookie('magellan_reader_rate_' + postid));
		
		if(rating == 'false') return false;
				
		var contain = review_item.find('.bar');
		var bar_width = contain.width();
		var current_progress = contain.find('s').width();
		var grip = contain.find('.grip');
		
		var your = review_item.data('msg_your');
		
		var star_class = '';

		if(typeof rating[1] !== 'undefined' && rating[1] == '5')
		{
			star_class = 's-' + rating[0] + '-' + rating[1];
		}
		else
		{
			star_class = 's-' + rating[0];
		}
		
		grip.find('.tooltip').html(your + '<i class="stars rating ' + star_class + '"></i>');

		var left = (bar_width * rating * 2) / 100;
		
		if(current_progress > left)
		{
			grip.css('right', (current_progress-left) + 'px');
		}
		else
		{
			grip.css('right', (current_progress-left)*-1 + 'px');
		}
				
	},
	getStarRating: function(pos, width, separator) {
	
		var stars = Math.round((pos / width) * 5 * 10 ) / 10;
		var parts = (stars + '').split('.');
		var result = false;

		if(typeof parts[0] !== 'undefined')
		{
			if(typeof parts[1] === 'undefined') { parts[1] = '0'; }
			
			if(parseInt(parts[1]) >= 0 && parseInt(parts[1]) < 3)
			{
				result = parts[0];
			}
			else if(parseInt(parts[1]) >= 3 && parseInt(parts[1]) < 8)
			{
				result = parts[0] + separator + '5';
			}
			else	//round up
			{
				result = parseInt(parts[0]) + 1;
			}
		}
		
		return String(result);
	},
	snapRating: function(review_item, pos, width) {
				
		var stars = Math.round((pos / width) * 5 * 10 ) / 10;
		var star_val = (Math.round(stars * 2) / 2).toFixed(1) * 10;
		var snap_pos = (width / 50) * star_val;
		review_item.find('.bar .grip ').css('left', snap_pos );
	},
	resizeRatings: function() {
		
		jQuery('.review-summary').each(function(){
			
			var bar_w = jQuery(this).find('.row .bar').width();

			jQuery(this).find('.row .bar s').each(function(){
				var percent = jQuery(this).attr('data-value');
				var add_width = ((percent*bar_w)/100)+'px';
				jQuery(this).width(add_width);
			});
		});
	},
	initTrendingNewsSliderHiding: function() {
		
		if(jQuery('body').hasClass('trending-slider-fixed') || jQuery('body').hasClass('trending-slider-docked'))
		{
			jQuery('.close-trending-slider').click(function(){
				
				if(jQuery('body').hasClass('trending-slider-fixed'))
				{
					jQuery('.trending-slider').hide();
				}
				else
				{
					jQuery('.trending-slider').slideUp(300);
				}
				
				jQuery('body').removeClass('trending-slider-fixed trending-slider-docked');
				
				//set cookie to hide this for 1 day
				createCookie('magellan_trending_slider_hidden', 'on', 1);
				
				return false;
			});
						
			//hide if previously disabled
			if(getCookie('magellan_trending_slider_hidden') !== false)
			{
				jQuery('.trending-slider').hide();
				jQuery('body').removeClass('trending-slider-fixed trending-slider-docked');
			}
			else
			{
				jQuery('.trending-slider').show();
			}
		}
	},
	initNiceScroll: function() {
		
		if(jQuery(window).outerWidth() > 767 && !isMobileDevice()) {	//disable on mobile
			
			jQuery('.slider > .row > div').niceScroll({
				cursorwidth: '10px',
				cursorcolor: '#252525',
				background: 'rgba(0, 0, 0, 0.2)',
				cursorborder: 'none',
				cursorborderradius: '0',
				autohidemode: false,
				preservenativescrolling: true,
				nativeparentscrolling: true,
				railoffset: {top: 0, left: 15}
			});
			
			jQuery('.post-block .list').niceScroll({
				cursorwidth: '10px',
				cursorcolor: '#252525',
				background: 'rgba(0, 0, 0, 0.2)',
				cursorborder: 'none',
				cursorborderradius: '0',
				autohidemode: false,
				preservenativescrolling: true,
				nativeparentscrolling: true,
				railoffset: {top: 0, left: -15}
			});
			
			jQuery('.widget-footer .scrollable').niceScroll({
				cursorwidth: '10px',
				cursorcolor: 'rgba(255, 255, 255, 0.8)',
				background: 'rgba(255, 255, 255, 0.2)',
				cursorborder: 'none',
				cursorborderradius: '0',
				autohidemode: false,
				nativeparentscrolling: false,
				railoffset: {top: 0, left: 0}
			});
			
			jQuery('.lightbox-gallery .thumbs').niceScroll({
				cursorwidth: '10px',
				cursorcolor: 'rgba(255, 255, 255, 0.8)',
				background: 'rgba(255, 255, 255, 0.2)',
				cursorborder: 'none',
				cursorborderradius: '0',
				autohidemode: true,
				railoffset: {top: 0, right: 0}
			});
		} 
        else if(jQuery(window).outerWidth() > 767 && isMobileDevice()) 
        {
            jQuery('.slider > .row > div, .post-block .list, .widget-footer .scrollable, .lightbox-gallery .thumbs').css('overflow-y', 'scroll');
            jQuery('.slider > .row > div, .post-block .list, .widget-footer .scrollable, .lightbox-gallery .thumbs').css('-webkit-overflow-scrolling', 'touch');
            jQuery('.post-block .list > .post-block').css('overflow', 'visible');
        }
	},
	initDockTrendingPosts: function() {
		jQuery('#trending-posts').carousel();
	},
	initMoreCategoryPopup: function() {
		
		jQuery('.show-more').on('click', function(e) {
			
			var postid = false;
			
			if(jQuery('.dynamic-more-dropdown').length > 0)
			{
				postid = jQuery('.dynamic-more-dropdown').eq(0).find('.more-dropdown').data('post_id');
				jQuery('.dynamic-more-dropdown').remove();
			}
			
			var original = jQuery(this).parent().find('.more-dropdown').eq(0);
			var offset = original.offset();
			var dropdown = original.clone();
			
			//simply close the dropdown if, it's clicked again
			if(postid)
			{
				if(postid === original.data('post_id'))
				{
					return false;
				}
			}

			dropdown.addClass('active');
			dropdown.wrap('<div class="tags"></div>');
			dropdown = dropdown.parent();

			dropdown.offset({left: offset.left, top: offset.top});			
			dropdown.addClass('dynamic-more-dropdown');
            dropdown.css('position', 'absolute');

			jQuery('body').after(dropdown);
			
			return false;
		});
		
		//remove all dropdowns on body click
		jQuery('.focus').click(function(){
			if(jQuery('.dynamic-more-dropdown').length > 0)
			{
				jQuery('.dynamic-more-dropdown').remove();
			}
		});
		
	},
	hideEditorsChoiceLabelOnHover: function() {
		jQuery('.editors-choice .post-featured .title').hover(
			function(){ jQuery(this).parents('.editors-choice .post-featured').children('.btn-editors-choice').addClass('hidden'); },
			function(){ jQuery(this).parents('.editors-choice .post-featured').children('.btn-editors-choice').removeClass('hidden'); }
		);
	},
	initLargeTrending: function() {
		jQuery('.trending-posts-main-content').each(function(){
			var postswidth = jQuery(this).outerWidth();
			var titlewidth = jQuery(this).find('.title-default').outerWidth();
			var controlswidth = jQuery(this).find('.controls').outerWidth();
			jQuery(this).find('.carousel-inner').css('width', postswidth - titlewidth - controlswidth -31);
		});
	},
	initFeaturedPostPhoto: function() {
				
		//set height of container
		jQuery('.featured-post-content').each(function(){
			var windowheight = jQuery(window).outerHeight();
			var headerheight = jQuery('.header').outerHeight();
			var dockheight = jQuery('.dock').outerHeight();
			var megamenuheight = jQuery('.mega-menu-wrapper').outerHeight();
			jQuery(this).css('height', windowheight - headerheight - dockheight - megamenuheight -37);
			
			theme.recalculateOverlayPosition(jQuery(this).find('.title'));
			
			jQuery('body.featured-post .post-page-title, body.featured-post .post-page-title .overlay-wrapper').hide().css('visibility','visible').fadeIn(200);
		});
		
		if(jQuery(window).outerWidth() >= 992)
		{
			//title affix
			jQuery('body.featured-post .post-page-title').affix({
				offset: {
					top: jQuery('.dock').outerHeight() + jQuery('.header').outerHeight() + 117,
					bottom: 1000
				}
			});

			//wrapper affix
			jQuery('body.featured-post .parallax-wrapper').affix({
				offset: {
					top: jQuery('.dock').outerHeight() + jQuery('.header').outerHeight() + 117
				}
			});

			//affix adjust margin top
			jQuery('body.featured-post .parallax-wrapper').on('affix.bs.affix',function(){
				var windowheight = jQuery(window).outerHeight();
				var headerheight = jQuery('.header').outerHeight();
				var dockheight = jQuery('.dock').outerHeight();
				var megamenuheight = jQuery('.mega-menu-wrapper').outerHeight();
				jQuery(this).css('margin-top', windowheight - headerheight - dockheight - megamenuheight -77);
			});

			//adjust opacity on scroll
			jQuery(window).scroll(function (){

				if(jQuery('.post-page-title').hasClass('affix'))
				{
					var headerheight = jQuery('.header').outerHeight(true);
					var dockheight = jQuery('.dock').outerHeight(true);
					var megamenuheight = (jQuery('.mega-menu-wrapper').outerHeight() / 2);
					var currentScrollTop = jQuery(window).scrollTop() - headerheight - dockheight - megamenuheight;
					var contentHeight = jQuery('body.featured-post .affix .featured-post-content').height();
					var opacity = 1 - currentScrollTop/contentHeight;
					jQuery('body.featured-post .affix .featured-post-content').css('opacity',opacity);
				}
				else
				{
					jQuery('body.featured-post .affix .featured-post-content').css('opacity', 1);
				}
			});
		}
		else
		{
			
		}
		
		//remove opacity 
		jQuery('body.featured-post .parallax-wrapper').on('affixed-top.bs.affix',function(){
		
			jQuery('body.featured-post .featured-post-content').css('opacity', 1);
		});
	},
	initExclusivePosts: function() {
		jQuery('.post-exclusive').each(function(){
			var height = jQuery(this).outerHeight(true);
			jQuery(this).find('.image, .text > .title').css('height', height);
		});
	},
	activeMenuItemTitle: function() {
		
		if(jQuery('.mega-menu').length > 0)
		{
		
			var active = jQuery('.mega-menu .menu-item.active'); 
			if(active.length > 0)
			{
				jQuery('.mega-menu .togglemenu').text(active.find('a > span').eq(0).text());
			}
			
		}
	},
	IeEdgeFilters: function() {
		if(/Edge/.test(navigator.userAgent))
		{
			jQuery('html').addClass('no-cssfilters');
		}
	},
	initAffixSidebar: function() {
        if(magellan_js_params.enable_sidebar_affix === 'on' && !isMobileDevice())
        {
			//regular sidebar
			if(jQuery('.sidebar').length > 0 && jQuery(window).width() > 970-15)
            {
                var main_content = jQuery('.main-content');
                var sidebar = jQuery('.sidebar');
				
				var menu_offset = 45;	//affixed sidebar height

                if(main_content.outerHeight() > sidebar.outerHeight())
                {
					var offset_top;
					if(jQuery('body').hasClass('featured-post'))
					{
						var admin_bar_height = jQuery('#wpadminbar').outerHeight(true);
						var dock_height = jQuery('.dock').outerHeight(true);
						var header_height = jQuery('.header').outerHeight(true);
						var menu_half_height = 37;
						var overlay_height = jQuery('.featured-post-content').height();
						var content_padding = 40;

						offset_top = admin_bar_height + dock_height + header_height + menu_half_height + overlay_height + content_padding - menu_offset;
					}
					else
					{
						offset_top = sidebar.offset().top - menu_offset;
					}

                    jQuery('.sidebar').wrapInner('<div class="sidebar-affix-wrap affix-top"></div>');
					jQuery('.sidebar-affix-wrap').width(jQuery('.sidebar').width());

                    jQuery('.sidebar-affix-wrap').affix({
                        offset: {
                            top: offset_top,
                            bottom: function () {
                                return (this.bottom = jQuery('.footer').outerHeight(true) + jQuery('#trending-slider').outerHeight(true) + 30)
                            }
                        }
                    });
                }
            }
			
			//home affix
			if(jQuery('.sidebar-affix').length > 0 && jQuery(window).width() > 970-15)
            {
                jQuery('.sidebar-affix').each(function(){
					
					var sidebar = jQuery(this);
					var main_content = sidebar.siblings('.wpb_column').eq(0);

					var menu_offset = 45;	//affixed sidebar height
					if(main_content.outerHeight() > sidebar.outerHeight())
					{
						sidebar.wrapInner('<div class="sidebar-affix-wrap affix-top"></div>');
						sidebar.find('.sidebar-affix-wrap').width(sidebar.width());

						jQuery('.sidebar-affix-wrap').affix({
							offset: {
								top: sidebar.parent().offset().top - menu_offset,
								bottom:  jQuery(document).height() - (sidebar.parent().offset().top + sidebar.parent().height())
							}
						});
					}
					
				});
				
				
			}
        }
    },
	initTouchMenu: function() {
        if('ontouchstart' in document)
        {
            
            //no dropdown
			jQuery('.mega-menu .nav > .menu-item:not(.dropdown) > a').on('touchstart', function(){
				if(jQuery(window).outerWidth() > 992)
				{
					return true;
				}
			});
            
            //first level of dropdowns
            jQuery('.mega-menu .nav > .menu-item.dropdown > .dropdown-toggle').on('touchstart', function(){
				
				if(jQuery(window).outerWidth() > 992)
				{        
					var current_item = jQuery(this);
					//if there are other open menus, hide them
					if(jQuery('.nav .menu-item.dropdown.hover .dropdown-toggle').not(this).length > 0)
					{
						jQuery('.nav .menu-item.dropdown.hover').each(function(){
							if(current_item.parent() !== jQuery(this))
							{
								jQuery(this).removeClass('hover');
							}
						});
					}

					var parent = jQuery(this).parent();
					if(!parent.hasClass('hover'))
					{
						jQuery(this).parent().addClass('hover');
						return false;
					}
				}
            });
            
            //second level of dropdowns
            jQuery('.mega-menu .nav > .menu-item.dropdown .dropdown > a').on('touchstart', function(){
				if(jQuery(window).outerWidth() > 992)
				{ 
					var current_item = jQuery(this).parent();
					
					//if there are other open menus, hide them
					if(jQuery('.nav .menu-item.dropdown .dropdown').not(current_item).length > 0)
					{
						jQuery('.nav .menu-item.dropdown .dropdown.hover').each(function(){
							if(current_item.parent() !== jQuery(this))
							{
								jQuery(this).removeClass('hover');
							}
						});
					}

					if(!current_item.hasClass('hover'))
					{
						current_item.addClass('hover');
						return false;
					}
				}
            });
            
            
            //close menu
			jQuery('.main-content-wrapper, .header, .footer').on('touchstart', function(){
				if(jQuery(window).outerWidth() > 992)
				{ 
					var open = jQuery('.mega-menu .nav .hover');
					if(open.length > 0)
					{
						open.removeClass('hover');
					}
				}
			});			
            
        }
    },
    addMobileBodyClass: function() {
        
        if(isMobileDevice())
        {
            jQuery('body').addClass('mobile-device');
        }
    },
	initParticles: function() {
		//only for large screens
		if(jQuery('#particles').length > 0 && jQuery(window).width() > 970)
		{
			jQuery('#particles').particleground({
				dotColor: magellan_js_params.particle_color,
				lineColor: magellan_js_params.particle_color,
				parallax: false,
				particleRadius: 6,
				minSpeedX: 1,
				minSpeedY: 1,
				maxSpeedX: 2,
				maxSpeedY: 2
			});

			jQuery(window).scroll(function() {
				if(jQuery(window).width() > 970)
				{
					jQuery('#particles').particleground('start').delay(10).particleground('pause');
				}
			});
		}
	}
};

//split array in chunks
function chunk (arr, len) {

  var chunks = [],
      i = 0,
      n = arr.length;

  while (i < n) {
    chunks.push(arr.slice(i, i += len));
  }

  return chunks;
}

function calcParallax(tileheight, speedratio, scrollposition) {
  //    by Brett Taylor http://inner.geek.nz/
  //    originally published at http://inner.geek.nz/javascript/parallax/
  //    usable under terms of CC-BY 3.0 licence
  //    http://creativecommons.org/licenses/by/3.0/
  return ((tileheight) - (Math.floor(scrollposition / speedratio) % (tileheight+1)));
}

function getCookie(name){
    var pattern = RegExp(name + '=.[^;]*')
    matched = document.cookie.match(pattern)
    if(matched){
        var cookie = matched[0].split('=')
        return cookie[1]
    }
    return false
}

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = '; expires='+date.toGMTString();
    }
    else var expires = '';
    document.cookie = name+'='+value+expires+'; path=/';
}

function isMobileDevice() {
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        return true;
    }
    return false;
}
