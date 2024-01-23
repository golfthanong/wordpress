/**
 * Scroll Effects - Functionality describing the logic of scrolling effect.
 */
! function( $, undefined ) {
	"use strict";

	// Private variables that are used only in the context of this function, it is necessary to optimize the code.
	var _window = window,
		_document = document,
		_body = _document.body;

	// Math API
	var floor = Math.floor,
		max = Math.max,
		min = Math.min;

	// Check for is set availability objects
	_window.$us = _window.$us || {};
	_window.$ush = _window.$ush || {};

	/**
	 * @var {{}} Effect directions.
	 */
	const _DIRECTION_ = {
		DOWN: 'down',
	};

	/**
	 * @var {Number} N(px) - The value provides the best balance: performance VS precision.
	 */
	const _TRANSLATE_FACTOR_ = 7;

	/**
	 * Get the current offset of the scrolls.
	 *
	 * @return {{}} Returns an object of current scroll values.
	 */
	function scroll() {
		return {
			top: _window.scrollY || _window.pageYOffset,
			left: _window.scrollX || _window.pageXOffset
		};
	}

	/**
	 * Determines if disable effects.
	 *
	 * @return {Boolean} True if disable effects, False otherwise.
	 */
	function isDisableEffects() {
		return $us.canvasOptions.disableEffectsWidth >= _body.clientWidth;
	}

	/**
	 * @var {{}} Private temp data.
	 */
	var _$temp = {
		bodyHeight: $ush.parseInt( _body.clientHeight ),
		disableEffects: isDisableEffects(),
	};

	/**
	 * @class ScrollEffects - Scroll effects manager functionality.
	 */
	function ScrollEffects() {
		var self = this;

		// Variables
		self.elms = [];

		/**
		 * @var {{}} Bondable events
		 */
		self._events = {
			scroll: self._scroll.bind( self ),
			resize: $ush.debounce( self._resize.bind( self ), 25 ),
			updateElmsInitialData: $ush.debounce( self._updateElmsInitialData.bind( self ), 1 ),
		};

		// Events
		$us.$window
			// Handler for the scroll event
			.on( 'scroll', self._events.scroll )
			// Handler for viewport resize event
			.on( 'resize', self._events.resize );

		// Handler for updating initial data when content changes
		$us.$canvas
			.on( 'contentChange', self._events.updateElmsInitialData );
	}

	// Scroll Effect API
	ScrollEffects.prototype = {

		/**
		 * Add a node to the list of elements.
		 *
		 * @param {Node|[Node...]} node The node or array of nodes.
		 */
		addElms: function( nodes ) {
			var self = this;
			if ( ! $.isArray( nodes ) ) {
				nodes = [ nodes ];
			}
			nodes.map( function( node ) {
				if ( $ush.isNode( node ) ) {
					self.elms.push( new SE_Node( node ) );
				}
			} );
		},

		/**
		 * Updates initial data for all elements.
		 */
		_updateElmsInitialData: function() {
			var self = this;
			self.elms.map( function( element ) {
				element.setInitialData();
			} );
		},

		/**
		 * Handler for viewport resize event.
		 *
		 * @event handler
		 */
		_resize: function() {
			var self = this;

			// Turn effects on or off for the current screen width
			var disableEffects = isDisableEffects();
			if ( _$temp.disableEffects !== disableEffects ) {
				_$temp.disableEffects = disableEffects;
				self.elms.map( function( element ) {
					element[ disableEffects ? 'removeEffects' : 'setEffects' ]();
				} );
			}

			// If the body height has not changed, then exit
			var bodyHeight = $ush.parseInt( _body.clientHeight );
			if ( _$temp.bodyHeight === bodyHeight ) {
				return;
			}
			_$temp.bodyHeight = bodyHeight;
			self._updateElmsInitialData(); // updates initial data for all elements
		},

		/**
		 * Handler for the scroll event.
		 *
		 * @event handler
		 */
		_scroll: function() {
			var self = this;
			if ( isDisableEffects() ) {
				return;
			}
			self.elms.map( function( element ) {
				// If the node is outside the viewport, then skip it
				if ( ! element.isInViewport() ) {
					element.node.classList.remove( 'in_viewport' );
					return;
				}
				element.node.classList.add( 'in_viewport' );
				element.setEffects();
			} );
		}

	};

	// Export API
	$us.scrollEffects = new ScrollEffects;

	/**
	 * @class SE_Node - Scroll Effects Node.
	 * @param {Node} node The node.
	 */
	function SE_Node( node ) {
		var self = this;

		// Variables
		self.node = node;
		self.nearestTop = 0; // nearest top of initial or current position
		self.initialData = {
			top: 0,
			height: 0,
		};
		self._config = {
			start_position: '0%', // distance from the bottom screen edge, where the element starts its animation
			end_position: '100%', // distance from the bottom screen edge, where the element ends its animation
		};

		// Load configuration
		var $node = $( node );
		$.extend( self._config, $node.data( 'us-scroll' ) || {} );
		$node.removeAttr( 'data-us-scroll' );

		// Set initial data
		self.setInitialData();

		// Init effects
		if ( ! isDisableEffects() ) {
			self.setEffects();
		}

		// Set classes and css variable
		node.classList.toggle( 'in_viewport', self.isInViewport() );

		// Important! Set after init node positions
		$ush.timeout( function() {
			node.style.setProperty( '--scroll-delay', self.getParam( 'delay' ) );
		}, 1 );
	}

	// Scroll Effects Node API
	SE_Node.prototype = {

		/**
		 * Determines if in viewport.
		 *
		 * @return {Boolean} True if in viewport, False otherwise.
		 */
		isInViewport:function() {
			// Test version 1
			// var initialData = this.initialData,
			// 	scrollTop = scroll().top;
			// return (
			// 	initialData.top <= ( scrollTop + _window.innerHeight )
			// 	&& ( initialData.top + initialData.height ) >= scrollTop
			// );

			// Test version 2
			var self = this,
				rect = $ush.$rect( self.node ), // current data
				initialTop = self.initialData.top - scroll().top,
				nearestTop = min( initialTop, rect.top ) - _window.innerHeight;
			self.nearestTop = nearestTop;
			return nearestTop <= 0 && ( max( initialTop, rect.top ) + rect.height ) >= 0;
		},

		/**
		 * Set or update initial data.
		 */
		setInitialData: function() {
			var self = this,
				rect = $ush.$rect( self.node );
			self.initialData.top = scroll().top + rect.top;
			self.initialData.height = rect.height;

			// Exclude effects
			self.initialData.top -= $ush.parseFloat( self.node.style.getPropertyValue( '--translateY' ) );
		},

		/**
		 * Get params value.
		 * Note: The method supports dynamic date attributes in Edit Live.
		 *
		 * @param {String} name The param name.
		 * @param {Mixed} defaultValue The default value.
		 * @return {Mixed} Returns param values.
		 */
		getParam: function( name, defaultValue ) {
			var self = this;
			return (
				self.node.dataset[ name ]
				|| self._config[ name ]
				|| defaultValue
			);
		},

		/**
		 * Set the all effects.
		 */
		setEffects: function() {
			this.setTranslateY();
		},

		/**
		 * Remove the all effects.
		 */
		removeEffects: function() {
			this.node.style.setProperty( '--translateY', '' );
		},

		/**
		 * Set vertical offset.
		 */
		setTranslateY: function() {
			var self = this;

			// If the speed is not set, then exit
			var translateSpeed = $ush.parseFloat( self.getParam( 'translate_y_speed' ) );
			if ( ! translateSpeed ) {
				return;
			}

			// Determine direction down
			var directionIsDown = self.getParam( 'translate_y_direction' ) === _DIRECTION_.DOWN;
			if ( ! directionIsDown ) {
				translateSpeed = -translateSpeed;
			}

			/**
			 * Get final translate y-coordinate relative to the center.
			 *
			 * @param {Number} position The position in percentage.
			 * @return {Number} The final translate y-coordinate relative to the center.
			 */
			function getFinalTranslateY( position ) {
				return ( position - /* center: ( 100% / 2 ) */50 ) * ( _TRANSLATE_FACTOR_ * translateSpeed );
			}

			// Test version 1
			//var currentPosition = self.initialData.top - scroll().top + ( self.initialData.height / 2 );

			// Test version 2
			var currentPosition = directionIsDown
				? _window.innerHeight + self.nearestTop
				: self.initialData.top - scroll().top;
			currentPosition += ( self.initialData.height / 2 );

			currentPosition = 100 - ( currentPosition / _window.innerHeight * 100 ); // R = 100% - ( A1 / A2 * 100% )
			currentPosition = min( 100, max( 0, currentPosition ) ); // correction value out of range: 0-100%

			// Get translate Y-coordinate
			var translateY = getFinalTranslateY( currentPosition );

			// Set the "Animation Start Position" in viewport
			var startPosition = $ush.parseInt( self.getParam( 'start_position' ) );
			if ( startPosition && floor( currentPosition ) <= startPosition ) {
				translateY = getFinalTranslateY( startPosition );
			}

			// Set the "Animation End Position" in viewport
			var endPosition = $ush.parseInt( self.getParam( 'end_position' ) );
			if ( endPosition && floor( currentPosition ) >= endPosition ) {
				translateY = getFinalTranslateY( endPosition );
			}

			// Set value to css variable
			self.node.style.setProperty( '--translateY', translateY + 'px' );
		}
	};

	// Add to jQuery
	$.fn.usScrollEffects = function() {
		return this.each( function() {
			$us.scrollEffects.addElms( this );
		} )
	};

	// Init scroll effects
	$( function() {
		$( '[data-us-scroll]' ).usScrollEffects();
	} );

}( jQuery );
