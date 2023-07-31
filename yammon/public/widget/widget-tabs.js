YAMMON.Widgets.Tabs = new Class({
    Extends: YAMMON.Widget ,
    options: {
        remote: false
    },
    initialize: function( node , options ){
        this.parent( node , options );
        this.links    = node.getElements('.yammon-tabs-ul a');
        this.contents = node.getElements('.yammon-tabs-content');
        for( var i = 0 ; i < this.links.length ; i++ ){
            var link    = this.links[i];
            var content = this.contents[i];
            
            if( content.hasClass('yammon-tabs-content-active') )
                content.store('tab-loaded' , true );
            
            link.addEvent( 'click' , this.onClick.bindWithEvent( this , [link , content] ) );
        }
        
    },
    onClick: function( e , link , content ){
                
        e.stop();
                        
        //Activate Link
        for( var i = 0 ; i < this.links.length ; i++ ){
            this.links[i].getParent('li').removeClass('yammon-tabs-active');
        }
        link.getParent('li').addClass('yammon-tabs-active');

        //Show Content
        for( var i = 0 ; i < this.contents.length ; i++ ){
            this.contents[i].removeClass('yammon-tabs-content-active');
        }
        content.addClass('yammon-tabs-content-active');            

        //Load Content
        if( this.options.remote && !content.retrieve('tab-loaded') ){

            var id = 'helper.tabs.' + this.node.get('id').toLowerCase() + "." + link.get('rel');
            var req = new Request.HTML({
                url:    link.href ,
                update: content ,
                headers: { 
                    'X-YAMMON-REQUEST'          : 'PARTS' ,
                    'X_YAMMON_PARTS'            : id
                }
            }).send();
        }


    
    }
});

