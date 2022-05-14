BasicBuilder.prototype = {
    init: function(){
        this.tabs();
        this.switcherSetup();
        this.radioSelectorSetup();
        this.saveSetup();
        this.calculatePageSelectorSetup();
        this.checkPageSelector();
    },
    tabs: function(){
    
        var bb = this.bb;

        bb.addEventListener('click', (e)=>{
            if(e.target.closest("[tab-type=button][tab-family][tab-link]") !== null){
                var tabBtn = e.target.closest("[tab-type=button][tab-family][tab-link]");
                var tabBtnsFamily = bb.querySelectorAll('[tab-type=button][tab-family=' + tabBtn.getAttribute('tab-family') + ']');
                var tabFamily = bb.querySelectorAll('[tab-type=tab][tab-family=' + tabBtn.getAttribute('tab-family') + ']');
                var linkedTab = bb.querySelector('[tab-type=tab][tab-family=' + tabBtn.getAttribute('tab-family') + '][tab-link=' + tabBtn.getAttribute('tab-link') + ']');
                
                selectOneAmongOthers(tabBtn, tabBtnsFamily);
                selectOneAmongOthers(linkedTab, tabFamily);
            }
        });
    },
    saveSetup: function(){

        document.addEventListener('keydown', (e)=>{
            if(e.ctrlKey && e.which === 83){
                e.preventDefault();
                this.save();
            }
        });

        var saveBtns = document.querySelectorAll('.save-transition-form-btn');
        for (let i = 0; i < saveBtns.length; i++) {
            const saveBtn = saveBtns[i];
            saveBtn.addEventListener('click', (e)=>{
                e.preventDefault();
                this.save();
            });
        }

    },
    switcherSetup: function(){
        document.addEventListener('click', (e)=>{
            if(e.target.closest('.switch, .switch-power') !== null){
                var switcher = e.target.closest('.switch, .switch-power');

                var input = switcher.querySelector('input');
                if(input.value === "0"){
                    input.value = '1';
                    if(switcher.closest('.element-tl') !== null){
                        switcher.closest('.element-tl').classList.remove('disabled');
                    }
                }else{
                    input.value = '0';
                    if(switcher.closest('.element-tl') !== null){
                        switcher.closest('.element-tl').classList.add('disabled');
                    }
                }
                switcher.querySelector('input').dispatchEvent(new Event('input'));

                if(input.closest('.element-tl') !== null){
                    document.dispatchEvent(new CustomEvent('activElement', {
                        detail:{
                            state: input.value,
                            element: input.closest('[editeur]').getAttribute('editeur')
                        }
                    }));
                }
            }
        });
    },
    radioSelectorSetup: function(){

        var allRadioSelectors = document.querySelectorAll('[radio-selector]');

        for(const allRadioSelector of allRadioSelectors){
            if(allRadioSelector.querySelector('[radio-unit].active') === null){
                allRadioSelector.querySelector('[radio-unit]').classList.add('active');
            }
        }

        window.addEventListener('click', (e)=>{
            if( e.target.closest('[radio-unit]') !== null && e.target.closest('[radio-selector]') !== null ){
                this.selectRadio(e.target.closest('[radio-unit]'));
            }
        });

    },
    selectRadio: function(el){
        var neighbors = el.closest('[radio-selector]').querySelectorAll('[radio-unit]');
        var elData = el.querySelectorAll('input');
        var datas = el.closest('[radio-selector]').querySelectorAll('[radio-data] input');

        selectOneAmongOthers(el, neighbors);
        for (let i = 0; i < datas.length; i++) {
            datas[i].value = elData[i].value;
        }
        
    },
    calculatePageSelectorSetup: function(){
        var pageSelectorSeekerBtn = document.getElementById('calculate-page-selector');
        var searchIsRunning = null;

        pageSelectorSeekerBtn.addEventListener('click', ()=>{
            var seekerPageSelector = document.createElement('iframe');
            seekerPageSelector.id = "wppatr-simulated-website";
            seekerPageSelector.setAttribute('src', WPPATR_Localize_Url.getHomeUrl);
            seekerPageSelector.setAttribute('frameborder', "0");
            seekerPageSelector.style = "position:absolute;top:0;left:0;width:100%;height:100%;z-index:-99999999;pointer-events:none;opacity:0;";
            
            if(searchIsRunning === null){
                searchIsRunning = true;
                document.body.appendChild(seekerPageSelector);
                pageSelectorSeekerBtn.querySelector('span').style.opacity = "0";
                pageSelectorSeekerBtn.innerHTML += "<div class='ld-container'><div class='ld-seeker-selector'><div></div><div></div><div></div><div></div></div></div>";
                var ldContainer = pageSelectorSeekerBtn.querySelector('.ld-container');
                ldContainer.style.opacity = "1";

                new PageSelectorSetup(WPPATR_Localize_Url.activeTheme, (pageSelector)=>{
                    pageSelectorSeekerBtn.parentNode.querySelector('[name="wppatr-page-selector"]').value = pageSelector;
                    seekerPageSelector.remove();
                    ldContainer.style.opacity = "0";
                    pageSelectorSeekerBtn.querySelector('span').style.opacity = "1";
                    searchIsRunning = setTimeout(() => {
                        var pageSelectorInput = document.querySelector('[name="wppatr-page-selector"]');
                        if(pageSelectorInput.hasAttribute('saving-error')){
                            pageSelectorInput.removeAttribute('saving-error');
                        }
                        if(pageSelectorInput.hasAttribute('help-text')){
                            pageSelectorInput.removeAttribute('help-text');
                        }
                        if(pageSelectorInput.classList.contains('component-error')){
                            pageSelectorInput.classList.remove('component-error');
                        }
                        ldContainer.remove();
                        searchIsRunning = null;
                    }, 300);
                });
            }

        });
    },
    checkPageSelector: function(){
        var pageSelectorInput = document.querySelector('[name="wppatr-page-selector"]');
        var pageSelectorSeekerBtn = document.getElementById('calculate-page-selector');
        var checkIsRunning = null;
        var ldContainer;

        
        var oncePageSelectorFilled = debounce(()=>{
            if(checkIsRunning === null){
                checkIsRunning = true;
                var seekerPageSelector = document.createElement('iframe');
                seekerPageSelector.setAttribute('src', WPPATR_Localize_Url.getHomeUrl);
                seekerPageSelector.setAttribute('frameborder', "0");
                seekerPageSelector.style = "position:absolute;top:0;left:0;width:100%;height:100%;z-index:-99999999;pointer-events:none;opacity:0;";
                document.body.appendChild(seekerPageSelector);

                seekerPageSelector.addEventListener('load', onceSeekerLoaded = ()=>{
                    var hasErrors = false;
                    try {
                        seekerPageSelector.contentWindow.document.querySelector( pageSelectorInput.value )
                    } catch (error) {
                        hasErrors = true;
                    }

                    if( !hasErrors && seekerPageSelector.contentWindow.document.querySelector( pageSelectorInput.value ) === null){
                        pageSelectorInput.setAttribute('help-text', 'Your page selector doesn\'t exist on your website.');
                        if( !pageSelectorInput.hasAttribute('saving-error') ){
                            pageSelectorInput.setAttribute('saving-error', '');
                        }
                        pageSelectorInput.classList.add('component-error');
                    }else{
                        if(pageSelectorInput.hasAttribute('saving-error')){
                            pageSelectorInput.removeAttribute('saving-error');
                        }
                        if(pageSelectorInput.hasAttribute('help-text')){
                            pageSelectorInput.removeAttribute('help-text');
                        }
                        if(pageSelectorInput.classList.contains('component-error')){
                            pageSelectorInput.classList.remove('component-error');
                        }
                    }
    
                    seekerPageSelector.remove();
                    ldContainer.style.opacity = "0";
                    pageSelectorSeekerBtn.querySelector('span').style.opacity = "1";
                    ldContainer.remove();
                    checkIsRunning = setTimeout(() => {
                        ldContainer.remove();
                        seekerPageSelector.removeEventListener('load', onceSeekerLoaded);
                        checkIsRunning = null;
                    }, 300);
                });
            }
        }, 1000);

        pageSelectorInput.addEventListener('input', oncePageSelectorFilled);
        
        pageSelectorInput.addEventListener('input', ()=>{
            pageSelectorInput.setAttribute('saving-error', '');
            if(pageSelectorSeekerBtn.querySelector('.ld-container .ld-seeker-selector') === null){
                pageSelectorSeekerBtn.innerHTML += "<div class='ld-container'><div class='ld-seeker-selector'><div></div><div></div><div></div><div></div></div></div>";
                ldContainer = pageSelectorSeekerBtn.querySelector('.ld-container');
                setTimeout(() => {
                    pageSelectorSeekerBtn.querySelector('span').style.opacity = "0";
                    ldContainer.style.opacity = "1";
                }, 0);
            }
        });

    },
    save: function(){

        if(document.querySelector('[saving-error]') === null){

            var options_fromform = jQuery('#form-builder').serialize();

            var ajaxData = new FormData;
            ajaxData.append('action', 'wppatrnonce_save_options');
            ajaxData.append('value', options_fromform);
            ajaxData.append('nonce', document.getElementById('wppatr-nonce').value );

            jQuery.ajax({
                method: "POST",
                url: WPPATR_Localize_Url.ajaxUrl,
                data: ajaxData,
                processData:false,
                contentType:false,
                beforeSend: function ( xhr ){
                    jQuery("#animation-backup").removeAttr('class').fadeIn('fast');
                },
                success: function(response){
                    
                    setTimeout(function(){
                        jQuery("#animation-backup").addClass('success-animation');
                    }, 300);

                    setTimeout(function(){
                        jQuery("#animation-backup").fadeOut();
                    },500);
                }
            });            
        }

    },
}

function BasicBuilder(){
    this.bb = document.getElementById('basic-builder');
    this.init();
}

document.addEventListener('DOMContentLoaded', ()=>{
    new BasicBuilder();
});
