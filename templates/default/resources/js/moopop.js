/*
  moopop: unobtrusive javascript popups via late binding using mootools 1.11
  
  copyright (c) 2007 by gonchuki - http://blog.gonchuki.com
  
  version:	1.0
  released: August 12, 2007
  
  This work is licensed under a Creative Commons Attribution-Share Alike 3.0 License.
    http://creativecommons.org/licenses/by-sa/3.0/
*/

/*
  Basic usage:
    add a rel attribute to your <a> tags to look like this:
      <a href="http://blog.gonchuki.com" rel="popup">foobar</a>
      or
      <a href="http://blog.gonchuki.com" rel="popup[600, 400]">foobar</a>
      
    where:
      "popup" is the default string token to match against so the popup behavior
              can be attached.
      "[600, 400]" is the (optional) size of the newly created window.
*/

var moopop = {
  width: 0,
  height: 0,
  /*
    Function: captureByRel
      standard capturing method, it's autorun onDomReady and you can manually use it
      to capture a different set of popup windows.
      
    Syntax:
      moopop.captureByRel(value, element);
      
    Arguments:
      value - The partial string to match against the rel attribute of your links.
      element - [optional] a DOM element to restrict which links should be processed.
  */
  captureByRel: function(attrVal, parent) {
    this.capture($ES('a', parent || document).filterByAttribute('rel', '*=', attrVal));
  },
  
  /*
    Function: capture
      multipurpose function allowing for different methods of capturing the popups.
      
    Syntax:
      moopop.capture(obj, width, height);
      
    Arguments:
      obj - (mixed) can be either a DOM element, an Array of elements or a className.
      width - [optional] (integer) default width for popups without a given size, if
              specified you must also specify the height.
      height - [optional] (integer) default height for popups without a given size.
  */
  capture: function(el, width, height) {
    if ($defined(width) && $defined(height)) {
      this.width = width;
      this.height = height;
    }
    
    switch ($type(el)) {
      case 'element':
        this.add_pop_to(el);
        break;
      case 'string':
      case 'array':
        $$(el).each( function(el) {
          this.add_pop_to(el);
        }, this);
        break;
    }
    
    this.width = null;
    this.height = null;
  },
  
  /*
    Function: add_pop_to
      Primarily used internally but you can also use it to manually attach the popup
      behavior to a single DOM element.
      
    Syntax:
      moopop.add_pop_to(element);
      
    Arguments:
      element - a DOM element to process.
  */
  add_pop_to: function(el) {
    el.addEvent('click', function(e){ new Event(e).stop(); this.popup(el); }.bind(this));
    
    var size = el.getAttribute('rel').match(/\[(\d+),\s*(\d+)\]/) || ['', this.width, this.height];
    
    if (size[1]) el.setAttribute('popupprops', 'width=' + size[1] + ', height=' + size[2] + ', scrollbars=1' );
  },
  
  /*
    Function: popup
      Triggers the popup behavior on a given link. Used internally but you can also use it to
      force a given unprocessed link to open in a new window.
      
    Syntax:
      moopop.popup(element);
      
    Arguments:
      element - a DOM element to process.
  */
  popup: function(el) {
    // if querystring exists
    if( el.href.match(/\?/) == '?' ) {
        // append popup marker
        el.href += '&popup=1';
    } else {
        // add popup marker
        el.href += '?popup=1';
    }
    window.open(el.href, '', el.getAttribute('popupprops') || '');
  }
};

/*
  process all links with rel="popup" by default.
*/
window.addEvent('domready', function () {
  moopop.captureByRel('popup');
});
