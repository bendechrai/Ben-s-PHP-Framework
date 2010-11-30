function init() {
	init_forms();
	init_tabs();
	init_messages();
	init_sort();
	if( window.init_local ) init_local();
}

function init_sort() {

	$each( $$('table.sortable'), function( element ) {

		if( !element.id ) {
			element.setAttribute( 'id', 'element-' + element.uid );
		}

		var myTable = new sortableTable(element.id);
	});
}

function init_messages() {

	if( $$('div#flash_messages').length == 1 ) {

		new Fx.Style( $('flash_messages'), 'opacity', {duration: 1000} ).start(0,1);

		$('flash_messages').addEvent( 'click', function( element ) {
			new Fx.Style( $('flash_messages'), 'opacity', {duration: 0} ).start(0);
		});

		var fx = new Fx.Style( $('flash_messages'), 'opacity', {duration: 1000} );
		fx.start.delay( 10000, fx, [0] );

	}

}

function init_forms() {

	function create_dynamic_remove_button( element ) {
		var button = document.createElement( 'input' );
		button.type='button';
		button.value='remove';
		button.class='button remove';
		button.addEvent( 'click', function( e ) {
			element.parentNode.removeChild( element );
		});
		return button;
	}

	// Add delete buttons to removable elements
	$each( $$( 'form .dynamic .removable' ), function( element ) {
		element.parentNode.appendChild( create_dynamic_remove_button( element ) );
	});

	var count = 1;

	// Add "add" buttons to dynamic lists
	$each( $$( 'form .dynamic' ), function( element ) {

		var data = element.getAttribute('rel').match(/^([^\|]+)\|([^\|]+)\|(.*)$/);
		data[1] = data[1].replace( '_', ' ' );

		var tag = document.createElement( data[3] );

		var input = document.createElement( 'input' );
		input.setAttribute( 'type', 'text' );

		var button = document.createElement( 'input' );
		button.type='button';
		button.value='add';
		button.class='button add';
		button.addEvent( 'click', function( e ) {

			// Get the text in the text box
			var text = e.target.parentNode.getElementsByTagName( 'input' ).item(0).value;

			if( text != '' ) {

				var tag = document.createElement( data[3] );

				var label = document.createElement( 'label' );
				label.appendChild( document.createTextNode( text ) );

				var input = document.createElement( 'input' );
				input.setAttribute( 'name', 'customfield_new[' + count + ']' );
				input.setAttribute( 'id', 'customfield_' + count );
				input.setAttribute( 'class', 'removable' );
				input.setAttribute( 'type', 'hidden' );
				input.setAttribute( 'value', text );

				tag.appendChild( label );
				tag.appendChild( input );
				tag.appendChild( create_dynamic_remove_button( tag ) );

				this.parentNode.parentNode.insertBefore( tag, this.parentNode );
				++count;

				e.target.parentNode.getElementsByTagName( 'input' ).item(0).value = '';
				e.target.parentNode.getElementsByTagName( 'input' ).item(0).focus();
			}
		});

		tag.appendChild( input );
		tag.appendChild( button );
		element.appendChild( tag );

	});

	// Add calendars to date fields
	$each( $$('input.date'), function( element ) {
		var obj = new Object();
		obj[element.id] = 'jS M Y';// H:i:s';

		// Define direction based on optional input element class "date-after-xx" where xx is the number of days in the future
		var direction = 1;
		var data = element.getAttribute('class').match(/date-after-([0-9]+)/);
		if( data ) direction = data[1];

		var calendar = new Calendar( obj, { direction: direction, tweak: {x: 3, y: -3} });
	});


	// Get confirmation for delete buttons
	$each( $$( 'input.button.delete' ), function( element ) {
		element.addEvent( 'click', function( e ) {
			if( !confirm( 'Are you sure you wish to preoceed with this deletion operation?' ) ) {
				e.preventDefault();
			}
		});
	});

	// Get confirmation for activate buttons
	$each( $$( '#campaign-edit input.button.activate' ), function( element ) {
		element.addEvent( 'click', function( e ) {
			if( !confirm( 'Are you sure you wish to activate this campaign?\n\nOnce activated, a campaign that has any requests associated with it cannot be deactivated and core settings cannot be altered.' ) ) {
				e.preventDefault();
			}
		});
	});

	// Add toggle checkboxes to lists (only OL and UL)
	var checkboxtogglecounter = 0;
	$each( $$('ol.checkboxtoggle', 'ul.checkboxtoggle'), function( element ) {

		++checkboxtogglecounter;

		// Add a <li><input type="checkbox" class="checkbox" name="toggler" id="togglerX"/><label class="nofloat" for="togglerX">Check All</label></li>
		// to the end of this element's children

		var input = new Element( 'input', {
			'type': 'checkbox',
			'class': 'checkbox',
			'name': 'toggler',
			'id': 'toggler' + checkboxtogglecounter,
			'events': {
				'change': function( e ) {
					var toggler = e.target;
					$each( this.parentNode.parentNode.getElements( 'input.checkbox' ), function( checkbox ) {
						if( checkbox.id != toggler.id ) checkbox.checked = toggler.checked;
					} );
				}
			}
		});

		var label = new Element( 'label', {
			'class': 'nofloat',
			'for': 'toggler' + checkboxtogglecounter,
			'html': 'Check All'
		});

		var li = new Element( 'li' );
		li.appendChild( input );
		li.appendChild( label );

		element.appendChild( li );

	});

	// Find forms that contain checkboxes with a toggle class
	$each( $$('table'), function( element ) {

		// How many checkboxes does this table have? If > 0 then process
		if( element.getElements('input').filter('.checkbox.toggle').length > 0 ) {

			++checkboxtogglecounter;

			// Add a <p><input type="button" class="button" name="toggler" id="togglerX" value="Check All"/></p>
			// after the table

			var input = new Element( 'input', {
				'type': 'button',
				'class': 'button',
				'name': 'toggler',
				'value': 'Check All',
				'id': 'toggler' + checkboxtogglecounter,
				'rel': '0',
				'events': {
					'click': function( e ) {
						var toggler = e.target;
						toggler.rel = ( toggler.rel == '1' ? '0' : '1' );
						$each( element.getElements('input').filter('.checkbox.toggle'), function( checkbox ) {
							if( checkbox.id != toggler.id ) checkbox.checked = ( toggler.rel == 1 );
						} );
					}
				}
			});

			var p = new Element( 'p' );
			p.appendChild( input );

			if(element.parentNode.lastChild == element) {
				element.parentNode.appendChild(p);
			} else {
				element.parentNode.insertBefore(p, element.nextSibling);
			}


		}

	});

	// Make first form element active
	var formelements = $$('input.text', 'input.password', 'select', 'textbox');
	if( formelements.length > 0 ) formelements[0].focus();

}

