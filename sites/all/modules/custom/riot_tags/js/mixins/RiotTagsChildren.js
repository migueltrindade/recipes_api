(function($) {
  var RiotTagsChildren = {
    init: function() {
      this.one('update', function() {
        this.processChildren()
        this.mountChildren()
      })
    },
    getChildExtraOptions: function(c) {
      return {}
    },
    processChildren: function() {
      this.children = []
      if(this.opts && typeof this.opts.children != 'undefined' && this.opts.children != null) {
        for(var c in this.opts.children) {
          if(this.opts.children.hasOwnProperty(c)) {
            var children = this.opts.children[c].hasOwnProperty('children') ? this.opts.children[c].children : null
            var opts = this.opts.children[c].hasOwnProperty('options') ? this.opts.children[c].options : {}
            this.children.push({
              plugin: c,
              weight: this.opts.children[c].plugin_weight,
              options: Object.assign(opts, { 'children': children }, this.getChildExtraOptions(this.opts.children[c])),
              tag: this.opts.children[c].html_tag
            })
          }
        }
        this.sortChildren()
      }
    },
    hasChildByPlugin: function(cls) {
      for(var ind in this.children) {
        if(this.children[ind].plugin == cls) {
          return true
        }
      }
      return false
    },
    getChildByPlugin: function(cls) {
      for(var ind in this.children) {
        if(this.children[ind].plugin == cls) {
          return this.children[ind]
        }
      }
      return null
    },
    mountChildren: function() {
      var self = this,
        l = this.children.length;
      $(this.children).each(function() {
        if(!this.mounted) {
          var tag = $('<' + this.tag + ' />')
          $(self.root).append(tag)
          riot.mount(tag, this.tag, this.options)
          this.mounted = true
          l--;
          if(!l) {
            self.trigger('childrenMounted');
          }
        }
        $(self.root).addClass('haschild-' + this.tag);
      })
    },
    sortChildren: function() {
      this.children.sort(function(a, b) {
        var aw = Number(a.weight), bw = Number(b.weight)
        if(aw < bw) {
          return -1
        }
        if(aw > bw) {
          return 1
        }
        return 0
      })
    }
  };
  riot.mixin('RiotTagsChildren', RiotTagsChildren);
})(jQuery)