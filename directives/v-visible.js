Vue.directive( 'visible', ( el, binding ) => {
    let value = binding.value;
    if( !!value ) {
        el.style.visibility = 'visible';
    } else {
        el.style.visibility = 'hidden';
    }
});