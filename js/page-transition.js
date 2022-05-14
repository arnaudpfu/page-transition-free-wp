Object.deepExtend = function(destination, source) {
    for (var property in source) {
      if (source[property] && source[property].constructor &&
       source[property].constructor === Object) {
        destination[property] = destination[property] || {};
        arguments.callee(destination[property], source[property]);
      } else {
        destination[property] = source[property];
      }
    }
    return destination;
};

PageTransition.prototype = {
    constructor: PageTransition,
    isIE: function() {
        return window.navigator.userAgent.indexOf("MSIE ") > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./);
    },
    triggerIn: function(){
        this.transitionContainer.classList.remove('init');
        this.transitionContainer.classList.add('page-loaded');
        this.page.classList.add('page-loaded');
        document.body.classList.add('page-loaded');
    },
    setRestSetup: function(){
        document.body.classList.remove('scroll-block');
        document.getElementById('loader-setup').classList.add('paused');
        window.dispatchEvent(new Event('resize'));
        this.transitionContainer.style.display = "none";
    },
    isPageSelectorComputer: function(){
        return document.body.classList.contains('wppatr-cancel-transition');
    },
    initOut: function(){
        
        for (let i = 0; i < this.transitionContainerChild.length; i++) {
            if(this.isIE()){
                this.transitionContainerChild[i].removeAttribute('style');
            }else{
                this.transitionContainerChild[i].style.transitionDuration = "0s";
                this.transitionContainerChild[i].style.transitionDelay = "0s";
            }
        }

        this.transitionContainer.style.display = null;
        this.transitionContainer.classList.remove('init-time');
        this.transitionContainer.classList.remove('page-loaded');
        this.page.classList.remove('page-loaded');
        document.body.classList.remove('page-loaded');

        if(this.transition.removeScrollBar === "1"){
            document.body.classList.add('scroll-block');
        }

        this.transitionContainer.classList.add('change-page');
        this.transitionContainer.classList.add('change-page-time');
        this.page.classList.add('change-page');
    },
    triggerOut: function(){
        for (let i = 0; i < this.transitionContainerChild.length; i++) {
            this.transitionContainerChild[i].style.transitionDuration = null;
            this.transitionContainerChild[i].style.transitionDelay = null;
        }
        this.transitionContainer.classList.remove('change-page');
        this.transitionContainer.classList.add('new-page');
    },
    noticeExist: function(){
        return document.getElementById('flint-notice') !== null;
    },
    noticeIsVisible: function(){
        if( !this.noticeExist() ){
            return;
        }
        var notice = document.getElementById('flint-notice');
        var x = window.innerWidth - ( 20 + (notice.getBoundingClientRect().width / 2) );
        var y = 20 + (notice.getBoundingClientRect().height / 2);
        return notice.contains( document.elementFromPoint(x, y) );
    },
    createNotice: function(){
        if( this.noticeExist() ){
            return;
        }
        var notice = document.createElement('div');
        notice.id = 'flint-notice';
        notice.style = 'display:block !important;opacity:1 !important;position:fixed !important;bottom:20px !important;right:20px !important;z-index:999999999999999999999999999999999999999999999999999999999999 !important;';
        notice.innerHTML = 'Transition created by <a href="https://fluent-interface.com/">Fluent Interface</a>';
        document.querySelector('html').appendChild(notice);
        this.notice = notice;
    },
    moveNotice:function(){
        if( !this.noticeExist ){
            return;
        }
        var notice = document.getElementById('flint-notice');
        document.querySelector('html').appendChild(notice);
    },
    loadNextPage: function(url){

        setTimeout(function(){
            window.location = url;
            document.getElementById('loader-setup').classList.remove('paused');
        },this.transition.outDuration);
    },
    init: function(){

        var obj = this;
        var transition = obj.transition;

        setTimeout(function(){

            var hideNoticeTimeout = null;
            obj.noticeExist() ? obj.moveNotice() : obj.createNotice();

            if( !obj.noticeIsVisible() ){
                return;
            }

            obj.triggerIn();

            setTimeout( function(){
                obj.setRestSetup();
                hideNoticeTimeout = setTimeout(function(){
                    obj.notice.style.setProperty('display', 'none', 'important');
                },2000);
            },transition.inDuration);
    
            if( ! obj.isPageSelectorComputer() ){
                transition.links = transition.links === "" ? "a" : transition.links;
                var selector = (transition.notLinks === "") ? transition.links : transition.links + ':not(' + transition.notLinks + ')';
                var links = document.querySelectorAll(selector);
        
                for(let i=0;i<links.length;i++){
                    const link = links[i];
                    link.addEventListener('click', function(e){

                        clearTimeout(hideNoticeTimeout);
                        obj.notice.style.setProperty('display', 'block', 'important');

                        obj.noticeExist() ? obj.moveNotice() : obj.createNotice();

                        if( !obj.noticeIsVisible() ){
                            return;
                        }
                        
                        var url = link.getAttribute("href");
                        if( url.indexOf('#') !== -1 ) {
                            return;
                        }
                        e.preventDefault();
        
                        obj.initOut();
                        
                        setTimeout(function(){
                            obj.triggerOut();
                            obj.loadNextPage(url);
                        },0);
                    });
                }
            }
        },0);
    },
}

function PageTransition(params){
  
    this.transition = {
        pageSelector : 'body',
        inDuration : 200,
        outDuration : 200,
        links : 'a',
        notLinks : '',
        removeScrollBar: '0',
    };
    this.page = null;
    this.transitionContainer = null;
    this.transitionContainerChild = null;
    this.notice = document.getElementById('flint-notice');
  
    if(params){
        Object.deepExtend(this.transition, params);
    }

    this.page = document.querySelector(this.transition.pageSelector) === null ? document.body : document.querySelector(this.transition.pageSelector) ;
    this.transitionContainer = document.getElementById('transition-container');

    if(this.transitionContainer === null){
        return;
    }

    this.transitionContainerChild = this.transitionContainer.querySelectorAll('*');

    this.init();
};