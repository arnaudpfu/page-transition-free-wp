TransitionDuration.prototype = {
    constructor: TransitionDuration,
    getPropertyTimeValue: function(css, property){
        var time = false;
    
        if(css.indexOf(property) !== -1){
            var trDelay = css.slice(css.indexOf(property + ':') + (property + ':').length );
            trDelay = trDelay.slice(0, trDelay.indexOf('ms;'));
            time = parseInt(trDelay, 10);
        }
        if(time === false){
            if(property === "transition-duration"){
                time = 1000;
            }else{
                time = 0;
            }
        }
        return time;
    },
    getTransitionDuration: function(css){
    
        css = this.convertToMilliSecond(css);
        var layersCss = css.split(/\#[a-zA-Z]+\{/);
        layersCss = layersCss.filter( function(el){ return el !== "" } );
        var durations = [];
    
        for (let i = 0; i < layersCss.length; i++) {
            var duration = 0;
            duration += this.getPropertyTimeValue(layersCss[i], 'transition-duration');
            duration += this.getPropertyTimeValue(layersCss[i], 'transition-delay');
            durations.push(duration);
        }
    
        return Math.max.apply(null, durations);
    },
    convertToMilliSecond: function(css){
        var properties = css.split(';');
    
        for (let i = 0; i < properties.length; i++) {
            const property = properties[i];
            if(property.indexOf(':') !== -1 && property.split(':').length === 2){
                var propertyName = property.split(':')[0];
                var propertyValue = property.split(':')[1];
                if(propertyValue.match(/[0-9]\s*s\s*$/) !== null){
                    var numericValue = propertyValue.replace('s', '').trim();
                    numericValue = numericValue * 100;
                    propertyValue = numericValue + "ms";
                    properties[i] = propertyName + ":" + propertyValue;
                }
            }
        }
    
        return properties.join(';');
    },
    getInDuration: function(){
        if(this.transitionData !== null ){
            var inStyle = '';
            var intTransition = this.transitionData.transition ;

            if( intTransition !== null && intTransition.overlay !== "" && intTransition.overlay !== false ){
                inStyle += JSON.parse(intTransition.overlay).css.in.time;
            }
            if( intTransition !== null && intTransition.page !== "" && intTransition.page !== false ){
                inStyle += '#page{' + JSON.parse(intTransition.page).in;
            }
            return this.getTransitionDuration(inStyle);            
        }
    },
    getOutDuration: function(){
        if(this.transitionData !== null){
            var outStyle = '';
            var outTransition = this.transitionData.transition ;

            if(outTransition !== null){
                if( outTransition !== null && outTransition.overlay !== "" && outTransition.overlay !== false ){
                    outStyle += JSON.parse(outTransition.overlay).css.out.time;
                }
                if( outTransition !== null && outTransition.overlay !== "" && outTransition.page !== false ){
                    outStyle += '#page{' + JSON.parse(outTransition.page).out;
                }
                return this.getTransitionDuration(outStyle);
            }

        }
    },
}

function TransitionDuration(transition){
    this.transitionData = transition;
}