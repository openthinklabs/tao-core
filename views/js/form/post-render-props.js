define([
    'jquery',
    'i18n'
],
    function($, __){


        /**
         * Toggle availability of mode switch (advanced/simple)
         *
         * @param newMode
         * @private
         */
        function _toggleModeBtn(newMode) {
            var $modeToggle = $('.property-mode');
            if(newMode === 'disabled') {
                $modeToggle.addClass('disabled');
            }
            else {
                $modeToggle.removeClass('disabled');
            }
        }

        /**
         * Reposition the radio buttons of a property and make them look nice.
         *
         * @private
         */
        function _upgradeRadioButtons($container) {

            $container.find('.form_radlst').not('.property-radio-list').each(function() {
                var $radioList = $(this);
                $radioList.addClass('property-radio-list');
                $radioList.parent().addClass('property-radio-list-box');
                $radioList.each(function() {
                    var $block = $(this),
                        $inputs = $block.find('input');

                    if($inputs.length <= 2) {
                        $block.find('br').remove();
                    }

                    $inputs.each(function() {
                        var $input = $(this),
                            $label = $block.find('label[for="' + this.id + '"]'),
                            $icon  = $('<span>', { 'class': 'icon-radio'});

                        $label.prepend($icon);
                        $label.prepend($input);
                    });
                });
            });
        }


        /**
         * Get reference to property container. If it doesn't' exist create one and add it to the DOM.
         *
         * @returns {*|HTMLElement}
         */
        function getPropertyContainer() {
            var $propertyContainer  = $('.content-block .property-container');
            if($propertyContainer.length) {
                return $propertyContainer;
            }
            $propertyContainer  = $('<div>', { 'class' : 'property-container' });
            $('.content-block .form-group').first().before($propertyContainer);
            return $propertyContainer;
        }


        /**
         * Add properties to the designated container. Also add some CSS classes for easier access.
         *
         * @param $properties
         * @private
         */
        function _wrapPropsInContainer($properties) {
            var $propertyContainer = getPropertyContainer($properties),
                // the reason why this is not done via a simple counter is that
                // the function could have been called multiple times, e.g. when
                // properties are created dynamically.
                hasAlreadyProperties = !!$propertyContainer.find('.property-block').length;


            $properties.each(function () {
                var $property = $(this),
                    type = (function() {
                        switch($property.attr('id').replace(/_?property_[\d]+/, '')) {
                            case 'ro':
                                return 'readonly-property';
                            case 'parent':
                                return 'parent-property';
                            default:
                                var $editIcon = $property.find('.icon-edit'),
                                    $editContainer = $property.children('div:first');

                                var $indexIcon = $property.find('.icon-add');

                                $editContainer.addClass('property-edit-container');

                                //on click on edit icon show property form or hide it
                                $editIcon.on('click', function() {
                                    //SlideToggle if the container is open and as property form or is close
                                    if(!$editContainer.parent().hasClass('property-edit-container-open') ||
                                        ($editContainer.parent().hasClass('property-edit-container-open') && $($('.property',$editContainer)[0]).is(':visible'))){
                                        $editContainer.slideToggle(function() {
                                            $editContainer.parent().toggleClass('property-edit-container-open');
                                            if(!$('.property-edit-container-open').length) {
                                                if($('.index',$editContainer).length > 0){
                                                    $('.property',$editContainer).each(function(){
                                                        var $currentTarget = $(this);
                                                        while(!_.isEqual($currentTarget.parent()[0], $editContainer[0])){
                                                            $currentTarget = $currentTarget.parent();
                                                        }
                                                        $currentTarget.hide();
                                                    });
                                                    $('.index-remover',$editContainer).each(function(){
                                                        $(this).parent().hide();
                                                    });
                                                }
                                                _toggleModeBtn('disabled');
                                            }
                                            else {
                                                if($('.index',$editContainer).length > 0){
                                                    //hide index properties
                                                    $('.index',$editContainer).each(function(){
                                                        var $currentTarget = $(this);
                                                        while(!_.isEqual($currentTarget.parent()[0], $editContainer[0])){
                                                            $currentTarget = $currentTarget.parent();
                                                        }
                                                        $currentTarget.hide();
                                                    });

                                                    $('.index-remover',$editContainer).each(function(){
                                                        $(this).parent().hide();
                                                    });

                                                    //show properties
                                                    $('.property',$editContainer).each(function(){
                                                        var $currentTarget = $(this);
                                                        while(!_.isEqual($currentTarget.parent()[0], $editContainer[0])){
                                                            $currentTarget = $currentTarget.parent();
                                                        }
                                                        $currentTarget.show();
                                                    });

                                                    //show or hide the list values select
                                                    var elt = $('[class*="property-type"]',$editContainer).parent("div").next("div");
                                                    if (/list$/.test($('[class*="property-type"]',$editContainer).val())) {
                                                        if (elt.css('display') === 'none') {
                                                            elt.show();
                                                            elt.find('select').removeAttr('disabled');
                                                        }
                                                    }
                                                    else if (elt.css('display') !== 'none') {
                                                        elt.css('display', 'none');
                                                        elt.find('select').prop('disabled', "disabled");
                                                    }

                                                }
                                                _toggleModeBtn('enabled');
                                            }
                                        });
                                    }
                                    //hide index form and show property
                                    else{
                                        if($('.index',$editContainer).length > 0){
                                            //hide index properties
                                            $('.index',$editContainer).each(function(){
                                                var $currentTarget = $(this);
                                                while(!_.isEqual($currentTarget.parent()[0], $editContainer[0])){
                                                    $currentTarget = $currentTarget.parent();
                                                }
                                                $currentTarget.hide();
                                            });
                                            $('.index-remover',$editContainer).each(function(){
                                                $(this).parent().hide();
                                            });
                                            //show properties
                                            $('.property',$editContainer).each(function(){
                                                var $currentTarget = $(this);
                                                while(!_.isEqual($currentTarget.parent()[0], $editContainer[0])){
                                                    $currentTarget = $currentTarget.parent();
                                                }
                                                $currentTarget.show();
                                            });

                                            //show or hide the list values select
                                            var elt = $('[class*="property-type"]',$editContainer).parent("div").next("div");
                                            if (/list$/.test($('[class*="property-type"]',$editContainer).val())) {
                                                if (elt.css('display') === 'none') {
                                                    elt.show();
                                                    elt.find('select').removeAttr('disabled');
                                                }
                                            }
                                            else if (elt.css('display') !== 'none') {
                                                elt.css('display', 'none');
                                                elt.find('select').prop('disabled', "disabled");
                                            }

                                        }
                                        _toggleModeBtn('enabled');

                                    }
                                });

                                //on click on index icon show index form or hide it
                                $indexIcon.on('click', function() {
                                    //if form property is simple we can show index form
                                    if($('.property-mode').hasClass('property-mode-advanced')){
                                        //show index form
                                        if(!$editContainer.parent().hasClass('property-edit-container-open') ||
                                            ($editContainer.parent().hasClass('property-edit-container-open') && $($('.index',$editContainer)[0]).is(':visible'))){
                                            $editContainer.slideToggle(function() {
                                                $editContainer.parent().toggleClass('property-edit-container-open');
                                                //hide index form
                                                if(!$('.property-edit-container-open').length) {
                                                    if($('.property',$editContainer).length > 0){
                                                        $('.index',$editContainer).each(function(){
                                                            var $currentTarget = $(this);
                                                            while(!_.isEqual($currentTarget.parent()[0], $editContainer[0])){
                                                                $currentTarget = $currentTarget.parent();
                                                            }
                                                            $currentTarget.hide();
                                                        });
                                                        $('.index-remover',$editContainer).each(function(){
                                                            $(this).parent().hide();
                                                        });
                                                    }
                                                }
                                                //hide property form and show index one
                                                else {
                                                    if($('.property',$editContainer).length > 0){
                                                        $('.property',$editContainer).each(function(){
                                                            var $currentTarget = $(this);
                                                            while(!_.isEqual($currentTarget.parent()[0], $editContainer[0])){
                                                                $currentTarget = $currentTarget.parent();
                                                            }
                                                            $currentTarget.hide();
                                                        });
                                                        $('.index',$editContainer).each(function(){
                                                            var $currentTarget = $(this);
                                                            while(!_.isEqual($currentTarget.parent()[0], $editContainer[0])){
                                                                $currentTarget = $currentTarget.parent();
                                                            }
                                                            $currentTarget.show();
                                                        });
                                                        $('.index-remover',$editContainer).each(function(){
                                                            $(this).parent().show();
                                                        });
                                                    }
                                                    _toggleModeBtn('disabled');
                                                }
                                            });
                                        }
                                        //hide index form
                                        else{
                                            //switch form
                                            if($('.property',$editContainer).length > 0){
                                                $('.property',$editContainer).each(function(){
                                                    var $currentTarget = $(this);
                                                    while(!_.isEqual($currentTarget.parent()[0], $editContainer[0])){
                                                        $currentTarget = $currentTarget.parent();
                                                    }
                                                    $currentTarget.hide();
                                                });
                                                $('.index',$editContainer).each(function(){
                                                    var $currentTarget = $(this);
                                                    while(!_.isEqual($currentTarget.parent()[0], $editContainer[0])){
                                                        $currentTarget = $currentTarget.parent();
                                                    }
                                                    $currentTarget.show();
                                                });
                                                $('.index-remover',$editContainer).each(function(){
                                                    $(this).parent().show();
                                                });
                                                _toggleModeBtn('disabled');
                                            }
                                        }
                                    }
                                });
                                return 'regular-property'
                        }
                    }());
                $property.addClass(!hasAlreadyProperties ? 'property-block-first property-block ' + type : 'property-block ' + type);
                $propertyContainer.append($property);
                hasAlreadyProperties = true;
            });
        }


        /**
         * Make properties look nice
         *
         * @param $properties (optional)
         */
        function init($properties) {
            var $container  = $('.content-block .xhtml_form:first form');

            // case no or empty argument -> find all properties not upgraded yet
            if(!$properties || !$properties.length){
                $properties = $container.children('div[id*="property_"]').not('.property-block');
            }
            if(!$properties.length) {
                return;
            }
            _wrapPropsInContainer($properties);
            _upgradeRadioButtons($container);
            _toggleModeBtn('disabled');
        }


    return {
        /**
         * Initialize post renderer, this can be done multiple times
         */
        init : init,
        getPropertyContainer: getPropertyContainer
    };
});


