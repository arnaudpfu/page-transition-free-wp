if(Transition.transition !== null && Transition.active === '1'){
    var TransitionDurations = new TransitionDuration(Transition);

    new PageTransition({
        inDuration : TransitionDurations.getInDuration(),
        outDuration : TransitionDurations.getOutDuration(),
        pageSelector : Transition.pageContainer,
        links: Transition.links,
        notLinks: Transition.notLinks,
        removeScrollBar: Transition.removeScrollBar,
    });    
}else{
    document.querySelector('.scroll-block').classList.remove('scroll-block');
}