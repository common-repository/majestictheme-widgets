;( function( $, window ) {
	'use strict';

	$( document ).ready( function() {
		var $body = $( 'body' );

		$body.off( 'click.mtwOnClickImageField', '.mtw-image-field-ctrl' );
		$body.on( 'click.mtwOnClickImageField', '.mtw-image-field-ctrl', function() {
			initImageUploader( $( this ) );
		} );

		$body.on( 'click.mtwOnClickGroupAdd', '.mtw-group-field-add', function() {
			var $groupWrapper     = $( this ).parents( '.mtw-group-field-wrapper' ),
				$groupList        = $groupWrapper.find( '.mtw-field-group-list' ),
				template          = $groupWrapper.find( '#mtw-field-group-template' ).clone( false, false ),
				formattedTemplate = template
									.removeAttr( 'id' )
									.html()
									.replace( /%id%/g, $groupList.children().length );

			$groupList
				.append( $( '<div class="mtw-field-group"/>' ).html( formattedTemplate ) );

			initGroupFields()
				.last()
				.find( '.mtw-field-group-header' )
				.click();

			initColorField();
			enableWidgetSaveButton( $( this ) );
		} );

		$body.on( 'click.mtwGroupClose', '.mtw-field-group-action-close', function() {
			$( this ).parents( '.mtw-field-group' ).find( '.mtw-field-group-header' ).click();
		} );

		$body.on( 'click.mtwGroupClose', '.mtw-field-group-action-delete', function() {
			$( this ).parents( '.mtw-field-group' ).remove();
		});

		initGroupFields();
		initColorField();
	} );

	function initImageUploader( $controller ) {
		var fileFrame,
			$fieldWrapper = $controller.parents( '.mtw-image-field' ),
			$fieldInput   = $fieldWrapper.find( '.mtw-image-field-id' ),
			$fieldPreview = $fieldWrapper.find( '.mtw-image-field-preview' ),
			$fieldBtn     = $fieldWrapper.find( '.mtw-image-field-btn' );

		if ( undefined !== fileFrame ) {
			fileFrame.open();
			return;
		}

		fileFrame = wp.media.frames.fileFrame = wp.media( {
			title: mtw.imgAddText,
			library: { type: 'image' },
			button: { text: mtw.imgAddText },
			multiple: false
		} );

		fileFrame.on( 'select', function () {
			var imgData, thumbnail;
			imgData = fileFrame.state().get( 'selection' ).first().toJSON();
			if ( imgData.id ) {
				$fieldInput.val( imgData.id ).trigger( window.getFakeEnterEvent() ).trigger( 'change' );
				thumbnail = ( typeof imgData.sizes.thumbnail !== 'undefined' ) ? imgData.sizes.thumbnail.url : imgData.url;
				$fieldPreview.html( '<img src="' + thumbnail + '"/>' );
				$fieldBtn.text( mtw.imgReplaceText );
			}
		} );

		fileFrame.open();
	}

	function enableWidgetSaveButton( $relTarget ) {
		var saveBtn = $relTarget
			.parents( '.widget-inside' )
			.find( '.widget-control-save' );

		if ( saveBtn.is( ':disabled' ) ) {
			saveBtn.removeAttr( 'disabled' )
		}
	}

	window.getFakeEnterEvent = function() {
		var event = jQuery.Event( 'keydown' );
		event.which = 13;
		return event;
	}

	window.initColorField = function() {
		jQuery( '#widgets-right' )
			.find( '.mtw-field-color' )
			.filter( function( i, item ) {
				return ! jQuery( item ).parents( '#mtw-field-group-template' ).length
			} )
			.wpColorPicker( {
				change: function( event ) {
					var $this = $( event.target );
					enableWidgetSaveButton( $this );
					$this.trigger( window.getFakeEnterEvent() );
				}
			} );
	}

	window.initGroupFields = function() {
		return jQuery( '#widgets-right' )
			.find( '.mtw-field-group-list' )
			.sortable( {
				axis: 'y',
				handle: '.mtw-field-group-header',
				stop: function( event, ui ) {
					jQuery( this.children ).each( function( index, groupItem ) {
						jQuery( groupItem )
							.find( '.mtw-field-index' )
							.val( index )
							.trigger( window.getFakeEnterEvent() );
					} );
					enableWidgetSaveButton( jQuery( this ) );
				}
			} )
			.find( '.mtw-field-group' )
			.accordion( {
				collapsible: true,
				active: false,
				header: '> .mtw-field-group-header',
				heightStyle: 'content',
				icons: {
					header: 'dashicons dashicons-arrow-down',
					activeHeader: 'dashicons dashicons-arrow-up'
				}
			} );
	}
} )( jQuery, window );