function init_tabs() {

	if( $$('.tabbed').length > 0 ) {

		// Remove all existing events from tabs
		$each( $$( 'div.tabbed ul a' ), function( element ) {
			element.removeEvents( 'click' );
		});

		// Create accordion
		var accordion = new Accordion( 'div.tabbed ul a', 'div.tabbed div.tabcontents', {
			height: false,
			opacity: false,
			fixedHeight: true,
			onActive: function(toggler, element) {
				toggler.addClass( 'active' );
				element.setStyle( 'display', 'block' );
				toggler.blur();
			},
			onBackground: function(toggler, element) {
				toggler.removeClass( 'active' );
				element.setStyle( 'display', 'none' );
			}
		});

		var currentTabInputs = new Array();
		var currentTab = 1;
		if( window.flashCurrentTab ) currentTab = flashCurrentTab;

		var tabCount = $$('.tabbed .tabs ul li').length;
		// Add a 'Next Tab' button to the campaign edit page and a form field to save tab settings accross page loads
		if( $$('.tabbed .tabs ul li').length > 0 ) {
			$each( $$('form'), function( element ) {
				var input = document.createElement( 'input' );
				input.setAttribute( 'type', 'hidden' );
				input.setAttribute( 'name', '__currentTab' );
				input.setAttribute( 'class', 'currentTabNumber' );
				input.setAttribute( 'value', currentTab );
				element.appendChild( input );
			});
			$each( $$('p.navigation-buttons'), function( element ) {
				var input = document.createElement( 'input' );
				input.setAttribute( 'type', 'button' );
				input.setAttribute( 'value', 'Next Tab' );
				input.addEvent( 'click', function( e ) {
					currentTab = ( currentTab % tabCount ) + 1;
					document.getElementById( 'tab_' + currentTab ).fireEvent( 'click', {target: document.getElementById( 'tab_' + currentTab )} );
				});
				element.appendChild( input );
			});
		}

		// Prevent default action (go to anchor)
		$each( $$( 'div.tabbed ul a' ), function( element ) {
			element.addEvent( 'click', function( e ) {
				if( e && e.preventDefault ) e.preventDefault();
				var tabNumber = e.target.id.replace( /tab_/, '' );
				$each( $$( '.currentTabNumber' ), function( element ) {
					element.setAttribute( 'value', tabNumber );
				});
			});
		});

		// focus default tab
		document.getElementById( 'tab_' + currentTab ).fireEvent( 'click', {target: document.getElementById( 'tab_' + currentTab )} );

	}

}
