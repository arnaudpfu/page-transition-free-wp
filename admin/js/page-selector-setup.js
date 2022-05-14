PageSelectorSetup.prototype = {
    constructor: PageSelectorSetup,
    findPageContainer: function(pageContainer){
        this.adaptDom();
        return this.searchPageContainer(pageContainer);
    },
    adaptDom: function(){
        if(this.iframe.contentWindow.document.head.querySelector('#wppatr-style') !== null){
            this.iframe.contentWindow.document.head.querySelector('#wppatr-style').remove();            
        }
        if(this.iframe.contentWindow.document.body.querySelector('#transition-container') !== null){
            this.iframe.contentWindow.document.body.querySelector('#transition-container').style.pointerEvents = "none";            
        }
        if(this.iframe.contentWindow.document.body !== null){
            this.iframe.contentWindow.document.body.classList.add('wppatr-cancel-transition');            
        }
    },
    searchPageContainer: function(pageContainer){
        var frame = this.iframe.contentWindow.document;
    
        var ancestors = pageContainer === undefined ? undefined : pageContainer;
        frame.addEventListener('click', getPath = (e)=>{
            e.preventDefault();
            if(ancestors === undefined){
                ancestors = e.path.splice(0, e.path.length - 3).map(el => this.getPageSelector(el));
            }else{
                var el = e.target;
                while( !ancestors.includes( this.getPageSelector(el) ) && el.tagName !== 'BODY' && el.tagName !== 'HTML' && el.tagName !== undefined ){
                    el = el.parentNode;
                }
                el = this.getPageSelector(el);
                ancestors = ancestors.slice(ancestors.indexOf(el));
            }
        });
    
        var widthSection = frame.documentElement.clientWidth / 3;
        var heightSection = frame.documentElement.clientHeight / 3;
        var heightAdminBar = frame.querySelector('#wpadminbar') !== null ? frame.querySelector('#wpadminbar').offsetHeight : 0 ;
        var xCoors = [1, widthSection, 2 * widthSection, 2.8 * widthSection];
        var yCoors = [heightAdminBar + 1, heightSection, 2 * heightSection, 2.8 * heightSection];
    
        try {
            for(const x of xCoors){
                for(const y of yCoors){
                    if(frame.elementFromPoint(x, y) !== null && frame.elementFromPoint(x, y) !== undefined){
                        frame.elementFromPoint(x, y).click();
                    }
                }
            }            
        } catch (error) {
            console.log(error);
        }
    
        frame.removeEventListener('click', getPath);
        return ancestors;
    },
    getPageSelector: function(pageContainer){
        var queryPageContainer;
    
        if(pageContainer.hasAttribute('id')){
            queryPageContainer = '#' + pageContainer.id;
        }else if(pageContainer.tagName === "BODY"){
            queryPageContainer = 'body';
        }else if(pageContainer.hasAttribute('class')){
            queryPageContainer = '.' + pageContainer.className.split(' ').join('.');
        }
    
        return queryPageContainer;
    },
    getPageLink: function(){
        var frame = this.iframe;
        var pageLink = undefined;
        var links = frame.contentWindow.document.querySelectorAll('a');
        var i = 0;
    
        while( pageLink === undefined && i < links.length ){
            if( links[i].closest('#wpadminbar') === null && !links[i].getAttribute('href').includes('/wp-admin') && links[i].hasAttribute('href') && links[i].getAttribute('href') !== frame.getAttribute('src') && !links[i].getAttribute('href').includes('#') ){
                pageLink = links[i];
            }
            i++;
        }
        return pageLink;
    },
    isFunction: function(functionToCheck) {
        return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
    },
    checkIsConfigTheme: function(currentThemes){
        var currentTheme = currentThemes[1] === "" ? currentThemes[0] : currentThemes[1];
        var containerSelector = false;

        switch (currentTheme) {
            case 'Divi':
                containerSelector = '#page-container';
                break;
            case 'Astra':
                containerSelector = '#page';
                break;
            case 'Ultra':
                containerSelector = '#pagewrap';
                break;
            case 'Avada':
                containerSelector = '#wrapper';
                break;
            case 'Twenty Twenty-One':
                containerSelector = '#page';
                break;
        }

        return containerSelector;
    }
}

function PageSelectorSetup(currentThemes, callback){
    
    this.callback = callback;
    this.pageContainer = this.checkIsConfigTheme(currentThemes);
    if(this.pageContainer !== false){
        setTimeout(()=>{
            if( this.isFunction(this.callback) ){
                this.callback(this.pageContainer);
            }
        },0);
    }else{
        this.iframe = document.getElementById('wppatr-simulated-website');
        this.pageContainer = undefined;

        this.iframe.addEventListener('load', firstLoad = ()=>{
            this.pageContainer = this.findPageContainer(this.pageContainer);
            this.iframe.removeEventListener('load', firstLoad);

            var pageLink = this.getPageLink();
            pageLink.click();

            this.iframe.addEventListener('load', secondLoad = ()=>{
                this.pageContainer = this.findPageContainer(this.pageContainer);
                this.iframe.removeEventListener('load', secondLoad);

                this.pageContainer = this.pageContainer[0];
                if( this.isFunction(this.callback) ){
                    this.callback(this.pageContainer);
                }
            });
        });        
    }
}
