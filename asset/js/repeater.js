(function($){

    function repeaterField(key) {
        var $this = this;
        this.$addTrigger = $('#'+key+'_add');
        this.$list = $('#'+key+'_list');
        this.$form = $('#'+key+'_form');
        this.$addTrigger.on('click', function(e){
            e.preventDefault();
            $this.addItem();
        });

        this.$form.modal({
            autofocus: false
        });

        this.$form.submit(function(){
            $this.formSubmit();
        });
    }

    repeaterField.prototype.formSubmit = function() {
        this.$list.find('.loader').addClass('indeterminate');
        this.$list.dimmer('show');
        //save
        $.post(ajaxurl, this.$form.serialize(), function(){
            this.$list.find('.loader').removeClass('indeterminate');
            this.refreshList();
        });


    };

    repeaterField.prototype.addItem = function() {
        this.resetForm();
        this.$form.modal('show');
    };

    repeaterField.prototype.editItem = function(id) {
        this.resetForm();
        //get data and populate form with it
        this.$form.modal('show');
    };

    repeaterField.prototype.refreshList = function() {
        this.$list.find('.loader').removeClass('indeterminate');
        //refresh
        this.$list.dimmer('hide');
    };

    repeaterField.prototype.resetForm = function() {
        this.$form[0].reset();
    };


    $(document).ready(function(){
        if (typeof d3AdminUtil_repeaterFields != 'undefined' && Array.isArray(d3AdminUtil_repeaterFields)) {
            $.each(d3AdminUtil_repeaterFields, function(i, val){
               return new repeaterField(val);
            });
        }
    });
})(jQuery);
