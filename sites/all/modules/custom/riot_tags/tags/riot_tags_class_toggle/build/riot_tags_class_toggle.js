riot.tag2('riot-tags-class-toggle', '<ul class="riot-tags-class-toggle"> <li each="{si, i in opts.class_config}" class="{\'toggle-\' + si.class + (parent.active_classes[si.class] ? \' active\' : \'\')}"> <a onclick="{toggleClicked}" data-class="{si.class}"><i></i>{parent.active_classes[si.class] ? si.label_active : si.label_inactive}</a> </li> </ul>', 'riot-tags-class-toggle,[riot-tag="riot-tags-class-toggle"],[data-is="riot-tags-class-toggle"]{ display: block; position: relative; } riot-tags-class-toggle ul,[riot-tag="riot-tags-class-toggle"] ul,[data-is="riot-tags-class-toggle"] ul{ list-style: none; margin-left: 0; margin-bottom: 0; } riot-tags-class-toggle ul:after,[riot-tag="riot-tags-class-toggle"] ul:after,[data-is="riot-tags-class-toggle"] ul:after{ content: \'\'; display: block; width: 0; height: 0; clear: both; } riot-tags-class-toggle li,[riot-tag="riot-tags-class-toggle"] li,[data-is="riot-tags-class-toggle"] li{ width: 33.33333%; float: left; padding-left: 15px; padding-right: 15px; position: relative; padding-left: 0; padding-right: 0; text-align: center; list-style: none; margin-left: 0; } riot-tags-class-toggle li a,[riot-tag="riot-tags-class-toggle"] li a,[data-is="riot-tags-class-toggle"] li a{ display: block; height: 44px; background: #999; color: #fff !important; line-height: 44px; position: relative; } riot-tags-class-toggle li a:hover,[riot-tag="riot-tags-class-toggle"] li a:hover,[data-is="riot-tags-class-toggle"] li a:hover,riot-tags-class-toggle li a:active,[riot-tag="riot-tags-class-toggle"] li a:active,[data-is="riot-tags-class-toggle"] li a:active{ text-decoration: none; background-color: #7a7a7a; } riot-tags-class-toggle li a i,[riot-tag="riot-tags-class-toggle"] li a i,[data-is="riot-tags-class-toggle"] li a i{ display: inline-block; width: 18px; height: 20px; margin-right: 8px; margin-bottom: -3px; } riot-tags-class-toggle li.toggle-list-active a i,[riot-tag="riot-tags-class-toggle"] li.toggle-list-active a i,[data-is="riot-tags-class-toggle"] li.toggle-list-active a i{ background: url("/sites/all/modules/bluetent/riot_solr/images/icon-list-white.svg") center no-repeat; } riot-tags-class-toggle li.toggle-both-active a,[riot-tag="riot-tags-class-toggle"] li.toggle-both-active a,[data-is="riot-tags-class-toggle"] li.toggle-both-active a{ border-left: solid 1px #fff; } riot-tags-class-toggle li.toggle-both-active a i,[riot-tag="riot-tags-class-toggle"] li.toggle-both-active a i,[data-is="riot-tags-class-toggle"] li.toggle-both-active a i{ background: url("/sites/all/modules/bluetent/riot_solr/images/icon-splitview-white.svg") center no-repeat; } riot-tags-class-toggle li.toggle-both-active.active a,[riot-tag="riot-tags-class-toggle"] li.toggle-both-active.active a,[data-is="riot-tags-class-toggle"] li.toggle-both-active.active a{ border-left: 0; } riot-tags-class-toggle li.toggle-list-active.active + .toggle-both-active a,[riot-tag="riot-tags-class-toggle"] li.toggle-list-active.active + .toggle-both-active a,[data-is="riot-tags-class-toggle"] li.toggle-list-active.active + .toggle-both-active a{ border-right: solid 1px #fff; border-left: 0; } riot-tags-class-toggle li.toggle-map-active a i,[riot-tag="riot-tags-class-toggle"] li.toggle-map-active a i,[data-is="riot-tags-class-toggle"] li.toggle-map-active a i{ background: url("/sites/all/modules/bluetent/riot_solr/images/icon-map-white.svg") center no-repeat; margin-right: 4px; } riot-tags-class-toggle li.active a,[riot-tag="riot-tags-class-toggle"] li.active a,[data-is="riot-tags-class-toggle"] li.active a{ background: #333; }', '', function(opts) {
        (function(tag, $) {
            tag.active_count = 0
            tag.active_classes = {}
            for(i in opts.class_config) {
                if(opts.class_config[i].default == 'true') {
                    tag.active_classes[opts.class_config[i].class] = true
                    tag.active_count++
                }
            }
            tag.one('mount', function() {
                tag.update()
            })
            tag.on('update',function() {
                for(i in opts.class_config) {
                    $(tag.root).parent().toggleClass(opts.class_config[i].class, typeof tag.active_classes[opts.class_config[i].class] == 'undefined' ? false : tag.active_classes[opts.class_config[i].class])
                }
            })
            tag.toggleClicked = function(e) {
                if(tag.active_classes[e.item.si.class]) {
                    if(tag.active_count > 1 || opts.emtpy) {
                        tag.active_classes[e.item.si.class] = false
                        tag.active_count--
                    }
                } else {
                    if(parseInt(opts.multi) == 0) {
                        tag.active_classes = {}
                        tag.active_count = 0
                    }
                    tag.active_classes[e.item.si.class] = true
                    tag.active_count++
                }
                tag.update()
            }
        })(this, jQuery);
});
