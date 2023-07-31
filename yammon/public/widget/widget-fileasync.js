YAMMON.Widgets.FileAsync = new Class({
    Extends: YAMMON.Widget ,
    options: {
        element_id:    null,
        max_file_size: '1022mb',
        chunk_size:    '1mb',
        file_filters:  []
    },
    plupload: null,
    timerID: {},
    initialize: function( node , options ){
        this.parent( node , options );

        //Create Plupload element
        var container     = this.getNode();
        var fileBox       = node;
        var picker        = fileBox.getElement('.plupload_button_edit');
        var fileBox_id    = 'plupload_filebox_'   + new Date().getTime() + '_' + Math.floor(Math.random()*10000001);
        var id            = 'plupload_selectBtn_' + new Date().getTime() + '_' + Math.floor(Math.random()*10000001);

        //Set picker id and container relation
        picker.set('id'  , id);
        fileBox.set('id' , fileBox_id);
        fileBox.set('ref', id);

        this.plupload = new plupload.Uploader({
            runtimes            : 'html5,flash,silverlight',
            fileBox_id          : fileBox_id,
            key                 : null,
            multi_selection     : false,
            browse_button       : id ,
            chunk_size          : this.options.chunk_size ,
            max_file_size       : this.options.max_file_size ,
            url                 : window.location.href ,
            flash_swf_url       : '/yammon/public/plupload/plupload.flash.swf' ,
            silverlight_xap_url : '/yammon/public/plupload/plupload.silverlight.xap',
            multipart           : true,
            multipart_params    :{
                'X-YAMMON-REQUEST' : 'HELPER_FORM_ELEMENT_FILEASYNC',
                'ELEMENT'          : this.options.element_id,
                'FILE_ID'          : null
            },
            resize : { width :  this.options.max_width   ,
                       height : this.options.max_height  ,
                       quality : 90} ,
            filters : this.options.file_filters
        });


        //Plupload Events
        this.plupload.bind('FilesAdded'     , this.pluploadFilesAdded     , this);
        this.plupload.bind('BeforeUpload'   , this.pluploadBeforeUpload   , this);
        this.plupload.bind('FileUploaded'   , this.pluploadFileUploaded   , this);
        this.plupload.bind('UploadProgress' , this.pluploadUploadProgress , this);
        this.plupload.bind('Error'          , this.pluploadError          , this);

        this.plupload.init();

        //Add Handlers
        fileBox.addEvent( 'mouseover:relay(.plupload_button_edit)' , this.onEditBtn.bindWithEvent( this ) );
        fileBox.addEvent( 'click:relay(.plupload_button_cancel)'   , this.onCancelBtn.bindWithEvent( this ) );
        fileBox.addEvent( 'click:relay(.plupload_button_delete)'   , this.onDeleteBtn.bindWithEvent( this ) );
    },

    refresh: function(){
        this.plupload.refresh();
    },

    pluploadFilesAdded: function(up, files){

        // Upload Only one file
        $each(files, function( file , i ) {
            if ( i > 0 )
                up.removeFile( file );
        });

        //No file added
        if ( !(file = files[0]) )
            return;

        //Change status and filebox state
        this.changeState('uploading', file);

        //Set the uploading key to prevent multiples sents
        up.settings.key = file.id;
        up.settings.multipart_params['FILE_ID'] = file.id;

        //Start Upload
        setTimeout( function(){
            up.start();
        } , 250 );

    },

    pluploadBeforeUpload: function(up, file){

        //Lock the form
        var form     = this.node.getParent('form');
        var widget   = form ? form.retrieve('widget.form') : false;
        if( widget ) widget.lock( );

        //Start timer to prevent session timeout (5 minutes)
        this.timerID[file.id] = setInterval( function() { new Request({ url: window.location.href }).send(); }, 300000 );
    },

    pluploadFileUploaded: function( up , file , ret ){

        //Clear Timer
        clearTimeout( this.timerID[file.id] );

        //Get Item
        var response = JSON.decode( ret.response );

        //The keys must be the same
        if ( file.id == up.settings.key && file.status == plupload.DONE ) {

            // Change status and filebox state
            this.changeState('edit', file, response);

            //Unlock the form
            var form     = this.node.getParent('form');
            var widget   = form ? form.retrieve('widget.form') : false;
            if( widget ) widget.unlock( );
        }
        else if ( file.status == plupload.FAILED )
            this.changeState('new');


    },

    pluploadUploadProgress: function(up, file) {

        //The keys must be the same
        if ( file.id == up.settings.key ) {

            //Update the progress bar
            var uploadingElement = $(up.settings.fileBox_id).getElement('.plupload_loadingbar div');
            uploadingElement.setStyle('width' , up.total.percent + '%' );
        }

    },

    pluploadError: function(up, error ){

        var code = error.code;
        var msg  = error.message;
        var file = error.file;

        //Clear Timer
        clearTimeout( this.timerID[file.id] );

        //Show error to the user
        var txt = "There was an unexpected error\n";
        if( file )
            txt += "uploading file " + file.name + "\n";
        txt += "\n";

        if( code )
            txt += "code: " + code + "\n";

        if( msg )
            txt += "message: '" + msg + "'";

        // Change filebox state
        this.changeState('new');
    },

    onEditBtn: function(e) {

        //Stop Event
        e.stop();

        this.plupload.refresh();
    },

    onCancelBtn: function(e) {

        //Stop Event
        e.stop();

        //Clear Timer
        clearTimeout( this.timerID[this.plupload.settings.key] );

        //Stop the plupload Upload
        this.plupload.stop();
        this.plupload.settings.key = null;


        //Unlock the form
        var form     = this.node.getParent('form');
        var widget   = form ? form.retrieve('widget.form') : false;
        if( widget ) widget.unlock( );

        this.changeState('new');
    },

    onDeleteBtn: function(e) {

        //Stop Event
        e.stop();

        this.changeState('new');
    },

    changeState: function(state, file, response) {

        //Get elements
        var up          = this.plupload;
        var fileBox     = $( up.settings.fileBox_id );
        var hiddenField = fileBox.getPrevious('input.form_element_plupload');
        var id          = fileBox.get('ref');

        //Hide all
        fileBox.getElement('.plupload_new').setStyle('display' , 'none' );
        fileBox.getElement('.plupload_uploading').setStyle('display' , 'none' );
        fileBox.getElement('.plupload_edit').setStyle('display' , 'none' );

        //Change file information
        if ( file ) {
            if ( fileName = fileBox.getElements('.plupload_filename') )
                fileName.set('html' , file.name );

            if ( fileSize = fileBox.getElements('.plupload_filesize') )
                fileSize.set('html' , '- ' + plupload.formatSize(file.size) );
        }

        //New State
        if ( state == 'new' ) {

            hiddenField.set('value', '');
            fileBox.getElement('.plupload_new').show();
        }
        //Uploading State
        else if ( state == 'uploading' ) {

            //Update the progress bar
            var uploadingElement = fileBox.getElement('.plupload_loadingbar div');
            uploadingElement.setStyle('width' , '0%' );

            fileBox.getElement('.plupload_uploading').show();
        }
        //Edit State
        else if ( state == 'edit' ) {

            //Ajax error
            if (!response) {
                fileBox.getElement('.plupload_new').show();
                up.refresh();

                return;
            }

            fileBox.getElement('.plupload_edit').dispose();
            fileBox.adopt( new Element('div', {html: response.edit_tpl}).getChildren() );

            hiddenField.set('value', response.filename);
            fileBox.getElement('.plupload_edit').show();

        }

        up.refresh();

    }

});

