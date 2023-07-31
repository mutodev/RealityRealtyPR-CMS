YAMMON.Widgets.i18n = new Class({
    Extends: YAMMON.Widget ,
    options: {},
    initialize: function( node , options ){
        this.parent( node , options );
        this.links = node.getElements('.ym-form-i18n-language-selector a');

        for( var i = 0 ; i < this.links.length ; i++ ){
            var link = this.links[i];

            link.addEvent( 'click' , this.onClick.bindWithEvent( this , [link] ) );
        }

    },
    onClick: function( e , link ){

        e.stop();

        //Activate Link
        for( var i = 0 ; i < this.links.length ; i++ ){
            this.links[i].getParent('li').removeClass('ym-form-i18n-language-selector-active');
        }
        link.getParent('li').addClass('ym-form-i18n-language-selector-active');

        //Change Content
        var node = this.getNode()

        //Hide Current
        node.getElements('> div:not(.ym-form-i18n-item-hide)').addClass('ym-form-i18n-item-hide');

        //Show New Language
        node.getElements('> div.ym-form-i18n-item-lang-' + link.get('lang')).removeClass('ym-form-i18n-item-hide');
    }
});

