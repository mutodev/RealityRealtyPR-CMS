YAMMON.Widgets.FileMultiple = new Class({
    Extends: YAMMON.Widget ,
    Implements: Options,
    options: {
        max_file_size:  '10mb' ,
        chunk_size:     '256kb' ,
        max_width:      null ,
        max_height:     null ,
        resize_quality: 90 ,
        extensions:     null
    },

    initialize: function( element , options )
    {

        //Load Options
        this.setOptions( options );

        //Get the element
        this.element = $( element );

        //Get the button
        this.button = this.element.getElement('.ym-form-file-multiple-start');
        if( !this.button.id ){
            this.button.id = this.element.id + '_button';
        }

        //Create the uploader
        var uploaderOptions = {
            runtimes            : 'html5,gears,flash,html4',
            drop_element        : this.element.id ,
            multi_selection     : true,
            browse_button       : this.button.id ,
            url                 : window.location.href ,
            flash_swf_url       : '/yammon/public/plupload/plupload.flash.swf' ,
            silverlight_xap_url : '/yammon/public/plupload/plupload.silverlight.xap',
            multipart           : true ,
            multipart_params    :{
                'X-YAMMON-REQUEST'   : 'HELPER_FORM_ELEMENT_FILE_MULTIPLE:UPLOAD',
                'X-YAMMON-REQUEST-ID': 1
            },
            required_features:  'multipart'
        };

        if( this.options.hasOwnProperty('chunk_size') ){
            uploaderOptions.chunk_size = this.options.chunk_size;
        }

        if( this.options.hasOwnProperty('max_file_size') ){
            uploaderOptions.max_file_size = this.options.max_file_size;
        }

        if( this.options.hasOwnProperty('max_width') ){
            if( !uploaderOptions.hasOwnProperty('resize') )
                uploaderOptions.resize = {};
            uploaderOptions.resize.width = this.options.max_width;
        }

        if( this.options.hasOwnProperty('max_height') ){
            if( !uploaderOptions.hasOwnProperty('resize') )
                uploaderOptions.resize = {};
            uploaderOptions.resize.height = this.options.max_height;
        }

        if( this.options.hasOwnProperty('resize_quality') ){
            if( !uploaderOptions.hasOwnProperty('resize') )
                uploaderOptions.resize = {};
            uploaderOptions.resize.quality = this.options.resize_quality;
        }

        if( this.options.hasOwnProperty('extensions') ){

            if( !uploaderOptions.hasOwnProperty('filters') )
                uploaderOptions.filters = [];

            for( var i in this.options.extensions )
                if( this.options.extensions.hasOwnProperty( i ) )
                    uploaderOptions.filters.push( { title: i , extensions: this.options.extensions[i] } );
        }

        //Create uploaders
        this.uploader = new plupload.Uploader( uploaderOptions );

        //Bind Events
        this.uploader.bind( 'Init'           , this.onInit.bind( this ) );
        this.uploader.bind( 'FilesAdded'     , this.onFilesAdded.bind( this ) );
        this.uploader.bind( 'UploadProgress' , this.onUploadProgress.bind( this ) );
        this.uploader.bind( 'FileUploaded'   , this.onFileUploaded.bind( this ) );
        this.uploader.bind( 'Error'          , this.onError.bind( this ) );
        this.button.addEvent( 'click'        , this.onClick.bindWithEvent( this ) );
        this.element.addEvent('click:relay(.ym-form-file-multiple-queue-item-delete)'   , this.onDelete.bindWithEvent( this ) );
        this.element.addEvent('click:relay(.ym-form-file-multiple-queue-item-moveup)'   , this.onMoveUp.bindWithEvent( this ) );
        this.element.addEvent('click:relay(.ym-form-file-multiple-queue-item-movedown)' , this.onMoveDown.bindWithEvent( this ) );

        //Initialize Uploader
        this.uploader.init();

        //Show empty message
        this.showEmptyMessage();

    },

    onClick: function( e ){
        e.stop();
    },

    onInit: function( up , params )
    {
        if( window.console )
            console.log( params.runtime );
    },

    onFilesAdded: function( up, files )
    {

        //Reposition Uploader
        up.refresh();

        //Auto Start Upload
        setTimeout( function(){
            up.start();
        } , 500 );

        //Reset the progress bar
        this.setProgress( 0 , true );

    },

    onUploadProgress: function( up, file )
    {
        this.setProgress( up.total.percent );
    },

    onFileUploaded: function( up, file , ret )
    {

        //Update Progress
        this.setProgress( up.total.percent );

        //Decode Return
        var response = JSON.decode( ret.response );

        //Create new entry
        var items         = this.element.getElements('.ym-form-file-multiple-queue-item');
        var templateItem  = this.element.getElement('.ym-form-file-multiple-queue-template');
        var lastItem      = items.length ? items[ items.length - 1 ] : templateItem;
        var newItem       = templateItem.clone();

        //Change the element
        newItem.addClass('ym-form-file-multiple-queue-item');
        newItem.removeClass('ym-form-file-multiple-queue-template');
        var a   = newItem.getElement('.ym-form-file-multiple-queue-item-preview');
        var id  = newItem.getElement('.ym-form-file-multiple-queue-item-id');
        var img = a.getElement('img');

        id.value = response.result.id;
        a.href   = response.result.download;
        img.src  = response.result.preview;

        newItem.setStyle('opacity' , 0 );
        newItem.inject( lastItem , 'after' );
        newItem.fade('in');

        //Hide Empty element
        this.showEmptyMessage();

    },

    onError: function( up, err )
    {
        if( window.console )
            console.log( err );
        alert( err.message );
    },

    onDelete: function( e )
    {
        var self     = this;
        var target   = $(e.target);
        var item     = target.getParent('.ym-form-file-multiple-queue-item');
        var fx       = new Fx.Tween( item , { 'property': 'opacity' , 'onComplete' : function(){

            //Destroy the item
            item.destroy();
            self.showEmptyMessage();


        }});
        fx.start( 0 );
    },

    onMoveUp: function( e )
    {
        var target = $(e.target);
        var item   = target.getParent('.ym-form-file-multiple-queue-item');
        var oitem  = item.getPrevious('.ym-form-file-multiple-queue-item');
        if( !oitem ) return;
        item.setStyle('opacity' , 0 );
        item.inject( oitem , 'before' );
        item.fade('in');
    },

    onMoveDown: function( e )
    {
        var target = $(e.target);
        var item   = target.getParent('.ym-form-file-multiple-queue-item');
        var oitem  = item.getNext('.ym-form-file-multiple-queue-item');
        if( !oitem ) return;
        item.setStyle('opacity' , 0 );
        item.inject( oitem , 'after' );
        item.fade('in');
    },

    showEmptyMessage: function()
    {

        if( !this['empty'] )
            this.empty = this.element.getElement('.ym-form-file-multiple-empty');

        if( !this['queue'] )
            this.queue = this.element.getElement('.ym-form-file-multiple-queue');

        var items = this.element.getElements('.ym-form-file-multiple-queue-item');

        if( !items.length ){
            this.empty.show();
            this.queue.hide();
        }else{
            this.empty.hide();
            this.queue.show();
        }

    },

    setProgress: function( percent , dont_animate )
    {

        if( !this['pb'] )
            this.pb = this.element.getElement('.ym-form-file-multiple-progress-bar');

        if( !this['bg'] )
            this.bg = this.element.getElement('.ym-form-file-multiple-progress-bg');

        if( !this['val'] )
            this.val = this.element.getElement('.ym-form-file-multiple-progress-value');

        if( !this['fx'] )
            this.fx  = new Fx.Tween( this.bg , { 'unit' : '%' , 'property': 'width' });

        if( dont_animate ){
            this.bg.setStyle('width' , '0%');
        }else{
            this.fx.stop();
            this.fx.start( percent );
        }
        this.val.set('html' , percent + '%' );

    }

});