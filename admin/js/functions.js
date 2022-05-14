/**
 * 
 * @returns an array without an element
 */
Array.prototype.remove = function () {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

/* for IE */
if (!('remove' in Element.prototype)) {
    Element.prototype.remove = function () {
        if (this.parentNode) {
            this.parentNode.removeChild(this);
        }
    };
}

function regexIndexOf(string, regex, startpos) {
    var indexOf = string.substring(startpos || 0).search(regex);
    return (indexOf >= 0) ? (indexOf + (startpos || 0)) : indexOf;
}

function isFunction(functionToCheck) {
    return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
}

Node.prototype.appear = function(anim = "fade",appearTime){

    if(anim.includes("slide")){
        var heightContainer = this.offsetHeight;
        this.style.height = '0';
    }
    if(anim.includes("fade")){
        this.style.opacity = '0';
    }
    if(anim.includes("popY")){
        this.style.transform = 'scaleY(0.6)';
    }
    if(anim.includes("pop")){
        this.style.transform = 'scale(0.6)';
    }

    setTimeout(()=>{
        if(anim.includes("slide")){
            this.style.height = heightContainer + 'px';
        }
        if(anim.includes("fade")){
            this.style.opacity = '1';
        }
        if(anim.includes("popY")){
            this.style.transform = 'scaleY(1)';
        }
        setTimeout(()=>{
            if(anim.includes("slide")){
                this.style.height = null;
            }
            if(anim.includes("fade")){
                this.style.opacity = null;
            }
            if(anim.includes("popY")){
                this.style.transform = null;
            }
            if(anim.includes("pop")){
                this.style.transform = null;
            }
            this.setAttribute('appear-anim', anim);
        },appearTime);
    },0);
}

Node.prototype.disappear = function(callback, disappearTime){

    let anim = this.getAttribute('appear-anim');
    if(anim.includes("fade")){
        this.style.opacity = '1';
    }
    if(anim.includes("slide")){
        this.style.height = this.offsetHeight + 'px';
    }
    if(anim.includes("popY")){
        this.style.transform = 'scaleY(1)';
    }
    if(anim.includes("pop")){
        this.style.transform = 'scale(1)';
    }
    setTimeout(()=>{
        if(anim.includes("fade")){
            this.style.opacity = '0';
        }
        if(anim.includes("slide")){
            this.style.height = '0';
        }
        if(anim.includes("popY")){
            this.style.transform = 'scaleY(0.6)';
        }
        if(anim.includes("pop")){
            this.style.transform = 'scale(0.6)';
        }
        setTimeout(()=>{
            if(anim.includes("fade")){
                this.style.opacity = null;
            }
            if(anim.includes("slide")){
                this.style.height = null;
            }
            if(anim.includes("popY")){
                this.style.transform = null;
            }
            if(anim.includes("pop")){
                this.style.transform = null;
            }
            this.removeAttribute('appear-anim');
            callback();
        },disappearTime);
    });
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

/**
 * 
 * @param {string} motif 
 * @returns le nombre d'apparition du motif
 */
String.prototype.nbOccu = function (motif) {
    return this.split(motif).length - 1;
}

function debounce(callback, delay) {
    var timer = null;
    return function(){
        clearTimeout(timer);
        timer = setTimeout(function(){
            callback();
        }, delay);
    }
}

function numberWithSpaces(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

var decodeEntities = (function() {
    // this prevents any overhead from creating the object each time
    var element = document.createElement('div');

    function decodeHTMLEntities (str) {
    if(str && typeof str === 'string') {
        // strip script/html tags
        str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
        str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
        element.innerHTML = str;
        str = element.textContent;
        element.textContent = '';
    }

    return str;
    }

    return decodeHTMLEntities;
})();

/**
 * 
 * @param {float} number 
 * @param {flaot} precision 
 * @returns the number arounded
 */
function aroundInt(number, precision) {
    return Math.round(number * (1 / precision)) / (1 / precision);
}

/**
 * 
 * @param {string} orig 
 * @returns orig en hexadecimal
 */
/* function rgba2hex(orig) {
    var a,
        rgb = orig.replace(/\s/g, '').match(/^rgba?\((\d+),(\d+),(\d+),?([^,\s)]+)?/i),
        alpha = (rgb && rgb[4] || "").trim(),
        hex = rgb ?
            (rgb[1] | 1 << 8).toString(16).slice(1) +
            (rgb[2] | 1 << 8).toString(16).slice(1) +
            (rgb[3] | 1 << 8).toString(16).slice(1) : orig;

    if (alpha !== "") {
        a = alpha;
    } else {
        a = 01;
    }
    // multiply before convert to HEX
    a = (Math.round((a * 255)) | 1 << 8).toString(16).slice(1);
    a = (a === 'ff') ? '' : a;
    hex = hex + a;

    return hex;
} */

/**
 * select an element among a nodeList
 * @param {node} elem wich will selected by adding the 'active' class
 * @param {nodeList} listElem
 */
function selectOneAmongOthers(elem, listElem, selectClass = "active") {
    listElem.forEach(el => el.classList.remove(selectClass));
    if(Array.isArray(elem)){
        for (let i = 0; i < elem.length; i++) {
            if (elem[i] !== undefined) { elem[i].classList.add(selectClass) };
        }
    }else{
        if (elem !== undefined) { elem.classList.add(selectClass) };
    }
}

/**
 * retrieve the index of the nth occurrence of a substr in a str
 * @param {string} string 
 * @param {string} subString 
 * @param {integer} index 
 * @returns 
 */
function getPosition(string, subString, index) {
    return string.split(subString, index).join(subString).length;
  }

/**
* ajoute des elements a un container
* @param {nodeList} elements 
* @param {node} box 
*/
function elementsToBox(elements, box) {
    for (let i = 0; i < elements.length; i++) {
        box.appendChild(elements[i]);
    }
}

function zoomCadre(parentContainer) {
    var positionCursor = parentContainer.getAttribute('position');
    var maxPosition = parentContainer.querySelector('.input-range').getAttribute('max');
    var cadreScreen = document.getElementById('cadre-screen');

    cadreScreen.style.transform = 'scale(' + positionCursor * maxPosition / '100' + ')';
}

/**
 * check if a str is numeric
 * @param {string} n 
 * @returns {boolean}
 */
function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

String.prototype.replaceArray = function(find, replace) {
    var replaceString = String(this);
    for (var i = 0; i < find.length; i++) {
        while(replaceString.includes(find[i])){
            replaceString = replaceString.replace(find[i], replace);
        }
    }
    return replaceString;
};

function slideUp(elem, transitionDuration){

    var height = elem.clientHeight + parseInt(window.getComputedStyle(elem).getPropertyValue('border-top-width'), 10) + parseInt(window.getComputedStyle(elem).getPropertyValue('border-bottom-width'), 10) + 'px';

    elem.style.overflow = 'hidden';
    elem.style.height = height;
    elem.style.transitionDuration = transitionDuration + "ms";

    setTimeout(function(){
        elem.style.height = '0px';

        setTimeout(() => {
            elem.classList.remove('slide-open');
            elem.style.overflow = null;
            elem.style.display = 'none';
            elem.style.transitionDuration = null;
            elem.style.height = null;
        }, transitionDuration);
    },0);

}

function slideDown(elem, transitionDuration){

    elem.classList.add('slide-open');
    elem.style.display = 'block';
    elem.style.height = 'auto';
    elem.style.overflow = 'hidden';
    elem.style.transitionDuration = transitionDuration + "ms";

    var height = elem.clientHeight + parseInt(window.getComputedStyle(elem).getPropertyValue('border-top-width'), 10) + parseInt(window.getComputedStyle(elem).getPropertyValue('border-bottom-width'), 10) + 'px';

    elem.style.height = '0px';

    setTimeout(function () {
        elem.style.height = height;
        setTimeout(() => {
            elem.style.overflow = null;
            elem.style.transitionDuration = null;
            elem.style.height = null;
        }, transitionDuration);
    }, 0);
}

function slideToggle(elem, transitionDuration){

    if (!elem.classList.contains('slide-open')) {
        slideDown(elem, transitionDuration);      
    } else {
        slideUp(elem, transitionDuration);
    }
}