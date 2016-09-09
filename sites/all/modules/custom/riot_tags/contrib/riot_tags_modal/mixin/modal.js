/**
 * An updated version of modalEffects.js by http://www.codrops.com
 */
var RiotModal = {

  ModalOverlay: null,
  ModalUtils: {

    hasClass: function (ele, cls) {
      return !!ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
    },

    addClass: function (ele, cls) {
      if (!this.hasClass(ele, cls)) {
        ele.className += " " + cls;
      }
    },

    removeClass: function (ele, cls) {
      var reg;
      if (this.hasClass(ele, cls)) {
        reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
        ele.className = ele.className.replace(reg, ' ');
      }
    },

    generateOverlay: function(modal) {
      var ov = document.createElement('DIV');
      ov.className = 'md-overlay';
      modal.parentNode.insertBefore(ov, modal.nextSibling);
      return ov;
    }
  },

  init: function(){
    this.on('updated', function() {
      this.updateModals();
    });
  },

  updateModals: function() {
    var self = this;
    [].slice.call( this.root.querySelectorAll( '.md-trigger' ) ).forEach( function( el, i ) {
      if(!el.ModalOverlay) {
        el.ModalContent = self.root.querySelector( '#' + el.getAttribute( 'data-modal' ) )
        el.ModalClose = el.ModalClose || el.ModalContent.querySelector( '.md-close' )
        el.ModalOverlay = self.ModalUtils.generateOverlay(el.ModalContent)
        el.addEventListener( 'click', function( ev ) {
          self.ModalUtils.addClass( el.ModalContent, 'md-show' );
          el.ModalOverlay.removeEventListener( 'click', removeModalHandler )
          el.ModalOverlay.addEventListener( 'click', removeModalHandler )
          if( self.ModalUtils.hasClass( el, 'md-setperspective' ) ) {
            setTimeout( function() {
              self.ModalUtils.addClass( el.ModalContent, 'md-perspective' )
            }, 25 )
            self.trigger('md-show')
          }
        })
        if(typeof el.ModalClose != 'undefined' && el.ModalClose != null) {
          el.ModalClose.addEventListener( 'click', function( ev ) {
            removeModalHandler();
          })
        }
      }
      function removeModal( hasPerspective ) {
        self.ModalUtils.removeClass( el.ModalContent, 'md-show' )
        if( hasPerspective ) {
          self.ModalUtils.removeClass( el.ModalContent, 'md-perspective' )
        }
      }
      function removeModalHandler() {
        removeModal( self.ModalUtils.hasClass( el, 'md-setperspective' ) )
      }
    })
  }
}

riot.mixin('RiotModal', RiotModal);
